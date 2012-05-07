<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class DS_Helpdesk_Nodataset extends MY_Model {
	function __construct() {
		parent::__construct();
		
		$this->table = 'a_helpdesk';
		$this->cache_enabled = TRUE;
	}
	
	function getContent($id){
		$this->db->select('*');
		$this->db->where('id',$id);
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getAssignName($id){
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