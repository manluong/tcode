<?php

class TenantM extends MY_Model {

	function __construct() {
		$this->table = 'tenant';
		$this->id_field = 'id';

		parent::__construct();
	}




}
?>
