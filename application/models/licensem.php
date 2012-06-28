<?php

class LicenseM extends MY_Model {

	public $data_fields = array(
		'name' => array(
			'type' => 'text',
		),
		'description' => array(
			'type' => 'text',
		),
		'duration' => array(
			'type' => 'numeric',
		),
		'fee' => array(
			'type' => 'numeric',
		),
	);

	private $rules = array();

	function __construct() {
		$this->table = 'license';

		parent::__construct();
	}





	function setup() {
		if (APP_ROLE == 'TSUB') {
			$this->load_license_rules();
		}
	}






	function get_license($license_id) {
		if (APP_ROLE != 'TBOSS' || APP_ROLE != 'TPROC') return FALSE;

		$rs = $this->db->select()
				->from($this->table)
				->where('id', $license_id)
				->limit(1)
				->get();

		$result = $rs->result_array();

		$result['rules'] = $this->get_license_rules($license_id);

		return $result;
	}

	function get_license_rules($license_id) {
		$rs = $this->db->select()
				->from('license_rules')
				->where('license_id', $license_id)
				->get();

		return $rs->result_array();
	}

	function save_license($details, $rules) {
		if (APP_ROLE != 'TBOSS') return FALSE;

		$result = $this->save($details, 'id');

		$this->table = 'license_rules';
		foreach($rules AS $r) {
			$this->save($r, 'id');
		}
		$this->table = 'license';

		return $result;
	}

	function get_license_id($license_name) {
		$rs = $this->db->select('id')
				->from($this->table)
				->where('name', $license_name)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$result = $rs->row_array();

		return $result['id'];
	}

	function assign_license($license_id_or_name, $tenant_id, $recurring=0, $start='', $end='') {
		if (APP_ROLE != 'TBOSS' || APP_ROLE != 'TPROC') return FALSE;

		if (is_numeric($license_id_or_name)) {
			$license_id = $license_id_or_name;
		} else {
			$license_id = $this->get_license_id($license_id_or_name);
		}

		if ($start == '') $start = get_current_stamp();

		$data = array(
			'license_id' => $license_id,
			'tenant_id' => $tenant_id,
			'recurring' => $recurring,
			'start_stamp' => $start,
			'end_stamp' => $end,
			'deleted' => 0
		);

		$this->db->insert('tenant_license', $data);
	}

	function export_license_rules($tenant_id) {
		if (APP_ROLE != 'TBOSS' || APP_ROLE != 'TPROC') return FALSE;

		$rs = $this->db->select()
				->from('license_rules AS r')
				->join('license AS l', 'l.id=r.license_id')
				->join('tenant_license AS tl', 'tl.license_id=l.id')
				->where('tl.tenant_id', $tenant_id)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$rules = $this->consolidate_rules($rs->result_array());

		$this->load->model('TenantM');
		$this->TenantM->setup($tenant_id);
		$this->TenantM->save_license_rules($rules);
	}






	function load_license_rules() {
		$rs = $this->db->select()
				->from('tenant_license_rules')
				->get();

		foreach($rs->result_array() AS $r) {
			$this->rules[$r['app_id']][$r['rule_type']] = array(
				'value' => $r['rule_value'],
				'db_table' => $r['db_table'],
				'db_field' => $r['db_field']
			);
		}
	}

	function has_restriction($app_id, $rule_type) {
		return (isset($this->rules[$app_id][$rule_type]));
	}

	function get_restriction($app_id, $rule_type) {
		return $this->rules[$app_id][$rule_type];
	}

	function get_accessible_app_ids() {
		$rs = $this->db->select('app_id')
				->from('tenant_license_rules')
				->where('rule_type', 'access')
				->where('rule_value', 1)
				->get();

		if ($rs->num_rows == 0) return array();

		$results = array();
		foreach($rs->result_array() AS $row) {
			$results[] = $row['app_id'];
		}

		return $results;
	}



	private function consolidate_rules($rules) {
		$result = array();

		foreach($rules AS $r) {
			if (!isset($result[$r['app_id']][$r['rule_type']])) {
				$result[$r['app_id']][$r['rule_type']] = array(
					'value' => $r['rule_value'],
					'db_table' => $r['db_table'],
					'db_field' => $r['db_field']
				);
			} else {
				$result[$r['app_id']][$r['rule_type']]['value'] += $r['rule_value'];
			}
		}

		return $result;
	}

}
?>
