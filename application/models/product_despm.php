<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Product_DespM extends MY_Model {

	public $data_fields = array(
		'product_id' => array(
			'type' => 'id'
		),
		'lang' => array(
			'type' => 'text'
		),
		'content' => array(
			'type' => 'text',
			'required' => true,
			'allow_blank' => false
		)
	);

	function __construct() {
		parent::__construct();

		$this->app = 'product';
		$this->table = 'a_product_desp';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}
}