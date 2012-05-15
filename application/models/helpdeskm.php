<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class HelpdeskM extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table = 'a_helpdesk';
		$this->cache_enabled = TRUE;
		$this->sett_filter_deleted = FALSE;
	}

	function get_content($id) {
		return $this->get($id);
	}

	function search_content($value='', $limit=10) {
		$this->db->select('*');

		if ($value !='') {
			$this->db->like('subject', $value);
		}

		$this->db->limit($limit);
		$query = $this->db->get($this->table);

		if ($query->num_rows() == 0) return FALSE;

		return $query->result();
	}

	function group_fillter($value) {
		$this->db->select('*');
		$this->db->where('group', $value);
		$query = $this->db->get($this->table);

		if ($query->num_rows() == 0) return FALSE;

		return $query->result();
	}

	function status_fillter($value) {
		$this->db->select('*');
		$this->db->where('status', $value);
		$query = $this->db->get($this->table);

		if ($query->num_rows() == 0) return FALSE;

		return $query->result();
	}

	function type_fillter($value) {
		$this->db->select('*');
		$this->db->where('type', $value);
		$query = $this->db->get($this->table);

		if ($query->num_rows() == 0) return FALSE;

		return $query->result();
	}

	function priority_fillter($value) {
		$this->db->select('*');
		$this->db->where('priority', $value);
		$query = $this->db->get($this->table);

		if ($query->num_rows() == 0) return FALSE;

		return $query->result();
	}

	function insert_upload_file($filename, $id_helpdesk) {
		$data = array (
			'filename' => $filename,
			'id_helpdesk' => $id_helpdesk
		);

		if ($this->db->insert('a_helpdesk_file', $data)) {
			return $this->db->insert_id();
		} else {
			return 0;
		}
	}

	function get_helpdesk_not_use() {
		$this->db->select('id');
		$this->db->where('active', 1);
		$query = $this->db->get($this->table);

		if ($query->num_rows() == 0) return FALSE;

		return $query->result();
	}

	function get_helpdesk_files($id){
		$this->db->select('*');
		$this->db->where('id_helpdesk', $id);
		$query = $this->db->get('a_helpdesk_file');

		if ($query->num_rows() == 0) return FALSE;

		return $query->result();
	}

	function delete_files_not_use($id){
		$this->db->where('id', $id);
		$this->db->delete('a_helpdesk_file');
	}
}