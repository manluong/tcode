<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class InvoiceItemM extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table = 'a_invoice_item';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}

	function getByInvoiceId($invoice_id) {
		$this->db->select('*');
		$this->db->where('invoice_id', $invoice_id);
		$query = $this->db->get($this->table);

		return $query->result();
	}

	function deleteByInvoiceId($invoice_id) {
		$this->db->where('invoice_id', $invoice_id);
		$this->db->delete($this->table);
	}
}