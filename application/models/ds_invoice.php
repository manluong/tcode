<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class DS_Invoice extends DatasetM {
	function __construct() {
		parent::__construct();

		$this->load('ds_invoice');
	}

}