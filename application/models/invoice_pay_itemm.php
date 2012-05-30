<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Invoice_Pay_ItemM extends MY_Model {

	public $data_fields = array(
		'invoice_pay_id' => array(
			'type' => 'id'
		),
		'invoice_id' => array(
			'type' => 'id'
		),
		'amount' => array(
			'type' => 'numeric'
		)
	);

	function __construct() {
		parent::__construct();

		$this->table = 'a_invoice_payitem';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}
}
