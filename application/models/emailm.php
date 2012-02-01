<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class EmailM extends MY_Model {

	function __construct() {
		parent::__construct();
	}

	function insert_new_email($data) {
		$this->db->insert('email', $data);
		return $this->db->insert_id();
	}

	function update_email($insert_id, $data) {
		$this->db->where('id', $insert_id)
			->update('email', $data);
		return $this->db->affected_rows();
	}

	function check_bcc () {
		$query = $this->db->select('alwaysbcc')
			->from('email_generalsetting')
			->get();
		$bcc = $query->row_array();
		return (isset($bcc['alwaysbcc']) && $bcc['alwaysbcc'] !== '') ? $bcc['alwaysbcc'] : '';
	}

	function get_emailaddress_from_type($type, $type_id) {
		foreach ($type_id as $id) {
			if ($type === 'client') {
				$query = $this->db->select('card_name_fname, card_email_add')
				->from('card_email')
				->join('client', 'client.client_cardid = card_email.card_email_cardid')
				->join('card_name', 'client.client_cardid = card_name.card_name_cardid')
				->where('client_id', $id)
				->get();
			} elseif ($type === 'card') {
				$query = $this->db->select('card_name_fname, card_email_add')
					->from('card_email')
					->join('card_name', 'card_email.card_email_cardid = card_name.card_name_cardid')
					->where('card_email_cardid', $id)
					->get();
			}
			$i = $query->row_array();
			$j['to'][] = $i['card_email_add'];
			$j['toname'][] = $i['card_name_fname'];
		}
		return $j;
	}

	function get_from_address() {
		$query = $this->db->select('fromaddress')
			->from('email_addresssetting')
			->get();
		$i = $query->row_array();
		if (empty ($i)) {
			$query = $this->db->select('defaultfromaddress')
			->from('email_generalsetting')
			->get();
			$i = $query->row_array();
		}
		if (isset($i['fromaddress']) && $i['fromaddress'] !== '') return $i['fromaddress'];
		if (isset($i['defaultfromaddress']) && $i['defaultfromaddress'] !== '') return $i['defaultfromaddress'];
		return '';
	}

	function get_from_name() {
		$query = $this->db->select('fromname')
			->from('email_addresssetting')
			->get();
		$i = $query->row_array();
		if (empty ($i)) {
			$query = $this->db->select('defaultfromname')
			->from('email_generalsetting')
			->get();
			$i = $query->row_array();
		}
		if (isset($i['fromname']) && $i['fromname'] !== '') return $i['fromname'];
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
		if ($query->num_rows !== 0) {
			$i = $query->result_array();
			return $i[0]['result'];
		}
	}

	function update_email_events_result($data, $email_id) {
		$this->db->where('id', $email_id)
			->update('email', $data);
		return $this->db->affected_rows();
	}

	// Functions for email email_parser
	function save_received_email($data) {
		$this->db->insert('email_received', $data);
		return $this->db->insert_id();
	}
}