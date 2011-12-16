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
		if (!$apps_action['core_apps_action_template']){
			$sql = "SELECT * FROM core_layout_template WHERE core_layout_template_platform = '".$id['platform']."' AND core_layout_template_default = '1' LIMIT 1";
			$layout_template_theme = 0;
		}else{
			$sql = "SELECT * FROM core_layout_template WHERE core_layout_template_name = '".$apps_action['core_apps_action_template']."' AND core_layout_template_platform = '".$id['platform']."' LIMIT 1";
			$layout_template_theme = 0;
		}

		$result = $this->db->query($sql);
		$result = $result->row_array(0);

		if ($result){

			$layout['name'] = $result['core_layout_template_name'];
		    $layout['pagefile'] = DOCUMENT_ROOT.'/'.$result['core_layout_template_folder'].'/'.$result['core_layout_template_pagefile'];
		    $layout['pageplain'] = DOCUMENT_ROOT.'/'.$result['core_layout_template_folder'].'/'.$result['core_layout_template_pageplain'];
		    $layout['folder'] = $result['core_layout_template_folder'];
		    $layout['folderinc'] = $result['core_layout_template_folderinc'];
		    $layout['formtype'] = $result['core_layout_template_formtype'];
		    $layout['listtype'] = $result['core_layout_template_listtype'];
		    if ($result['core_layout_template_addons']) $layout['addons'] = $result['core_layout_template_addons'].",";

		}else{
		    meg(999,"No Template Found.");
		}

		//LOAD FORMAT
	    //What is the page output?
	    //format_type
		//if type=1 (noraml), with logo,menu?
	    if (!$apps_action['core_apps_action_x_core_layout_format_name']) {
	        $sql2 = "SELECT * FROM core_layout_format WHERE core_layout_format_name  = 'default' LIMIT 1";
	    }else{
	        $sql2 = "SELECT * FROM core_layout_format WHERE core_layout_format_name  = '".$apps_action['core_apps_action_x_core_layout_format_name']."' LIMIT 1";
	    }
	    //$result2 = $db->fetchRow($sql2, 2);
		$result2 = $this->db->query($sql2);
		$result2 = $result2->row_array(0);

		if ($result2){
		    $layout['type'] = $result2['core_layout_format_type'];
		    $layout['logo'] = $result2['core_layout_format_logo'];
		    $layout['menu'] = $result2['core_layout_format_menu'];
		    $layout['footer'] = $result2['core_layout_format_footer'];
			$layout['boxformat'] = $result2['core_layout_format_boxformat'];
			if ($result2['core_layout_format_addons']) $layout['addons'] .= $result2['core_layout_format_addons'].",";
	    }else{
	    	meg(999,"No Layout Format Specified Found.");
	    }

		//OTHER
		$layout['format'] = $apps_action['core_apps_action_x_core_layout_format_name'];
		$layout['content'] = $apps_action['core_apps_action_content_layout'];
		$layout['breadcrumb'] = $apps_action['core_apps_action_breadcrumb'];
		if ($apps_action['core_apps_action_addons']) $layout['addons'] .= $apps_action['core_apps_action_addons'].",";

		//MENU
		if ($apps_action['core_apps_action_appmenu']){
		    if ($apps_action['core_apps_action_appmenu_gp']) {
		        $layout['appmenu'] = 1;
		        $layout['appmenu_gp'] = $apps_action['core_apps_action_appmenu_gp'];
		    }else{
		        $layout['appmenu'] = 1;
		    }
		}

	return ($layout);
	}

	function html_addons($addonname){

	    $this_count = 0;
	    $this_array = explode(",", $addonname);
		$this_comma = "";
		$where_equal = "";
	    while ($this_array[$this_count]){
	        $where_equal .= $this_comma."'".$this_array[$this_count]."'";
			$this_comma = ",";
	    $this_count++;
	    }

	    $sql = "SELECT * FROM core_addons WHERE core_addons_name IN (".$where_equal.")";
	    //$result = $db->fetchAll($sql, 2);
		$result = $this->db->query($sql);
		$result = $result->result_array();

		$addons = array();
	    $addons['css'] = "";
	    $addons['js'] = "";
	    $addons['js_bodyend'] = "";

		foreach($result as $field){
	    if (isset($field['core_addons_css'])) $addons['css'] .= $field['core_addons_css'];
	    if (isset($field['core_addons_js'])) $addons['js'] .= $field['core_addons_js'];
	    if (isset($field['core_addons_bodyendjs'])) $addons['js_bodyend'] .= $field['core_addons_bodyendjs'];
		}

	return ($addons);
	}

}