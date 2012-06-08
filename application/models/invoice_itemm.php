<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Invoice_ItemM extends MY_Model {

	public $data_fields = array(
		'invoice_id' => array(
			'type' => 'id'
		),
		'product_id' => array(
			'type' => 'id'
		),
		'subscription_id' => array(
			'type' => 'id'
		),
		'tax_use_id' => array(
			'type' => 'id'
		),
		'price_type' => array(
			'type' => 'id'
		),
		'description' => array(
			'type' => 'text'
		),
		'unit_price' => array(
			'type' => 'numeric',
			'required' => true,
			'allow_blank' => false
		),
		'quantity' => array(
			'type' => 'numeric',
			'required' => true,
			'allow_blank' => false
		),
		'discount' => array(
			'type' => 'text'
		),
		'total' => array(
			'type' => 'numeric',
			'required' => true,
			'allow_blank' => false
		),
		'sort_order' => array(
			'type' => 'numeric',
			'required' => true,
			'allow_blank' => false
		),
		'subscription_start_stamp' => array(
			'type' => 'datetime'
		),
		'subscription_end_stamp' => array(
			'type' => 'datetime'
		),
		'duration_type' => array(
			'type' => 'id'
		),
		'acc_code' => array(
			'type' => 'text'
		)
	);

	function __construct() {
		parent::__construct();

		$this->table = 'a_invoice_item';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}
}