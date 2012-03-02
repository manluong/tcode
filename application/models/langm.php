<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class LangM extends CI_Model {
	var $active = array();

	function __construct() {
		parent::__construct();

		$this->load_active_languages();
	}

	function load_active_languages(){
		$rs = $this->db->select('lang_use_code, lang_use_name, lang_use_default')
				->where('lang_use_active', 1)
				->get('global_setting.lang_use');

		if ($rs->num_rows() == 0) return FALSE;

		$this->active = $rs->result_array();
	}

	function get_array($app, $language){
		//Lang file storage
		//Get array from DB now, can change to get from file in future.

		$rs = $this->db->select('lang_array_array')
				->where('lang_array_app', $app)
				->where('lang_array_lang', $language)
				->get('global_setting.lang_ci', 1);

		if ($rs->num_rows() == 0) return array();

    	$result = $rs->row_array();
    	return unserialize($result['lang_array_array']);
	}
}