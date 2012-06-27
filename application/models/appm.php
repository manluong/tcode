<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class AppM extends MY_Model {
	var $url = array();
	var $actions = array();

	var $app_cache = array(
		0 => array('id'=>0, 'name'=>'general'),
		'general' => array('id'=>0, 'name'=>'general'),
	);

	//Hardcoded APP_ID <=> MODEL_NAME
	var $app_model = array(
		15 => 'InvoiceM',
		39 => 'HelpdeskM',
	);

	var $acl_app_list = array(); //An ACL/Licensed controlled App List

	function __construct() {
		$this->table = 'global_setting.core_apps';
		$this->sett_has_system_fields = FALSE;

		parent::__construct();
	}

	function setup() {
		$this->acl_app_list = $this->get_apps();
	}

	function get($app_id_or_name) {
		if (isset($this->app_cache[$app_id_or_name])) return $this->app_cache[$app_id_or_name];

		$this->db->select()
			->from($this->table);

		if (is_numeric($app_id_or_name)) {
			$this->db->where('name', $app_id_or_name);
		} else {
			$this->db->where('id', $app_id_or_name);
		}

		$rs = $this->db->limit(1)
				->get();

		if ($rs->num_rows()==0) return FALSE;

		$result = $rs->row_array();

		$this->app_cache[$result['id']] = $result;
		$this->app_cache[$result['name']] = $result;

		return $result;
	}

	function must_disable_plain_id($app_id_or_name) {
		if (isset($this->app_cache[$app_id_or_name])) return $this->app_cache[$app_id_or_name]['use_encoded_id'];

		$result = $this->get($app_id_or_name);

		return $result['use_encoded_id'];
	}

	function get_status($app_id_or_name) {
		if (isset($this->app_cache[$app_id_or_name])) return $this->app_cache[$app_id_or_name]['status'];

		$result = $this->get($app_id_or_name);

		return $result['status'];
	}

	function get_id($app_name) {
		if (isset($this->app_cache[$app_name])) return $this->app_cache[$app_name]['id'];

		$result = $this->get($app_name);

		if ($result === FALSE) return 0;

		return $result['id'];
	}

	function get_name($app_id) {
		if (isset($this->app_cache[$app_id])) return $this->app_cache[$app_id]['name'];

		$result = $this->get($app_id);

		return $result['name'];
	}

	function get_language_name($app_id) {
		if (isset($this->app_cache[$app_id])) return $this->app_cache[$app_id]['name'];

		$result = $this->get($app_id);

		return $this->lang->line('core_apps-name-'.$result['name']);
	}

	//Get list of Apps, with License restrictions applied.
	//TODO: add ACL restriction.
	function get_apps() {
		if (!$this->UserM->is_logged_in()) return array();

		if (APP_ROLE == 'TSUB') {
			$accessible_app_ids = $this->LicenseM->get_accessible_app_ids();
			if (count($accessible_app_ids) == 0) return array();
		}

		$this->db->select()
			->from('global_setting.core_apps')
			->where('status', 1);

		if (APP_ROLE == 'TSUB') {
			$this->db->where_in('id', $accessible_app_ids);
		}

		$rs = $this->db->order_by('sort_order', 'ASC')
				->get();

		if ($rs->num_rows() == 0) return array();
		$results = array();
		foreach($rs->result_array() AS $row) {
			if (!$this->AclM->check($row['name'])) continue;

			$results[] = $row;

			$this->app_cache[$row['id']] = $row;
			$this->app_cache[$row['name']] = $row;
		}
		return $results;
	}

	function get_model($app_id) {
		if (!isset($this->app_model[$app_id])) return FALSE;

		return $this->app_model[$app_id];
	}
}