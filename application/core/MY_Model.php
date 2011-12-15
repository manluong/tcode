<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class MY_Model extends CI_Model {
	function __construct() {
		parent::__construct();

	}



	function get($id) {
		$rs = $this->db->select()
				->where('id', $id)
				->from($this->db_table);
		return $rs->row_array();
	}
	
	function get_list() {
		$rs = $this->db->select()
				->from($this->db_table);
		return $rs->result_array();
	}
	
	
	function save($data) {
		//save to database
	}




}