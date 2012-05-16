<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class HelpdeskM extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table = 'a_helpdesk';
		$this->cache_enabled = TRUE;
		$this->sett_filter_deleted = FALSE;
	}

	public $data_fields = array(
		'subject' => array(
		),
		'group' => array(
		),
                'status' => array(
		),
                'type' => array(
		),
                'rate' => array(
		),
                'in_charge_card_id' => array(
		),
                'cc_email' => array(
		),
                'assign_id' => array(
		),
                'active' => array(
		),
	);

	function getTotalRecord() {
		$this->db->select('id');
		$query = $this->db->get($this->table);

		foreach ($query->result() as $row) {
			$result[] = $row;
		}

		if (!empty($result)) {
			return count($result);
		} else {
			return false;
		}
	}

	function get_content($id) {
		$this->db->select('*');
		$this->db->where('id',$id);
		$query = $this->db->get($this->table);

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function search_content($value='',$limit=10) {
		$this->db->select('*');
		if ($value !='') {
			$this->db->like('subject',$value);
		}
		$this->db->limit($limit);
		$query = $this->db->get($this->table);
		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function group_fillter($value) {
		$this->db->select('*');
		$this->db->where('group', $value);
		$query = $this->db->get($this->table);

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function status_fillter($value) {
		$this->db->select('*');
		$this->db->where('status', $value);
		$query = $this->db->get($this->table);

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function type_fillter($value) {
		$this->db->select('*');
		$this->db->where('type', $value);
		$query = $this->db->get($this->table);

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function priority_fillter($value) {
		$this->db->select('*');
		$this->db->where('priority', $value);
		$query = $this->db->get($this->table);

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function insert_upload_file($filename , $id_helpdesk) {
		$data = array (
			'filename' => $filename,
			'id_helpdesk' => $id_helpdesk
		);
		if ($this->db->insert('a_helpdesk_file',$data)) {
			return $this->db->insert_id();
		} else {
			return 0;
		}
	}

	function get_helpdesk_not_use() {
		$this->db->select('id');
		$this->db->where('active', 1);
		$query = $this->db->get($this->table);

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_helpdesk_files($id) {
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
}