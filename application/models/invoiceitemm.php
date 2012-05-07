<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class InvoiceItemM extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table = 'a_invoice_item';
		$this->cache_enabled = TRUE;
	}

	function getByInvoiceId($id) {
		$this->db->select('*');
		$this->db->where('invoice_id', $id);
		$query = $this->db->get($this->table);

		return $query->result();
	}
}