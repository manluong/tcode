<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class InvoiceItemM extends MY_Model {

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