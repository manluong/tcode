<?php
//This Model is used in TBOSS, to modify data in TSUB (tenant) DB tables
class TenantM extends MY_Model {
	public $data = array();

	public $data_fields = array(
		'card_id' => array(
			'type' => 'id',
		),
		'type' => array(
			'type' => 'selection',
			'options' => array(
				0 => '',
				1 => '',
			),
		),
		'subscription_id' => array(
			'type' => 'id'
		),
		'domain' => array(
			'type' => 'text',
		),
	);

	function __construct() {
		$this->table = 'tenant';

		parent::__construct();
	}

	function setup($tenant_id) {
		if (isset($this->data['id']) && $this->data['id'] == $tenant_id) return;

		$this->data = $this->get($tenant_id);
	}

	//Clears the tenant_license_rules table before saving a new batch
	function save_license_rules($rules) {
		if (strlen($this->data['domain']) == 0) die('This tenant (id: '.$this->data['id'].') does not have a domain set up.');

		if (count($rules) == 0) return;

		$data = array();
		foreach($rules AS $app_id => $rule) {
			foreach($rule AS $rule_type => $rule_values) {
				$temp = array(
					'app_id' => $app_id,
					'rule_type' => $rule_type,
					'rule_value' => $rule_values['value'],
				);

				if (isset($rule_values['db_table'])) $temp['db_table'] = $rule_values['db_table'];
				if (isset($rule_values['db_field'])) $temp['db_field'] = $rule_values['db_field'];

				$data[] = $temp;
			}
		}

		$tenant_db_table = 't_'.$this->data['domain'];
		$this->db->query('TRUNCATE TABLE '.$tenant_db_table.'.tenant_license_rules');
		$this->db->insert_batch($tenant_db_table.'.tenant_license_rules', $data);
	}

}
?>
