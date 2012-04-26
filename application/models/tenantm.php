<?php

class TenantM extends MY_Model {
	var $data = array();

	function __construct() {
		$this->table = 'tenant';
		$this->id_field = 'id';

		parent::__construct();
	}

	function setup($tenant_id) {
		if (isset($this->data['id']) && $this->data['id'] == $tenant_id) return;

		$this->data = $this->get($tenant_id);
	}

	function save_license_rules($rules) {
		if (strlen($this->data['domain'] == 0)) die('This tenant (id: '.$this->data['id'].') does not have a domain set up.');

		$this->db->insert_batch('t_'.$this->data['tenant_domain'].'.tenant_license_rules', $rules);
	}

}
?>
