<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class MenuM extends CI_Model {
	var $url = array();
	var $actions = array();

	var $public_apps = array('access', 'dashboard');

	function __construct() {
		parent::__construct();

		$CI =& get_instance();
		$this->url = $CI->url;
	}

	function get_menu() {
		
		$sql = "SELECT core_apps_name,core_apps_icon FROM core_apps WHERE core_apps_status = '1' AND core_apps_showmenu = '1' ORDER BY core_apps_menusort";
		$rs = $this->db->query($sql);

		if ($rs->num_rows() == 0) return FALSE;
		return $rs->result_array();		
		
	}
	
	
}