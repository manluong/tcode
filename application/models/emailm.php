<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class EmailM extends MY_Model {

	function __construct() {
		$this->sett_has_system_fields = FALSE;
		$this->table = 'email_sent';

		parent::__construct();
	}

	function create_log($data) {
		$this->db->insert('email_sent_log', $data);

		return $this->db->insert_id();
	}

	function insert_new_email($data) {
		$this->db->insert($this->table, $data);

		return $this->db->insert_id();
	}

	function update_log($id, $data) {
		$this->db->where('id', $id)
			->update('email_sent_log', $data);

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

	function update_status($email_sent_log_id, $email, $event, $timestamp) {
		$data = array(
			'event' => $event,
			'event_stamp' => parse_timestamp($timestamp, 'MYSQL'),
		);

		return $this->db->where('email_sent_log_id', $email_sent_log_id)
						->where('to', $email)
						->update($this->table, $data);
	}

	// Functions for email email_parser
	function save_received_email($data) {
		$this->db->insert('email_received', $data);

		return $this->db->insert_id();
	}
}