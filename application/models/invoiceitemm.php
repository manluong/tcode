<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class InvoiceItemM extends MY_Model {

	function __construct() {
		parent::__construct();

		$this->table = 'a_invoice_item';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}

	function get_by_invoice_id($invoice_id) {
		$this->db->select('a_invoice_item.*, a_product.a_product_name');
		$this->db->from('a_invoice_item');
		$this->db->join('a_product', 'a_invoice_item.product_id = a_product.a_product_id');
		$this->db->where('a_invoice_item.invoice_id', $invoice_id);
		$query = $this->db->get();

		return $query->result();
	}

	function delete_by_invoice_id($invoice_id) {
		$this->db->where('invoice_id', $invoice_id);
		$this->db->delete($this->table);
	}
}