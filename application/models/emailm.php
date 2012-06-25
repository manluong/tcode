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

	function get_template_list() {
		$results = $this->db->select('t.id, t.app_id, t.name, a.name AS app_name')
					->from('email_template AS t')
					->join('global_setting.core_apps AS a', 'a.id=t.app_id', 'left')
					->order_by('t.sort_order', 'ASC')
					->get()
					->result_array();

		foreach($results AS $k => $v) {
			$app_lang = $this->lang->line('core_apps-name-'.$v['app_name']);
			$template_lang = $this->lang->line('email_template-'.$v['name']);
			$results[$k]['display_name'] = $app_lang.' > '.$template_lang;
		}

		return $results;
	}

	//TODO: change to use app_name + template_name?
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

	//Checks the email_routing DBTable for the app_id responsible for an email address
	function get_app_id($email) {
		//if the email is not from the default_email_domain, it is a custom domain.
		$default_email_domain = $this->eightforce_config['default_email_domain'];
		$is_custom = (substr($email, 0-strlen($default_email_domain)) !== $default_email_domain) ? 1 : 0;

		if ($is_custom == 1) {
			$email_name = $email;
		} else {
			$e = explode('@', $email);
			$email_name = $e[0];
		}

		$row = $this->db->select('app_id')
				->from('email_routing')
				->where('is_custom', $is_custom)
				->where('email_name', $email_name)
				->limit(1)
				->get()
				->row_array();

		return $row['app_id'];
	}
}