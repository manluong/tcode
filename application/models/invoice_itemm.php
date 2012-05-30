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
		'tax_id' => array(
			'type' => 'id'
		),
		'price_type' => array(
			'type' => 'id'
		),
		'description' => array(
			'type' => 'text'
		),
		'unit_price' => array(
			'type' => 'numeric'
		),
		'quantity' => array(
			'type' => 'numeric'
		),
		'discount' => array(
			'type' => 'text'
		),
		'total' => array(
			'type' => 'numeric'
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

	function get_by_invoice_id($invoice_id) {
		$this->db->select('a_invoice_item.*, a_product.a_product_name, a_product_pricetype.a_product_pricetype_name, a_product_durationtype.a_product_durationtype_name');
		$this->db->from('a_invoice_item');
		$this->db->join('a_product', 'a_invoice_item.product_id = a_product.a_product_id');
		$this->db->join('a_product_pricetype', 'a_invoice_item.price_type = a_product_pricetype.a_product_pricetype_id', 'left');
		$this->db->join('a_product_durationtype', 'a_invoice_item.duration_type = a_product_durationtype.a_product_durationtype_id', 'left');
		$this->db->where('a_invoice_item.invoice_id', $invoice_id);
		$query = $this->db->get();

		return $query->result();
	}

	function delete_by_invoice_id($invoice_id) {
		$this->db->where('invoice_id', $invoice_id);
		$this->db->delete($this->table);
	}
}
