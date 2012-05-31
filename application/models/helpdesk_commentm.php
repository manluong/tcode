<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Helpdesk_CommentM extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table = 'a_helpdesk_comment';
		$this->cache_enabled = TRUE;
        $this->sett_filter_deleted = FALSE;
	}

	function get_list() {
		$this->db->select('*');
		$query = $this->db->get('a_helpdesk');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_group() {
		$this->db->select('*');
		$query = $this->db->get('access_ro');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_status() {
		$this->db->select('*');
		$query = $this->db->get('a_status');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_type() {
		$this->db->select('*');
		$query = $this->db->get('a_type');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_priority() {
		$this->db->select('*');
		$query = $this->db->get('a_priority');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
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

	function get_content($id) {
		$this->db->select('*');
		$this->db->where('helpdesk_id',$id);
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
		$this->db->select('display_name');
		$this->db->where('id',$id);
		$query = $this->db->get('card');

		if ($query->result()) {
			$tmp = $query->result();
			if (!empty($tmp)) {
				foreach($tmp as $k) {
					return $k->display_name;
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