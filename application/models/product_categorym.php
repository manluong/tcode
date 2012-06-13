<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Product_CategoryM extends MY_Model {

	public $data_fields = array(
		'parent_id' => array(
			'type' => 'id',
			'required' => true,
			'allow_blank' => false
		),
		'name' => array(
			'type' => 'text',
			'required' => true,
			'allow_blank' => false
		),
		'extra' => array(
			'type' => 'text'
		)
	);

	function __construct() {
		parent::__construct();

		$this->app = 'product';
		$this->table = 'a_product_category';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}

	function get_sub_category($parent_id) {
		$this->set_where('parent_id = '.$parent_id);
		return $this->get_list();
	}
}