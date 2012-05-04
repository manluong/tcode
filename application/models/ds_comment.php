<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class DS_Comment extends MY_Model {
	function __construct() {
		parent::__construct();
		
		$this->table = 'a_helpdesk_comment';
		$this->cache_enabled = TRUE;
	}
	
	function getGroup(){
		$this->db->select('*');
		$query = $this->db->get('a_access_gpsub');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getStatus(){
		$this->db->select('*');
		$query = $this->db->get('a_status');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getType(){
		$this->db->select('*');
		$query = $this->db->get('a_type');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getPriority(){
		$this->db->select('*');
		$query = $this->db->get('a_priority');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getContent($id){
		$this->db->select('*');
		$this->db->where('helpdesk_id',$id);
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
}