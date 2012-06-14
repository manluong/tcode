<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Product_PriceM extends MY_Model {

	public $data_fields = array(
		'product_id' => array(
			'type' => 'id'
		),
		'pricelist_id' => array(
			'type' => 'id'
		),
		'currency' => array(
			'type' => 'id'
		),
		'price_type_id' => array(
			'type' => 'id'
		),
		'amount' => array(
			'type' => 'numeric',
			'required' => true,
			'allow_blank' => false
		),
		'quantity' => array(
			'type' => 'id'
		),
		'duration_type' => array(
			'type' => 'id'
		)
	);

	function __construct() {
		parent::__construct();

		$this->app = 'product';
		$this->table = 'a_product_price';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}
}