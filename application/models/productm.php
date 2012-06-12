<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class ProductM extends MY_Model {

	public $data_fields = array(
		'category_id' => array(
			'type' => 'id',
			'required' => true,
			'allow_blank' => false
		),
		'name' => array(
			'type' => 'text',
			'required' => true,
			'allow_blank' => false
		),
		'sku' => array(
			'type' => 'text',
			'required' => true,
			'allow_blank' => false
		),
		'type' => array(
			'type' => 'text'
		),
		'status' => array(
			'type' => 'text'
		),
		'charge_type' => array(
			'type' => 'text'
		),
		'tax_id' => array(
			'type' => 'id'
		)
	);

	function __construct() {
		parent::__construct();

		$this->app = 'product';
		$this->table = 'a_product';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}
}