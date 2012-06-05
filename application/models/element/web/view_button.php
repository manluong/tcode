<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class View_button extends CI_Model {
	var $url = array();

	function __construct() {
		parent::__construct();

		//temporary fix for missing ID
		$CI =& get_instance();
		$this->url = $CI->url;
	}

	function output_button_format($element_button,$dgroup_submit='',$button_save_nojs='',$button_savecancel_js='',$saveid=0){
		//html_show_array($element_button);

		$previous_position = $alignrightar = $previous_align = $alignrightac = $listid = '';

		if (isset($element_button['listid'])) $listid = $element_button['listid'];

		if ($element_button['buttons']) {
			foreach ($element_button['buttons'] as $this_button) {
				$this_iconclass = '';

				if ($this_button['position'] == 'rowone') {
	            	$result['rowone'] = output_button_formatjs($this_button,$this->thisid['encode'],$saveid);
					continue;
	            }

				if (($previous_position != $this_button['position'] && $alignrightar)
					|| ($previous_align != $this_button['align'] && $alignrightar)) {
						$result[$previous_position] .= '</span>';
						$alignrightar = 0;
				} elseif (($previous_position != $this_button['position'] && $alignrightac)
					|| ($previous_align != $this_button['align'] && $alignrightac)) {
						$result[$previous_position] .= '</div>';
						$alignrightac = 0;
				}

				if (!isset($result[$this_button['position']])) $result[$this_button['position']] = '';

				if ($this_button['align'] == '1' && !$alignrightar) {
					$result[$this_button['position']] .= '<span class="fr">';
					$alignrightar = 1;
				} elseif ($this_button['align'] == '3' && !$alignrightac) {
					$result[$this_button['position']] .= '<div style="position: absolute; width: 100%; text-align: center;">';
					$alignrightac = 1;
				}

				$previous_position = $this_button['position'];
				$previous_align = $this_button['align'];

				//add icon class to button
				if (preg_match('/UIXX/', $this_button['lang'])){
					//2 icon = UIXXplusXXplusXX
					//1 icon = UIXXplusXXXX
					//1 right icon = UIXXXXplusXX
					$this_langarray = explode('XX', $this_button['lang'],4); //print_r($this_iconclass);
					$this_button['lang'] = $this_langarray[3];
					if ($this_langarray[1]) $this_iconclass = ' <i class="icon-'.$this_langarray[1].'"></i> ';
					if ($this_langarray[2]) $this_iconclass .= ' <i class="icon-'.$this_langarray[2].'"></i> ';
				}

				if ((isset($this_button['icononly']) && $this_button['icononly']) || $this_button['lang'] == 'xx') {
					$this_button['lang'] = '';
				}

				if (isset($this_button['lang__d']) && $this_button['lang__d']) $result[$this_button['position']] .= '<font class="bu__d">'.$this_button['lang__d'].'</font>';

				if ($this_button['type'] == 'listtitle') {
					$result[$this_button['position']] .= '<span style="padding-left:10px; padding-right:10px; font-weight: bold;">'.$this_button['lang'].'</span>';
				} elseif ($button_save_nojs && ($this_button['type'] == 'as' || $this_button['type'] == 'es' || $this_button['type'] == 'ss' || $this_button['type'] == 'fs')){
					$result[$this_button['position']] .= '<button type="submit" class="btn">'.$this_iconclass.$this_button['lang'].'</button>';
					//} elseif ($dgroup_submit && ($this_button['type'] == "as" || $this_button['type'] == "es" || $this_button['type'] == "ss" || $this_button['type'] == "fs")){
					//$result[$this_button['position']] .= '<button type="submit" class="button"'.$this_iconclass.' onclick="'.output_button_formatjs($this_button,$thisid_en,0,1,$this_element_id).'">'.$this_button['lang'].'</button>';
				} else {
					$result[$this_button['position']] .= '<button type="button" class="btn" onclick="'.$button_savecancel_js.''.$this->output_button_formatjs($this_button,$this->url['id_encrypted'],0,$listid).'">'.$this_iconclass.$this_button['lang'].'</button>';
				}

				//$button[$countbutton]['targeturl']
				//<a href="#" class="button1" onclick="'.core_element_button_formatjs($this_button,$thisid_en).'">'.$this_button['lang'].'</a>';
				//if ($this_button['lang__d']) $result[$this_button['position']] .= '';
			}
		}

		if ($alignrightar){
			$result[$previous_position] .= '</span>';
			$alignrightar = 0;
		} elseif ($alignrightac) {
			$result[$previous_position] .= '</div>';
			$alignrightac = 0;
		}

		if (isset($result)) return ($result);
	}


	function output_button_formatrowend($button, $targetid) {
		//html_show_array($element_button);
		//add icon class to button
		$count = 0;
		$result = '';
		foreach ($button as $this_button) {
			$this_iconclass = $targetvalue = '';

			if (preg_match('/UIXX/', $this_button['lang'])){
				//2 icon = UIXXplusXXplusXX
				//1 icon = UIXXplusXXXX
				//1 right icon = UIXXXXplusXX
				$this_langarray = explode("XX", $this_button['lang'],4); //print_r($this_iconclass);
				$this_button['lang'] = $this_langarray[3];
				if ($this_langarray[1]) $this_iconclass = ' data-icon-primary="ui-icon-'.$this_langarray[1].'"';
				if ($this_langarray[2]) $this_iconclass .= ' data-icon-secondary="ui-icon-'.$this_langarray[2].'"';
			}

			if ((isset($this_button['icononly']) && $this_button['icononly']) || $this_button['lang'] == 'xx') {
				$this_icon_only = ' data-icon-only="true"';
				$this_button['lang'] = '';
			}

			if ($this_button['lang__d']) $result[$this_button['position']] .= '<font class="bu__d">'.$this_button['lang__d'].'</font>';

			if (isset($targetid[$count]['targetvalue'])) $targetvalue = $targetid[$count]['targetvalue'];


			$result .= '<button type="button" class="btn"'.$this_iconclass.' onclick="'.$this->output_button_formatjs($this_button,0,$targetid[$count]['targetid'],0,$targetvalue).'">'.$this_button['lang'].'</button>';

			$count++;
		}

		return $result;
	}

	function output_button_formatjs($this_button,$thisid_en,$targetid=0,$listid=0,$targetvalue=""){
		/*
		$this_button['position']
		$this_button['type']
		$this_button['lang']
		$this_button['lang__d']
		$this_button['targetapp']
		$this_button['targetan']
		$this_button['targetaved']
		$this_button['targetid']
		$this_button['div']
		$element_button['targeturl']
		*/

		if ($targetid) {
			$thisid_format = $targetid;
		} elseif ($this_button['targetid'] == 'thisid') {
			$thisid_format = $this->url['id_encrypted'];
		} elseif ($this_button['targetid'] == 'listid') {
			$thisid_format = $listid;
		} elseif (substr($this_button['targetid'],0,5) == 'value') {
			$thisid_format = 'XX'.$this_button['targetid'].'XXvend';
			$thisid_isjs = 1;
		} elseif (substr($this_button['targetid'],0,5) == 'FIELD') {
			//$thisid_format = $this_button['targetid']."XX";
			$thisid_format = $this_button['thisid'];
			$thisid_isjs = 0;
		} elseif ($this_button['targetid'] == 'parentid') {
			$thisid_format = $this_button['parentid'];
			$thisid_isjs = 0;
		} elseif ($this_button['targetid'] == 'noid') {
			$thisid_format = 0;
		}

		/*
		elseif ($this_button['targetid'] == "rid") {
		$thisid_format = 'XXrid';
		$thisid_isrid = 1;
		}elseif ($this_button['targetid'] == "saveid") {
		$thisid_format = $saveid;
		$thisid_saveid = 1;
		}
		*/

		if ($this_button['type'] == 'js') {
			$result = $this_button['targeturl'];
		} elseif ($this_button['type'] == 'url') {
			if ($this_button['div'] == "page"){
				$result = "apps_action_pageload('".$this_button['targeturl']."',0);";
			} else {
				$result = "apps_action_pageload('".$this_button['targeturl']."','".$this_button['div']."');";
			}

		} elseif ($this_button['div'] == 'page') {
			$thisjsline = '/'.$this_button['targetapp'];
			if ($this_button['targetan']) {
				$thisjsline .= '/'.$this_button['targetan'];
				if ($thisid_format) {
					$thisjsline .= '/'.$thisid_format;
					if ($this_button['targetaved']) $thisjsline .= '/'.$this_button['targetaved'];
				}
			}

			if (isset($this_button['targetvalue']) && $this_button['targetvalue']) $thisjsline .= '/'.$targetvalue;

			$result = "apps_action_pageload('".$thisjsline."');";

		} else {
			$this_targetvalue = '';

			if (isset($this_button['targetvalue'])) $this_targetvalue = ",'$targetvalue'";

			//if ($dgroup_submit){
			//$result = "dgroup_submit('".$this_button['targetapp']."','".$this_button['targetan']."','".$this_button['targetaved']."','".$this_button['div']."','".$thisid_format."".$this_targetvalue."','".$morevalue_new."','".$this_element_id."');";
			//}else
			if (isset($thisid_isrid) || isset($thisid_saveid)) {
				$result = "apps_action_ajax('".$this_button['targetapp']."','".$this_button['targetan']."','".$this_button['targetaved']."','".$this_button['div']."','".$thisid_format."".$this_targetvalue."','');";
			} elseif (isset($thisid_isjs)) {
				$result = "apps_action_ajax('".$this_button['targetapp']."','".$this_button['targetan']."','".$this_button['targetaved']."','".$this_button['div']."',".$thisid_format."".$this_targetvalue.",'');";
			} elseif (isset($thisid_format)) {
				$result = "apps_action_ajax('".$this_button['targetapp']."','".$this_button['targetan']."','".$this_button['targetaved']."','".$this_button['div']."','".$thisid_format."'".$this_targetvalue.",'');";
			} else {
				$result = "apps_action_ajax('".$this_button['targetapp']."','".$this_button['targetan']."','".$this_button['targetaved']."','".$this_button['div']."',''".$this_targetvalue.",'');";
			}
		}

		return $result;
	}

}