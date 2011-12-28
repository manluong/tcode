<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class View_dgroup extends CI_model {

	function __construct() {
		parent::__construct();
	}

	function output_dgroup($this_output,$this_element){
		
		$aved = $this->url['subaction'];
		
		//$element['dgrouptype'];
		//$element['data'];
		
		//$element['form'];
		//$element['formtype'];
		//$element['list'];
		//html_show_array($this_output);exit;
		
		$dgroup_submit = 0;
		$button_save_nojs = 0;
		
		switch ($this_output['data']['dgrouptype']){
			
			case 'list':
				
				foreach ($this_output['data']['title'] as $thistitle){
				$this_addon['json']['list']['aoColumns'][]['sTitle'] = $thistitle;
				}
				
				if ($this_output['data']['button']['rowend']){
									
					$this_addon['json']['list']['aoColumns'][]['sTitle'] = "";
	
					foreach (array_keys($this_output['data']['table']) as $this_tableid){
						
						$this_rowend_bu = '<div class="ar bu-div">';
						$this_rowend_bu .= $this->View_button->output_button_formatrowend($this_output['data']['button']['rowend'],$this_output['data']['table_button'][$this_tableid]);
						$this_rowend_bu .= '</div>';
						$this_output['data']['table'][$this_tableid][] = $this_rowend_bu;
					
					}
					
				}
				
				$this_addon['json']['list']['aaData'] = $this_output['data']['table'];
				if ($this_output['data']['listnotitle']) $this_addon['json']['list']['listnotitle'] = 1; 
				
			break;
			
			
			case 'view':
				$this_addon['json']['form'] = $this->output_dgroup_view($this_output);
			break;
				
				
			case 'form':
				$output_dgroup_form = $this->output_dgroup_form($this_output, $aved);
				$this_addon['json']['form'] = $output_dgroup_form['form'];
				$this_addon['json']['formsetup'] = $output_dgroup_form['formsetup'];			
				$dgroup_submit = 1;
				$button_save_nojs = 1;
				
				foreach ($this_output['data']['element_button']['buttons'] as $this_button) { 
					if ($this_button['type'] == "as" || $this_button['type'] == "es"){
						$this_addon['json']['dgroup_savejs']['app'] = $this_button['targetapp'];
						$this_addon['json']['dgroup_savejs']['an'] = $this_button['targetan'];
						$this_addon['json']['dgroup_savejs']['aved'] = $this_button['targetaved'];
						$this_addon['json']['dgroup_savejs']['thisid'] = $this->url['id_encrypted'];
						$this_addon['json']['dgroup_savejs']['div'] = $this_button['div'];
						$this_addon['json']['dgroup_savejs']['element_id'] = $this_element['element_id'];
						$this_addon['json']['dgroup_savejs']['base_url'] = base_url();
					}	
				}
				
			break;
					
			case 'save':
				$this_addon['json']['form'] = $this_output['data'];
				$this_addon['json']['savebutton'] = $this_output['element_button'];
			break;				
				
		}
	
		$this_addon['json']['element_id'] = $this_element['element_id'];
		$this_addon['json']['element_type'] = $this_element['type'];
		$this_addon['json']['dgrouptype'] = $this_output['data']['dgrouptype'];
		
		if (isset($this_output['data']['element_button'])){
		$this_addon['json']['element_button_format'] = $this->View_button->output_button_format($this_output['data']['element_button'],$dgroup_submit,$button_save_nojs);
		}elseif ($this_output['element_button']){
		//$this_addon['json']['element_button_format'] = $this->View_button->output_button_format($this_output['element_button'],$dgroup_submit,$button_save_nojs);
		}
		//print_r($this_addon['json']['element_button_format']);exit;
		//html_show_array($this_addon['json']);exit;
		//print_r($this_addon['json']);exit;
		$this_addon['json'] = json_encode($this_addon['json']);

	return($this_addon);
	}


	function output_dgroup_view($this_output){
	
		$jsonfield = array();
		$count = 0;
	    foreach ($this_output['data']['form']['field'] as $this_field){
	    		
			$jsonfield[$count] = array();
			$jsonfield[$count]['value'] = "";
			
	        if (isset($this_field['imgvalue'])) $this_field['value'] = '<img class="img_'.$this_field['name'].'" src="'.$this_field['imgvalue_src'].'" />';
	           
			if (!$this_field['fvnolabel']) {
				$jsonfield[$count]['fieldname'] = $this_field['name'];
				$jsonfield[$count]['label'] = $this_field['title']; 
			}
			
			if (!$this_field['fvnobr']['this']) {
				$jsonfield[$count]['value'] .= $this_field['value'];
				$count++;
			} else {
				$jsonfield[$count]['value'] .= $this_field['fvnobr']['pretxt'].$this_field['value'].$this_field['fvnobr']['subtxt'];
			}  
		}
		
	    $this_addon['field'] = $jsonfield;
		
		if (!isset($this_output['data']['form']['fvnolabel'])){
		$this_addon['allnolabel'] = "";	
		} else {
		$this_addon['allnolabel'] = $this_output['data']['form']['fvnolabel'];	
		}
		
			  	
	return($this_addon);
	}
	
	
	
	
	function output_dgroup_form($this_output, $aved){
		
	    $forajax_fieldname = array();
	    $forajax_count = 0;
	    $previous_fnobr = 0;
		$field_count = 0;
		$html = "";
		
		foreach ($this_output['data']['form']['field'] as $this_field){
	    //foreach (array_keys($formdata['fieldsort']) as $this_fieldname){
	
	        //$this_field = $formdata['table'][$formdata['fieldsort'][$this_fieldname]['tablenum']]['fields'][$formdata['fieldsort'][$this_fieldname]['fieldnum']];
			$this_fieldname = $this_field['core_db_fields_name'];
			
	        if (!$this_field['hide']){
	
	        $this_field['core_db_fields_name'] = $this_fieldname;
	
	        //$this_field['fnobr_pre']
	        //$this_field['fnobr'])
			//$this_field['fnobr_stop'] 
			
	        //foreach ($this_table['fields'] as $this_field) {
	        //core_db_fields_name
	        
	        $formsetup[$field_count]['field'] = $this_fieldname;
			
	        switch($this_field['form_fieldtype']){
	
	        //input
	        case "1":
	        $html .= $this->f_layout_form_edit_input($this_field,$this_field['value'],$aved);
	        ; break;
	
	        //select
	        case "2":
	        //sellist,selmulti,seldefault,selblank
	        //$this_value = f_layout_form_select_view($this_field['sellist'],$formvalue[$this_field['core_db_fields_name']]);
			$html .= $this->f_layout_form_edit_select($this_field,$this_field['value'],$aved);
	
	        if (isset($this_field['selbutton'])) {
	        //special get value - form_radio_getvalue
	        $formsetup[$field_count]['valuebyradio'] = '1';
	        
	        } elseif ($this_field['autocomplete']) {
	        	
			//auto complete with the select list
			$formsetup[$field_count]['set'] = 'combobox';
	        
			} else {
	        	
	        //normal select list
			$formsetup[$field_count]['set'] = 'uniform';
	        }
	
	        $forajax_fieldchange[$this_fieldname] = $this_field['feselonchgjs'];//<<<<<< what is this
	        ; break;
	
	
	        //checkbox
	        case "3":
	        $html .= $this->f_layout_form_edit_chkbox($this_field,$this_field['value'],$aved);
			$formsetup[$field_count]['set'] = 'uniform';
			$formsetup[$field_count]['valuebycheck'] = '1';
			//special get value - checked
			
	        ; break;
	
	
	        //textarea
	        case "4":
	        $html .= $this->f_layout_form_edit_textarea($this_field,$this_field['value'],$aved);
	        ; break;
	
	        //radio
	        case "5":
			$formsetup[$field_count]['set'] = 'combo';
	        ; break;
	
	        //date
	        case "6":
	        $html .= $this->f_layout_form_edit_date($this_field,$this_field['value'],$aved);
			$formsetup[$field_count]['set'] = 'date';
	        ; break;
	
	        //datetime
	        case "18":
	        $html .= $this->f_layout_form_edit_datetime($this_field,$this_field['value'],$aved);
			$formsetup[$field_count]['set'] = 'datetime';		
	        ; break;
	
	        //time
	        case "12":
	        $html .= $this->f_layout_form_edit_time($this_field,$this_field['value'],$aved);
			$formsetup[$field_count]['set'] = 'time';
	        ; break;
	
	        //password
	        case "7":
	        $html .= $this->f_layout_form_edit_password($this_field,"",$aved);
	        ; break;
	
	        //email
	        case "8":
	        $html .= $this->f_layout_form_edit_input_emailnumurl($this_field,$this_field['value'],$aved,0,"email");
	        ; break;
	
	        //num
	        case "9":
	        $html .= $this->f_layout_form_edit_input_emailnumurl($this_field,$this_field['value'],$aved,0,"number");
	        ; break;
	
	        //url
	        case "10":
	        $html .= $this->f_layout_form_edit_input_emailnumurl($this_field,$this_field['value'],$aved,0,"url");
	        ; break;
	
	        //phone number
	        case "13":
	        $html .= $this->f_layout_form_edit_input_phone($this_field,$this_field['value'],$aved,1);
	        ; break;
	
	        //form hidden
	        case "19":
	        $html .= $this->f_layout_form_edit_input_hidden($this_field,$this_field['value'],$aved);
	        ; break;
	
	        }
	
			if ($this_field['autocomplete'] && $this_field['form_fieldtype'] != "2"){
				$formsetup[$field_count]['autocomplete'] = $this_field['autocomplete'];	
			}
	
	
	    }
	
		$field_count++;
	    }	
	
	$result['form'] = $html;
	$result['formsetup'] = $formsetup;
	
	return($result);
	}
	
	
	function f_layout_form_edit_fieldformat($field,$value,$aved,$inputtype,$inputwidth=0,$nolablefor=0){
	
	        //db_length,db_defaultvalue,form_required,form_validate
	        //form_edit,form_del
			$forminput = "";
			$forminput_class = "";
			$lableextra = "";
			$this_nolablefor = "";
			$fieldformat = array(
				'input' => '',
				'label' => '',
				'input' => '',
				'input_end' => '',
				'start' => '',
				'end' => '',
				'value' => ''
			);
			
	        if ($field['form_required']) { $lableextra .= " <em>*</em>"; $forminput .= ' required="required"'; }
	        if ($field['form_name_lang__d']) $lableextra .= ' <small>'.$field['form_name_lang__d'].'</small>';
	        if ($field['charcount']) $lableextra .= ' <small><span class="counter"></span></small>';
	
	        if ($field['db_length']) $forminput .= ' maxlength="'.$field['db_length'].'"';
	        if ($field['form_validmin']) $forminput .= ' minlength="'.$field['form_validmin'].'"';
	        if ($field['form_validpattern']) $forminput .= ' pattern="'.$field['form_validpattern'].'"';
	        //if ($field['form_matchfield']) $forminput .= ' data-equals="'.$field['form_matchfield'].'"';
	
	        if ($inputwidth) {
	            $forminput.=" style=\"width: ".$inputwidth.";\"";
	        } elseif($field['fwidth']) {
	            $forminput.=" style=\"width: ".$field['fwidth'].";\"";
	        }
	        //if ($field['form_validate'])
	
	        $fieldformat['forminput_novalue']=$forminput;
	
	        $value = htmlspecialchars($value, ENT_QUOTES);
	        if ($value || is_numeric($value)){
	            $forminput .=" value=\"".$value."\"";
	            $fieldformat['value']=$value;
	        }elseif ($aved == "a" && $field['db_defaultvalue']){
	            $forminput .=" value=\"".$field['db_defaultvalue']."\"";
	            $fieldformat['value']=$field['db_defaultvalue'];
	        }
	
	        //add class
	        if ($field['charcount']) $forminput_class .="word_count".$field['charcount'];
	        if ($forminput_class) $forminput .=" class=\"".$forminput_class."\"";
	
	        if (!isset($field['fnobr_pre'])) $fieldformat['start']='<div id="fl_'.$field['core_db_fields_name'].'" class="clearfix">';
	
	        if (!$nolablefor) $this_nolablefor = ' for="form_'.$field['core_db_fields_name'].'"';
	
	        if (!$field['fnolabel']) {
	        	$fieldformat['label']='<label'.$this_nolablefor.' class="form-label">'.$field['form_name_lang'].'</label>';
			}
			
	        if (!isset($field['fnobr_pre'])) $fieldformat['input'] = '<div class="form-input">';
			if ($field['fnobrpre']) $fieldformat['input'] = $field['fnobrpre'];
			
	        $fieldformat['input'] .= '<input type="'.$inputtype.'" id="form_'.$field['core_db_fields_name'].'" name="'.$field['core_db_fields_name'].'"'.$forminput;
	
	        $fieldformat['input_end'] = ' />'.$lableextra;
	
	        if (isset($field['popbutton'])) {
	
	            if ($field['popicon'] && $field['poplang']){
	
	            }elseif ($field['poplang']){
	
	            }else {
	            $fieldformat['input_end'] .= ' <a class="button" title=".ui-icon-plus" data-icon-primary="ui-icon-plus" data-icon-only="true"';
	            }
	
	            $fieldformat['input_end'] .= ' onClick="'.$field['popjs'].'(\''.$field['form_name_lang'].'\');">&nbsp;</a><div id="'.$field['popadddivname'].'"></div>';
	
	        }
	
	
	        if (!$field['fnobr']) {
	        $fieldformat['input_end'] .= '</div>';
	        $fieldformat['end']='</div>';
	        }
	
	        //if ($fieldformat['fnobr_stop']) $fieldformat['end']='</div>';
	
	
	return($fieldformat);
	}
	
	function f_layout_form_edit_input($field,$value,$aved,$inputwidth=0){
	
	        if (($aved == "a" || $aved == "ad") && $field['db_defaultvalue']) $value = $field['db_defaultvalue'];
	
	        $fieldformat = $this->f_layout_form_edit_fieldformat($field,$value,$aved,'text',$inputwidth);
	
	        $thisline = '
	        '.$fieldformat['start'].'
	            '.$fieldformat['label'].'
	            '.$fieldformat['input'].$fieldformat['input_end'].'
	        '.$fieldformat['end'];
	
	return($thisline);
	}
	
	function f_layout_form_edit_input_hidden($field,$value,$aved,$inputwidth=0){
	
	        if (($aved == "a" || $aved == "ad") && $field['db_defaultvalue']) $value = $field['db_defaultvalue'];
	
	        $fieldformat = $this->f_layout_form_edit_fieldformat($field,$value,$aved,'hidden',$inputwidth);
	
	        $thisline = $fieldformat['input'].$fieldformat['input_end'];
	
	return($thisline);
	}
	
	function f_layout_form_edit_input_emailnumurl($field,$value,$aved,$inputwidth=0,$formtype){
	
	        if (($aved == "a" || $aved == "ad") && $field['db_defaultvalue']) $value = $field['db_defaultvalue'];
	
	        $fieldformat = $this->f_layout_form_edit_fieldformat($field,$value,$aved,$formtype,$inputwidth);
	
	        $thisline = '
	        '.$fieldformat['start'].'
	            '.$fieldformat['label'].'
	            '.$fieldformat['input'].$fieldformat['input_end'].'
	        '.$fieldformat['end'];

	return($thisline);
	}
	
	function f_layout_form_edit_input_phone($field,$value,$aved,$formtype){
	
	        if (($aved == "a" || $aved == "ad") && $field['db_defaultvalue']) $value = $field['db_defaultvalue'];
	
	        $fieldformat = $this->f_layout_form_edit_fieldformat($field,$value,$aved,'number',$inputwidth);
	
	        $fieldformat['input']='
	        <div class="form-input">
	        <input type="number"
	        id="telcode"
	        name="'.$field['core_db_fields_name'].'_country"
	        value="'.$fieldformat['value'].'" style="width: 15%;" '.$fieldformat['forminput_novalue'].'/>-<input type="number"
	        id="form_'.$field['core_db_fields_name'].'_area"
	        name="'.$field['core_db_fields_name'].'_area"
	        value="'.$fieldformat['value'].'" style="width: 15%;" '.$fieldformat['forminput_novalue'].'/>-<input type="number"
	        id="form_'.$field['core_db_fields_name'].'"
	        name="'.$field['core_db_fields_name'].'"
	        value="'.$fieldformat['value'].'" style="width:60%;" '.$fieldformat['forminput_novalue'].'/>
	        </div>
	        ';
	
	        $thisline = '
	        '.$fieldformat['start'].'
	            '.$fieldformat['label'].'
	            '.$fieldformat['input'].'
	        '.$fieldformat['end'];
	
	      /*
	        $thisline.='<div class="clearfix">';
	        $thisline.='</div>';
	        $thisline.='<label for="form_'.$field['core_db_fields_name'].'_area" class="form-label">'.$field['lang_confirm'].' '.$field['form_name_lang'].'</label>';
	        $thisline.='<div class="form-input"><input type="number" id="form_'.$field['core_db_fields_name'].'_area" name="'.$field['core_db_fields_name'].'_area"';
	        $thisline.='"/></div>';
	
	        $this_field['core_db_fields_seldb'] = "countrylist";
	        $select_getopt = core_d_element_dgroup_select_getopt($this_field,$app);
	        $this_field['sellist'] = $select_getopt['list'];
	        $this_field['form_required'] = "0";
	        $this_field['form_name_lang'] = "Country";
	        $this_field['core_db_fields_name'] = $field['core_db_fields_name'].'_country';
	
	        $thisline .= f_layout_form_edit_select($this_field,$value,$aved,0);
	
	        $thisline.='<div class="clearfix">';
	        $thisline.='</div>';
	        $thisline.='<label for="form_'.$field['core_db_fields_name'].'_area" class="form-label">test</label>';
	        $thisline.='<div class="form-input"><input type="text" id="telcode" name="telcode"';
	        $thisline.='"/></div>';
	        */
	return($thisline);
	}
	
	function f_layout_form_edit_password($field,$value,$aved,$inputwidth=0){
	
	        //pwformat lang_confirm
	
	        $fieldformat = $this->f_layout_form_edit_fieldformat($field,$value,$aved,'password',$inputwidth);
	
	        $thisline = '
	        '.$fieldformat['start'].'
	            '.$fieldformat['label'].'
	            '.$fieldformat['input'].$fieldformat['input_end'].'
	        '.$fieldformat['end'];
	
	        $thisline.='<div class="clearfix">';
	        $thisline.='</div>';
	
	        if ($aved == "a" || $aved == "e"){
	        $thisline.='<label for="form_'.$field['core_db_fields_name'].'_match" class="form-label">'.$field['lang_confirm'].' '.$field['form_name_lang'].'</label>';
	        $thisline.='<div class="form-input"><input type="password" id="form_'.$field['core_db_fields_name'].'_match" name="'.$field['core_db_fields_name'].'_match"';
	        $thisline.=' data-equals="'.$field['core_db_fields_name'].'"/></div>';
	        }
	
	return($thisline);
	}
	
	function f_layout_form_edit_textarea($field,$value,$aved,$inputwidth=0){
	
	        //db_length,db_defaultvalue,form_required,form_validate
	        //textarea_type,textarea_colrowwrap
	        //form_edit,form_del
	        //"editor", label: "Band", inputWidth: 400, inputHeight: 60
	
	        $fieldformat = $this->f_layout_form_edit_fieldformat($field,$value,$aved,0,$inputwidth);
	
	        if (!$field['textarea_colrowwrap']){
	        $row="5";
	        } else {
	        $colrowwrap = explode(",", $field['textarea_colrowwrap']);
	        $row=$colrowwrap[1];
	        }
	
	
	        //add class
	        $forminput_class = "";
	        if ($field['charcount']) $forminput_class .="word_count".$field['charcount'];
	        if ($forminput_class) $forminput_class =" class=\"".$forminput_class."\"";
	
	        $fieldformat['input']='<div class="form-input form-textarea"><textarea id="form_'.$field['core_db_fields_name'].'" name="'.$field['core_db_fields_name'].'" '.$fieldformat['forminput_novalue'].' rows="'.$row.'"'.$forminput_class.'/>'.$fieldformat['value'].'</textarea>';
	        if ($field['charcount']) $fieldformat['input'] .= ' <small><span class="counter"></span></small>';
	        $fieldformat['input'] .= '</div>';
	
	        $thisline = '
	        '.$fieldformat['start'].'
	            '.$fieldformat['label'].'
	            '.$fieldformat['input'].'
	        '.$fieldformat['end'];
	
	
	return($thisline);
	}
	/*
					{type: "calendar", dateFormat: "%Y-%m-%d %H:%i", name: "start_date", label: "Start Date", readonly: true, options:{
						yearsRange: [2010, 2015],
						isMonthEditable: true,
						isYearEditable: true,
						enableTime: true
					}},
					{type: "calendar", name: "end_date", label: "End Date", readonly: true}
	*/
	function f_layout_form_edit_date($field,$value,$aved,$inputwidth=0){
	
	        //db_length,db_defaultvalue,form_required,form_validate
	        //form_edit,form_del
	
	        //date(saved format),date_showformat,date_to
	
	        if ($value == "0000-00-00") {
	        $value = "";
	        }else {
	        if ($aved == "ed") $value = core_date_convert($field['date'],$field['date_showformat'],$value);
	        }
	
	        $fieldformat = $this->f_layout_form_edit_fieldformat($field,$value,$aved,'date',$inputwidth);
	
	        $fieldformat['input']='<div class="form-input"><input type="date" class="form-dateinput" id="form_'.$field['core_db_fields_name'].'" name="'.$field['core_db_fields_name'].'" value="'.$fieldformat['value'].'" '.$fieldformat['forminput_novalue'].'/></div>';
	
	        $thisline = '
	        '.$fieldformat['start'].'
	            '.$fieldformat['label'].'
	            '.$fieldformat['input'].'
	        '.$fieldformat['end'];
	
	return($thisline);
	}
	
	function f_layout_form_edit_datetime($field,$value,$aved,$inputwidth=0){
	
	        //db_length,db_defaultvalue,form_required,form_validate
	        //form_edit,form_del
	
	        //date(saved format),date_showformat,date_to
	
	        if ($value == "0000-00-00 00:00:00") {
	        $value = "";
	        }else {
	        if ($aved == "ed") $value = core_date_convert($field['date'],$field['date_showformat'],$value);
	        }
	
	        $fieldformat = $this->f_layout_form_edit_fieldformat($field,$value,$aved,'date',$inputwidth);
	
	        $fieldformat['input']='<div class="form-input"><input type="date" class="form-dateinput" id="form_'.$field['core_db_fields_name'].'" name="'.$field['core_db_fields_name'].'" value="'.$fieldformat['value'].'" '.$fieldformat['forminput_novalue'].'/></div>';
	
	        $thisline = '
	        '.$fieldformat['start'].'
	            '.$fieldformat['label'].'
	            '.$fieldformat['input'].'
	        '.$fieldformat['end'];
	
	return($thisline);
	}
	
	function f_layout_form_edit_time($field,$value,$aved,$inputwidth=0){
	
	        //db_length,db_defaultvalue,form_required,form_validate
	        //form_edit,form_del
	
	        //date(saved format),date_showformat,date_to
	
	        if ($value == "00:00:00") {
	        $value = "";
	        }else {
	        if ($aved == "ed") $value = core_date_convert($field['date'],$field['date_showformat'],$value);
	        }
	
	        $fieldformat = $this->f_layout_form_edit_fieldformat($field,$value,$aved,'date',$inputwidth);
	
	        $fieldformat['input']='<div class="form-input"><input type="time" class="form-dateinput" id="form_'.$field['core_db_fields_name'].'" name="'.$field['core_db_fields_name'].'" value="'.$fieldformat['value'].'" '.$fieldformat['forminput_novalue'].'/></div>';
	
	        $thisline = '
	        '.$fieldformat['start'].'
	            '.$fieldformat['label'].'
	            '.$fieldformat['input'].'
	        '.$fieldformat['end'];
	
	return($thisline);
	}
	
	
	function f_layout_form_edit_select($field,$value,$aved,$inputwidth=0){
	
	        //db_length,db_defaultvalue,form_required,form_validate
	        //form_edit,form_del
	
	        //sellist,selmulti,seldefault,selblank
	        //$this_value = f_layout_form_select_view($this_field['sellist'],$formvalue[$this_field['core_db_fields_name']]);
	
	        //<div class="form-input"><select id="form-timezone"><option>America/Los Angeles</option><option>America/New York</option><option>Asia/Manila</option></select></div>
			$thisline = "";
	        if (!isset($field['selbutton'])){
	
	            $fieldformat = $this->f_layout_form_edit_fieldformat($field,$value,$aved,0,$inputwidth);
	
	            $fieldformat['input'] = '<div class="form-input"><select id="form_'.$field['core_db_fields_name'].'" name="'.$field['core_db_fields_name'].'">';
	             //echo $field['seldefault'];exit;
	            if (!$value && isset($field['seldefault'])) $value = $field['seldefault'];
	            //echo $field['core_db_fields_name'];
	            //print_r($field['sellist']);
	            if ($field['sellist']){
	              foreach ($field['sellist'] as $thisoption){
	                if (!$thisoption['key'] && !$thisoption['name']){
	
	                    if (!$field['autocomplete']){
	                        $fieldformat['input'].="<option value=\"0\"";
	                        if (!$value) $thisline.=" SELECTED";
	                        $fieldformat['input'].=">----------</option>";
	                    } else {
	                        $fieldformat['input'].="<option value=\"\"";
	                        if (!$value) $thisline.=" SELECTED";
	                        $fieldformat['input'].="></option>";
	                    }
	
	                }else{ 
	                    $fieldformat['input'].="<option value=\"".$thisoption['key']."\"";
	                    if ($thisoption['key'] == $value) $fieldformat['input'].=" SELECTED";
	                    $fieldformat['input'].=">".$thisoption['name']."</option>";
	                }
	
	              }
	            }
				
	            /*
	            $this_count = 0;
	            $this_count_result = $this_count+1;
	            $this_array = explode(",", $field['sellist']);
	            //print_r($this_array); exit;
	
	            if (!$this_array[0] && !$this_array[1]) {
	                $fieldformat['input'].="<option value=\"0\"";
	                if (!$value) $thisline.=" SELECTED";
	                $fieldformat['input'].=">----------</option>";
	                $this_count = $this_count+2;
	            }
	
	            while ($this_array[$this_count]){
	                $this_count_result = $this_count+1;
	                $fieldformat['input'].="<option value=\"".$this_array[$this_count]."\"";
	                if ($this_array[$this_count] == $value) $fieldformat['input'].=" SELECTED";
	                $fieldformat['input'].=">".$this_array[$this_count_result]."</option>";
	                $this_count = $this_count+2;
	            }
	            */
	
	            $fieldformat['input'] .= '</select></div>';
	
	        } else {
	
	            $fieldformat = $this->f_layout_form_edit_fieldformat($field,$value,$aved,0,$inputwidth,1);
	
	            $fieldformat['input'] = '<div class="buttonset form-input">
	            ';
	             //echo $field['seldefault'];exit;
	            if (!$value && isset($field['seldefault'])) $value = $field['seldefault'];
	
	            if ($field['sellist']){
	              $this_count = 0;
	              foreach ($field['sellist'] as $thisoption){
	                if (!$thisoption['key'] && !$thisoption['name']){
	                    $fieldformat['input'].="<input type=\"radio\" name=\"form_".$field['core_db_fields_name']."\" id=\"form_".$field['core_db_fields_name'].$this_count."\" value=\"0\" ";
	                    if ($thisoption['key'] == $value) $fieldformat['input'].=" CHECKED";
	                    $fieldformat['input'].="/><label for=\"form_".$field['core_db_fields_name'].$this_count."\">&nbsp;&nbsp;&nbsp;&nbsp;</label>";
	                }else{
	                    $fieldformat['input'].="<input type=\"radio\" name=\"form_".$field['core_db_fields_name']."\" id=\"form_".$field['core_db_fields_name'].$this_count."\" value=\"".$thisoption['key']."\" ";
	                    if ($thisoption['key'] == $value) $fieldformat['input'].=" CHECKED";
	                    $fieldformat['input'].="/><label for=\"form_".$field['core_db_fields_name'].$this_count."\">".$thisoption['name']."</label>";
	                }
	                $this_count++;
	              }
	            }
	
	
	            /*
	            $this_count = 0;
	            $this_array = explode(",", $field['sellist']);
	            //print_r($this_array); exit;
	
	            while ($this_array[$this_count]){
	                $this_count_result = $this_count+1;
	
	                $fieldformat['input'].="<input type=\"radio\" name=\"form_".$field['core_db_fields_name']."\" id=\"form_".$field['core_db_fields_name'].$this_count."\" value=\"".$this_array[$this_count]."\" ";
	                if ($this_array[$this_count] == $value) $fieldformat['input'].=" CHECKED";
	                $fieldformat['input'].="/><label for=\"form_".$field['core_db_fields_name'].$this_count."\">".$this_array[$this_count_result]."</label>";
	
	                $this_count = $this_count+2;
	            }
	            */
	
	
	            $fieldformat['input'] .= '</div>';
	
	        }
	
	
	        $thisline = '
	        '.$fieldformat['start'].'
	            '.$fieldformat['label'].'
	            '.$fieldformat['input'].'
	        '.$fieldformat['end'];
	
	return($thisline);
	}
	
	function f_layout_form_edit_chkbox($field,$value,$aved,$inputwidth=0){
	
	        $fieldformat = $this->f_layout_form_edit_fieldformat($field,$value,$aved,0,$inputwidth);
	
	        $thisline = '
	        '.$fieldformat['start'].'
	            '.$fieldformat['label'];
	
	        $thisline .='
	        <div class="form-input"><input type="checkbox" id="form_'.$field['core_db_fields_name'].'" name="'.$field['core_db_fields_name'].'"';
	        if (($aved == "a" || $aved == "ad") && $field['chked']){
	        $thisline .= ' checked="true"';
	        }elseif ($value) {
	        $thisline .= ' checked="true"';
	        }
	        $thisline.='/></div>';
	
	        $thisline .= '
	        '.$fieldformat['end'];
	
	return($thisline);
	}	
	
	
	

}