<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Element_dgroup_sql extends Element_dgroup {

	function __construct() {
		parent::__construct();
	}

	function get_action_element($app, $an){

		$sql = "SELECT * FROM global_setting.core_apps_action_element WHERE core_apps_action_element_x_core_apps_action_name = '$an' AND core_apps_action_element_active = '1' AND core_apps_action_element_x_core_apps_name = '$app' ORDER BY core_apps_action_element_sort ASC";
		$rs = $this->db->query($sql);

		if ($rs->num_rows() == 0) return FALSE;
		return $rs->result_array();

	}



}