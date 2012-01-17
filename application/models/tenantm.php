<?php

class TenantM extends MY_Model {

	function __construct() {
		$this->table = 'tenant';
		$this->id_field = 'id';

		parent::__construct();
	}


	function get_db_connection($tenant_id) {
		$tenant = $this->get($tenant_id);

		$db_config = array(
			'hostname' => $tenant['dbhost'],
			'username' => $tenant['dbuser'],
			'password' => $tenant['dbpass'],
			'database' => $tenant['dbname'],
			'dbdriver' => 'mysql',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci'
		);

		return $this->load->database($db_config, TRUE);
	}

}
?>
