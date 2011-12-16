<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Langmodel extends CI_Model {
	function __construct() {
		parent::__construct();
	}

	function loadarray($app, $lang_use){
		//Lang file storage
		//Get array from DB now, can change to get from file in future.

		$rs = $this->db->select()
				->where('lang_array_app', $app)
				->where('lang_array_lang', $lang_use)
				->get('lang_ci', 1);

		if ($rs->num_rows() == 0) return array();

    	$result = $rs->row_array();
    	return unserialize($result['lang_array_array']);
	}

	function initialise(){
		$rs = $this->db->select('lang_use_code, lang_use_name, lang_use_default')
				->where('lang_use_active', 1)
				->get('lang_use');

		if ($rs->num_rows() == 0) return array();

		return $rs->result_array();
	}


}