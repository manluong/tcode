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

	function check_bcc() {
		$query = $this->db->select('alwaysbcc')
			->from('email_generalsetting')
			->get();
		$bcc = $query->row_array();

		return (isset($bcc['alwaysbcc']) && $bcc['alwaysbcc'] !== '') ? $bcc['alwaysbcc'] : '';
	}

	function get_emailaddress_from_type($type, $type_id) {
		$result = array();

		foreach ($type_id as $id) {
			$this->db->select('card_name_fname, card_email_add')
					->from('card_email');

			if ($type === 'client') {
				$this->db->join('client', 'client.client_cardid = card_email.card_email_cardid')
					->join('card_name', 'client.client_cardid = card_name.card_name_cardid')
					->where('client_id', $id);
			} elseif ($type === 'card') {
				$this->db->join('card_name', 'card_email.card_email_cardid = card_name.card_name_cardid')
					->where('card_email_cardid', $id);
			}

			$query = $this->db->get();

			$i = $query->row_array();

			$result['to'][] = $i['card_email_add'];
			$result['toname'][] = $i['first_name'];
		}

		return $result;
	}

	function get_from_address() {
		$rs = $this->db->select('fromaddress')
				->from('email_addresssetting')
				->get();
		$i = $rs->row_array();
		if (isset($i['fromaddress']) && $i['fromaddress'] !== '') return $i['fromaddress'];

		$rs = $this->db->select('defaultfromaddress')
				->from('email_generalsetting')
				->get();
		$i = $rs->row_array();
		if (isset($i['defaultfromaddress']) && $i['defaultfromaddress'] !== '') return $i['defaultfromaddress'];

		return '';
	}

	function get_from_name() {
		$rs = $this->db->select('fromname')
				->from('email_addresssetting')
				->get();
		$i = $rs->row_array();
		if (isset($i['fromname']) && $i['fromname'] !== '') return $i['fromname'];

		$rs = $this->db->select('defaultfromname')
				->from('email_generalsetting')
				->get();
		$i = $rs->row_array();
		if (isset($i['defaultfromname']) && $i['defaultfromname'] !== '') return $i['defaultfromname'];

		return '';
	}

	function get_template_content($template) {
		$query = $this->db->select('content')
			->from('email_template')
			->where('name', $template)
			->get();
		$i = $query->row_array();

		return ( ! empty($i)) ? $i['content'] : '';
	}

	// Functions for email events_parser
	function get_result_arr($id) {
		$query = $this->db->get_where('email', array('id' => $id), 1);

		if ($query->num_rows == 0) return FALSE;

		$i = $query->row_array();
		return $i['result'];
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