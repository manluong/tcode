<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class HelpdeskM extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table = 'a_helpdesk';
		$this->cache_enabled = TRUE;
		$this->sett_filter_deleted = FALSE;
	}
	
	function getTotalRecord(){
		$this->db->select('id');
		$query = $this->db->get($this->table);

		foreach ($query->result() as $row){
			$result[] = $row;
		}
		
		if(!empty($result)){
			return count($result);
		}else{
			return false;
		}
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
	
	function search_content($value='',$limit=10){
		$this->db->select('*');
		if($value !=''){
			$this->db->like('subject',$value);
		}
		$this->db->limit($limit);
		$query = $this->db->get($this->table);
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function group_fillter($value){
		$this->db->select('*');
		$this->db->where('group',$value);
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function status_fillter($value){
		$this->db->select('*');
		$this->db->where('status',$value);
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function type_fillter($value){
		$this->db->select('*');
		$this->db->where('type',$value);
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function priority_fillter($value){
		$this->db->select('*');
		$this->db->where('priority',$value);
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
}