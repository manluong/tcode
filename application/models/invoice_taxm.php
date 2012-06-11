<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Invoice_TaxM extends MY_Model {

	public $data_fields = array(
		'invoice_id' => array(
			'type' => 'id'
		),
		'tax_id' => array(
			'type' => 'id'
		),
		'amount' => array(
			'type' => 'numeric'
		)
	);

	function __construct() {
		parent::__construct();

		$this->app = 'invoice';
		$this->table = 'a_invoice_tax';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}
}