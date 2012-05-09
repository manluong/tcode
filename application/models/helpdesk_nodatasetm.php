<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Helpdesk_NodatasetM extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table = 'a_helpdesk';
		$this->cache_enabled = TRUE;
		$this->sett_filter_deleted = FALSE;
	}

	function get_content($id){
		$this->db->select('*');
		$this->db->where('id',$id);
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
}