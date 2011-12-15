<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Langmodel extends CI_Model {

	function __construct() {
		parent::__construct();
	}
	
	function loadarray($app,$lang_use){

		//Lang file storage
		//Get array from DB now, can change to get from file in future.
    	$sql = 'SELECT * FROM lang_ci WHERE lang_array_app = "'.$app.'" AND lang_array_lang = "'.$lang_use.'"';
    	$result = $this->db->query($sql);
		$this_array = $result->row_array(0);
		$this_array = unserialize($this_array['lang_array_array']);
    	return $this_array;
	}
	
	function initialise(){
		
		$sql = 'SELECT lang_use_code,lang_use_name,lang_use_default FROM lang_use WHERE lang_use_active = "1"';
		$result = $this->db->query($sql);
		return $result->result_array();

	}
	

}