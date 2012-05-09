<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class DS_CommentM extends MY_Model {
	function __construct() {
		parent::__construct();
		
		$this->table = 'a_helpdesk_comment';
		$this->cache_enabled = TRUE;
	}
	
	function get_group(){
		$this->db->select('*');
		$query = $this->db->get('a_access_gpsub');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function get_status(){
		$this->db->select('*');
		$query = $this->db->get('a_status');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function get_type(){
		$this->db->select('*');
		$query = $this->db->get('a_type');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function get_priority(){
		$this->db->select('*');
		$query = $this->db->get('a_priority');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function get_assign(){
		$this->db->select('id,nickname');
		$query = $this->db->get('card');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function get_content($id){
		$this->db->select('*');
		$this->db->where('helpdesk_id',$id);
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function get_content_helpdesk($id){
		$this->db->select('*');
		$this->db->where('id',$id);
		$query = $this->db->get('a_helpdesk');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function get_assigname($id){
		$this->db->select('nickname');
		$this->db->where('id',$id);
		$query = $this->db->get('card');

		if($query->result()){
			$tmp = $query->result();
			if(!empty($tmp)){
				foreach($tmp as $k){
					return $k->nickname;
				}
			}
		}else{
			return false;
		}
	}

}