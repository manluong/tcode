<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Helpdesk_CommentM extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->app = 'helpdesk';
		$this->table = 'a_helpdesk_comment';
		$this->cache_enabled = TRUE;
        $this->sett_filter_deleted = FALSE;
	}

	function get_assign() {
		$this->db->select('id,display_name');
		$query = $this->db->get('card');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function get_comment_list($id){
		$this->db->select('a_helpdesk_comment.id, a_helpdesk_comment.comment, a_helpdesk_comment.created_stamp, card.display_name, card.organization_name');
		$this->db->from('a_helpdesk_comment');
		$this->db->join('card', 'card.id = a_helpdesk_comment.created_card_id');
		$this->db->where('helpdesk_id',$id);
		$this->db->where('a_helpdesk_comment.active',0);
		$this->db->order_by('created_stamp','DESC');
		
		$query = $this->db->get();
		
		if ($query->result_array()) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	
	function get_content($id) {
		$this->db->select('*');
		$this->db->where('helpdesk_id',$id);
		$this->db->where('active',0);
		$query = $this->db->get($this->table);

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_content_helpdesk($id) {
		$this->db->select('*');
		$this->db->where('id',$id);
		$query = $this->db->get('a_helpdesk');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_assigname($id) {
		$this->db->select('first_name, last_name');
		$this->db->where('id',$id);
		$query = $this->db->get('card');

		if ($query->result()) {
			$tmp = $query->result();
			if (!empty($tmp)) {
				foreach($tmp as $k) {
					return $k->first_name.' '.$k->last_name;
				}
			}
		} else {
			return false;
		}
	}

	function get_priority_type($id) {
		$this->db->select('name');
		$this->db->where('id',$id);
		$query = $this->db->get('a_priority');

		if ($query->result()) {
			$tmp = $query->result();
			if (!empty($tmp)) {
				foreach($tmp as $k) {
					return $k->name;
				}
			}
		} else {
			return false;
		}
	}

    function get_comment_not_use() {
		$this->db->select('id');
		$this->db->where('active', 1);
		$query = $this->db->get('a_helpdesk_comment');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_comment_files($id) {
		$this->db->select('*');
		$this->db->where('id_comment',$id);
		$query = $this->db->get('a_comment_file');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

    function delete_files_not_use($id){
		$this->db->where('id', $id);
		$this->db->delete('a_comment_file');
	}

	function search_comment($search_string) {
		$this->select_fields = array('helpdesk_id', 'comment');
		$this->search_fields = array(
			array('comment'),
		);

		return parent::search($search_string);
	}
}