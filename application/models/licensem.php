<?php

class LicenseM extends MY_Model {

	private $rules = array();

	function __construct() {
		$this->table = 'license';
		$this->id_field = 'id';

		parent::__construct();
	}





	function setup() {
		if (APP_ROLE == 'TSUB') {
			$this->load_license_rules();
		}
	}






	function get_license($license_id) {
		if (APP_ROLE != 'TBOSS') return FALSE;

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

	function assign_license($license_id, $tenant_id, $recurring, $start, $end) {
		if (APP_ROLE != 'TBOSS') return FALSE;

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
		if (APP_ROLE != 'TBOSS') return FALSE;

		$rs = $this->db->select()
				->from('license_rules')
				->join('license', 'license.id=license_rules.license_id')
				->join('tenant_license', 'tenant_license.license_id=license.id')
				->where('tenant_license.tenant_id', $tenant_id)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		$rules = $this->consolidate_rules($rs->result_array());

		$this->load->model('TenantM');
		$tenant_db = $this->TenantM->get_db_connection($tenant_id);

		$tenant_db->insert_batch('tenant_license_rules', $rules);
	}






	function load_license_rules() {
		$rs = $this->db->select()
				->from('tenant_license_rules')
				->get();

		foreach($rs->result_array() AS $r) {
			$this->rules[$r['app_id']][$r['actiongp']][$r['rule_type']] = array(
				'value' => $r['rule_value'],
				'db_table' => $r['db_table'],
				'db_field' => $r['db_field']
			);
		}
	}

	function has_restriction($app_id, $actiongp, $rule_type) {
		return (isset($this->rules[$app_id][$actiongp][$rule_type]));
	}

	function get_restriction($app_id, $actiongp, $rule_type) {
		return $this->rules[$app_id][$actiongp][$rule_type];
	}




	private function consolidate_rules($rules) {
		$result = array();

		foreach($rules AS $r) {
			if (isset($result[$r['app_id']][$r['actiongp']][$r['rule_type']])) {
				$result[$r['app_id']][$r['actiongp']][$r['rule_type']] = array(
					'value' => $r['rule_value'],
					'db_table' => $r['db_table'],
					'db_field' => $r['db_field']
				);
			} else {
				$result[$r['app_id']][$r['actiongp']][$r['rule_type']]['value'] += $r['rule_value'];
			}
		}

		return $result;
	}

}
?>