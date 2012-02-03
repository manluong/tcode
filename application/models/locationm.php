<?php

class LocationM extends MY_Model {

	function __construct() {
		$this->table = 'locations';
		$this->id_field = 'id';

		parent::__construct();
	}


	function get_name($id) {
		$rs = $this->db->select('name')
				->from($this->table)
				->where('id', $id)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$result = $rs->row_array();

		return $result['name'];
	}

}
