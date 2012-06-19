<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class EmailM extends MY_Model {

	function __construct() {
		$this->sett_has_system_fields = FALSE;
		$this->table = 'email';

		parent::__construct();
	}

	function create_log($data) {
		$this->db->insert('log_email', $data);

		return $this->db->insert_id();
	}

	function insert_new_email($data) {
		$this->db->insert('email', $data);

		return $this->db->insert_id();
	}

	function update_log($id, $data) {
		$this->db->where('id', $id)
			->update('log_email', $data);

		return $this->db->affected_rows();
	}

	function get_template_content($template) {
		$query = $this->db->select('content')
			->from('email_template')
			->where('name', $template)
			->get();
		$i = $query->row_array();

		return ( ! empty($i)) ? $i['content'] : '';
	}

	function update_status($log_email_id, $email, $event, $timestamp) {
		$data = array(
			'event' => $event,
			'event_stamp' => parse_timestamp($timestamp, 'MYSQL'),
		);

		return $this->db->where('log_email_id', $log_email_id)
						->where('to', $email)
						->update('email', $data);
	}

	// Functions for email email_parser
	function save_received_email($data) {
		$this->db->insert('email_received', $data);

		return $this->db->insert_id();
	}
}