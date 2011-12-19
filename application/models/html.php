<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Html extends CI_Model {
	function __construct() {
		parent::__construct();
	}

	/////////////////////////////////////////////////////////
	//load template
	/////////////////////////////////////////////////////////

	function html_template($apps_action){
		$id['platform'] = 1;
		$layout = array();
		//this function is possible to make CACHE entry
		//save the result as app-an name
		//load the saved data as $layout

		//platform
		//1=web
		//2=mobile
		//3=tablet
		//$id['platform']<< this give you the platform, a hidden field when user login
		//however, if the user yet to login, a function is called to check the platform and store in PHP session

		//LOAD TEMPLATE
		if (isset($apps_action['core_apps_action_template']) && $apps_action['core_apps_action_template']!='') {
			$rs = $this->db->select()
					->from('core_layout_template')
					->where('core_layout_template_name', $apps_action['core_apps_action_template'])
					->where('core_layout_template_platform', $id['platform'])
					->limit(1)
					->get();
			$layout_template_theme = 0;
		} else {
			$rs = $this->db->select()
					->from('core_layout_template')
					->where('core_layout_template_platform', $id['platform'])
					->where('core_layout_template_default', 1)
					->limit(1)
					->get();
			$layout_template_theme = 0;
		}

		if ($rs->num_rows()>0) {
			$result = $rs->row_array();

			$layout['name'] = $result['core_layout_template_name'];
		    $layout['pagefile'] = DOCUMENT_ROOT.'/'.$result['core_layout_template_folder'].'/'.$result['core_layout_template_pagefile'];
		    $layout['pageplain'] = DOCUMENT_ROOT.'/'.$result['core_layout_template_folder'].'/'.$result['core_layout_template_pageplain'];
		    $layout['folder'] = $result['core_layout_template_folder'];
		    $layout['folderinc'] = $result['core_layout_template_folderinc'];
		    $layout['formtype'] = $result['core_layout_template_formtype'];
		    $layout['listtype'] = $result['core_layout_template_listtype'];
		    if ($result['core_layout_template_addons']) $layout['addons'] = $result['core_layout_template_addons'].',';

		} else {
		    meg(999, 'No Template Found.');
		}

		//LOAD FORMAT
	    //What is the page output?
	    //format_type
		//if type=1 (noraml), with logo,menu?
	    if (isset($apps_action['core_apps_action_x_core_layout_format_name'])) {
			$rs = $this->db->select()
					->from('core_layout_format')
					->where('core_layout_format_name', $apps_action['core_apps_action_x_core_layout_format_name'])
					->limit(1)
					->get();
	    } else {
			$rs = $this->db->select()
					->from('core_layout_format')
					->where('core_layout_format_name', 'default')
					->limit(1)
					->get();
	    }

		if ($rs->num_rows()>0) {
			$result = $rs->row_array();

		    $layout['type'] = $result['core_layout_format_type'];
		    $layout['logo'] = $result['core_layout_format_logo'];
		    $layout['menu'] = $result['core_layout_format_menu'];
		    $layout['footer'] = $result['core_layout_format_footer'];
			$layout['boxformat'] = $result['core_layout_format_boxformat'];
			if ($result['core_layout_format_addons']) $layout['addons'] .= $result['core_layout_format_addons'].',';
	    } else {
	    	meg(999, 'No Layout Format Specified Found.');
	    }

		//OTHER
		$layout['format'] = $apps_action['core_apps_action_x_core_layout_format_name'];
		$layout['content'] = $apps_action['core_apps_action_content_layout'];
		$layout['breadcrumb'] = $apps_action['core_apps_action_breadcrumb'];
		if ($apps_action['core_apps_action_addons']) $layout['addons'] .= $apps_action['core_apps_action_addons'].',';

		//MENU
		if ($apps_action['core_apps_action_appmenu']) {
			$layout['appmenu'] = 1;
			if ($apps_action['core_apps_action_appmenu_gp']) {
				$layout['appmenu_gp'] = $apps_action['core_apps_action_appmenu_gp'];
			}
		}

		return $layout;
	}

	function html_addons($addonnames) {
		$addons = array(
			'css' => '',
			'js' => '',
			'js_bodyend' => '',
		);

		$rs = $this->db->select()
				->from('core_addons')
				->where_in('core_addons_name', explode(',', $addonnames))
				->get();

		foreach($rs->result_array() as $field){
			if (isset($field['core_addons_css'])) $addons['css'] .= $field['core_addons_css'];
			if (isset($field['core_addons_js'])) $addons['js'] .= $field['core_addons_js'];
			if (isset($field['core_addons_bodyendjs'])) $addons['js_bodyend'] .= $field['core_addons_bodyendjs'];
		}

		return $addons;
	}

}