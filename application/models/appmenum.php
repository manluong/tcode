<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class AppmenuM extends CI_Model {
	var $url = array();
	var $actions = array();

	function __construct() {
		parent::__construct();

		$CI =& get_instance();
		$this->url = $CI->url;
	}

	function get_menu() {
		$rs = $this->db->select('core_apps_name, core_apps_icon')
				->from('core_apps')
				->where('core_apps_status', '1')
				->where('core_apps_showmenu', '1')
				->order_by('core_apps_menusort')
				->get();

		if ($rs->num_rows() == 0) return FALSE;
		return $rs->result_array();
	}

	// Get the app menu array from db
	function get_appmenu_gp($appmenu_gp) {
		$rs = $this->db->select()
				->from('core_apps_menu_gp')
				->where('core_apps_menu_gp_name', $appmenu_gp)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;
		return $rs->row_array();
	}

	function get_appmenu_item($appmenu_gp) {
		$rs = $this->db->select()
				->from('core_apps_menu')
				->where('core_apps_menu_group', $appmenu_gp)
				->where('core_apps_menu_status', 1)
				->order_by('core_apps_menu_parent, core_apps_menu_sort', 'ASC')
				->get();

		if ($rs->num_rows() == 0) return FALSE;
		return $rs->result_array();
	}

	function get_realmenugp($menu_function, $card_accessgp) {
		$default = 'menu_nogp';

		$menugp = array(
			'card' => array(
				1 => '',
				2 => 'menu_staff',
				3 => 'menu_client',
				4 => 'menu_client',
				5 => 'menu_vendor',
				6 => 'menu_member',
				7 => '',
				8 => 'po',
			),
		);

		if ( isset($menugp[$menu_function][$card_accessgp]) ) {
			return $menugp[$menu_function][$card_accessgp];
		} else {
			return $default;
		}
	}





	function get_appmenu($appmenu_gp='') {
		$appmenu = array(
			'appmenu' => 1,
		);

		if (isset($this->layout['appmenu'])) $appmenu['appmenu'] = $this->layout['appmenu'];

		if ($appmenu_gp == '') {
			$appmenu_gp = (isset($this->layout['appmenu_gp']) && $this->layout['appmenu_gp']!='')
				? $this->layout['appmenu_gp']
				: $this->url['app'];
		}

		$result_menugp = $this->get_appmenu_gp($appmenu_gp);

		if ($result_menugp === FALSE) return FALSE;

		$menugp_getthisid = $result_menugp['core_apps_menu_gp_getthisid'];
		$menugp_thisidtype = $result_menugp['core_apps_menu_gp_thisidtype'];
		$menugp_realmenugp = $result_menugp['core_apps_menu_gp_realmenugp'];
		$menugp_realmenufunction = $result_menugp['core_apps_menu_gp_realmenufunction'];

		//go getthisid
		if ($menugp_getthisid && !isset($this->App_generalM->moreid['set'])) $this->App_generalM->get_moreid($menugp_thisidtype);

		//go get the realmenugp
		if ($menugp_realmenufunction) {
			//if $app is card, go get the access_group to see if it's client/vendor/staff
			if ($this->url['app'] == 'card' && $this->url['id_plain'] != 0) $get_accessgp = $this->App_generalM->get_accessgp($this->url['id_plain']);
			$menugp_realmenugp = $appmenu_gp = $this->get_realmenugp($menugp_realmenufunction, $get_accessgp['accessgp']);
		}


		if ($menugp_realmenugp && $menugp_realmenugp != $appmenu_gp) {
			$result_menugp_real = $this->get_appmenu_gp($menugp_realmenugp);
			$appmenu['style'] = $result_menugp_real['core_apps_menu_gp_style'];
			$menugp_app = $result_menugp_real['core_apps_menu_gp_app'];
		} else {
			$appmenu['style'] = $result_menugp['core_apps_menu_gp_style'];
			$menugp_app = $result_menugp['core_apps_menu_gp_app'];
		}

		$result_menu = $this->get_appmenu_item($appmenu_gp);

		$button_count = 0;
		$button = array();
		$thisparent = 'top';

		if ($result_menu) {
			foreach ($result_menu as $field_menu) {

				if (!$field_menu['core_apps_menu_thisidtype'] || $field_menu['core_apps_menu_thisidtype'] != 'thisid' || ($field_menu['core_apps_menu_thisidtype'] == 'thisid' && $this->url['id_plain'])) {

					if (($field_menu['core_apps_menu_parent'] != '') && ($field_menu['core_apps_menu_parent'] != $thisparent)) {
						$thisparent = $field_menu['core_apps_menu_parent'];
						$button_count = 0;
					}

					if ($field_menu['core_apps_menu_link']) {
						$button[$thisparent][$button_count]['link'] = htmlspecialchars_decode($field_menu['core_apps_menu_link']);
					} elseif ($field_menu['core_apps_menu_app'] || $field_menu['core_apps_menu_action']) {
						if ($field_menu['core_apps_menu_app']) {
							$button[$thisparent][$button_count]['link'] = '/'.$field_menu['core_apps_menu_app'];
						} else {
							$button[$thisparent][$button_count]['link'] = '/'.$this->url['app'];
						}

						if ($field_menu['core_apps_menu_action']) $button[$thisparent][$button_count]['link'] .= '/'.$field_menu['core_apps_menu_action'];

						if ($field_menu['core_apps_menu_thisidtype'] == 'thisid') {
							$button[$thisparent][$button_count]['link'] .= '/'.$this->url['id_encrypted'];
						} elseif ($field_menu['core_apps_menu_thisidtype']) {
							$button[$thisparent][$button_count]['link'] .= '/'.$this->App_generalM->moreid["en"][$field_menu['core_apps_menu_thisidtype']];
						}

						if ($field_menu['core_apps_menu_aved']) $button[$thisparent][$button_count]['link'] .= '/'.$field_menu['core_apps_menu_aved'];
						if ($field_menu['core_apps_menu_extra']) $button[$thisparent][$button_count]['link'] .= '/'.$field_menu['core_apps_menu_extra'];
					} else {
						$button[$thisparent][$button_count]['nolink'] = 1;
						$button[$thisparent][$button_count]['link'] = '';
					}

					$button[$thisparent][$button_count]['name'] = $field_menu['core_apps_menu_name'];
					$langname = 'appmenu_'.$field_menu['core_apps_menu_name'];
					$button[$thisparent][$button_count]['lang'] = $this->lang->line('core'.$langname);

					$button[$thisparent][$button_count]['icon'] = $field_menu['core_apps_menu_icon'];
					//$button[$thisparent][$button_count]['icon2'] = $field_menu['core_apps_menu_icon2'];
					$button[$thisparent][$button_count]['count'] = $button_count;
					$button[$thisparent][$button_count]['an'] = $field_menu['core_apps_menu_action'];
					$button[$thisparent][$button_count]['class'] = $field_menu['core_apps_menu_class'];

					//$button[$thisparent][$button_count]['thisidtype'] = $field_menu['core_apps_menu_thisidtype'];
					//$button[$thisparent][$button_count]['replacetitle'] = $field_menu['core_apps_menu_replacetitle'];
					//$button[$thisparent][$button_count]['replace_thisidtype'] = $field_menu['core_apps_menu_replace_thisidtype'];
					//$button[$thisparent][$button_count]['replace_dgroupname'] = $field_menu['core_apps_menu_dgroupname'];
					//$button[$thisparent][$button_count]['replace_fdataname'] = $field_menu['core_apps_menu_fdataname'];

					/*
					if ($field_menu['core_apps_menu_replacetitle']) {
						if ($field_menu['core_apps_menu_replace_thisidtype']) {
							$this_replace_thisid = $this->App_generalM->moreid[$field_menu['core_apps_menu_replace_thisidtype']];
						} else {
							$this_replace_thisid = $this->url['id_plain'];
						}
						$this->load->model('element/Element_dgroup');
						$button[$thisparent][$button_count]['lang'] = $this->Element_dgroup->element_fdata_from_dgroup($field_menu['core_apps_menu_dgroupname'], $field_menu['core_apps_menu_fdataname'], $this_replace_thisid, $field_menu['core_apps_menu_dgroupapp']);
					}
					*/

					if ($field_menu['core_apps_menu_seprator']) {
						$button_count++;
						$button[$thisparent][$button_count]['seprator'] = 1;
						$button[$thisparent][$button_count]['count'] = $button_count;
					}

					$button_count ++;
				}

			}
		}

		if (isset($this->layout['breadcrumb']) && $this->layout['breadcrumb']) {
			//format the breadcrumb
			$appmenu['apphead'] = 1;

			$this_breadcrumb[0]['title'] = $this->lang->line('coreapptitle_'.$menugp_app);
			$this_breadcrumb[0]['link'] = '/'.$menugp_app;

			if ($button['top']) {
				foreach ($button['top'] as $thisbutton) {
					if (isset($thisbutton['seprator']) || isset($this_breadcrumb_found)) continue;

					if (isset($thisbutton['an']) && $thisbutton['an'] == $this->url['action']) {
						if (isset($thisbutton['lang'])) $this_breadcrumb[1]['title'] = $thisbutton['lang'];
						if (isset($thisbutton['link'])) $this_breadcrumb[1]['link'] = $thisbutton['link'];
						$this_breadcrumb_found = 1;
					}

					if ( !isset($button[$thisbutton['name']]) ) continue;

					foreach ($button[$thisbutton['name']] as $thisbuttonchild) {
						if ($thisbuttonchild['an'] != $an) continue;

						$this_breadcrumb[2]['title'] = $thisbuttonchild['lang'];
						$this_breadcrumb[2]['link'] = $thisbuttonchild['link'];
						$this_breadcrumb[1]['title'] = $thisbutton['lang'];
						$this_breadcrumb[1]['link'] = $thisbutton['link'];
						$this_breadcrumb_found = 1;
					}
				}
			}

			$appmenu['breadcrumb'] = $this_breadcrumb;
		}


		$appmenu['appmenu_gp'] = $appmenu_gp;
		$appmenu['button'] = $button;


		return $appmenu;
	}









}