<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class InvoiceM extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table = 'a_invoice';
		$this->cache_enabled = TRUE;
	}

	function search($param) {
		if (array_key_exists('customer_id', $param) && $param['customer_id']) {
			$this->where[] = array('customer_card_id =' => $param['customer_id']);
		}
		if (array_key_exists('customer_name', $param) && $param['customer_name']) {

		}
		if (array_key_exists('date_range_from', $param) && $param['date_range_from']) {
			$this->where[] = array('payment_due_stamp >=' => $param['date_range_from']);
		}
		if (array_key_exists('date_range_to', $param) && $param['date_range_to']) {
			$this->where[] = array('payment_due_stamp <=' => $param['date_range_to']);
		}
		if (array_key_exists('invoice_id', $param) && $param['invoice_id']) {
			$this->where[] = array('id =' => $param['invoice_id']);
		}
		if (array_key_exists('po_number', $param) && $param['po_number']) {
			$this->where[] = array('purchase_order_number =' => $param['po_number']);
		}
		if (array_key_exists('notes', $param) && $param['notes']) {
			$this->where[] = array('memo LIKE' => '%'.$param['notes'].'%');
		}

		return $this->get_list();
	}

	function getCustomer() {
		$this->db->select('id, nickname');
		$query = $this->db->get('card');

		return $query->result();
	}

	function getTax() {
		//$this->db->select('set_tax_id, set_tax_name');
		//$query = $this->db->get('set_tax');

		//return $query->result();

		$results = array();
		$results[1] = 1;
		$results[2] = 2;

		return $results;
	}

	function getTerms() {
		$this->db->select('id, name, content');
		$query = $this->db->get('a_invoice_terms');

		return $query->result();
	}

	function getTermsById($id) {
		$this->db->select('id, name, content');
		$this->db->where('id', $id);
		$query = $this->db->get('a_invoice_terms');

		$result = $query->result();
		if ($result) {
			return $result[0];
		} else {
			return false;
		}
	}

	function getCustomerById($id) {
		$this->db->select('id, nickname');
		$this->db->where('id', $id);
		$query = $this->db->get('card');

		$result = $query->result();
		if ($result) {
			return $result[0];
		} else {
			return false;
		}
	}

	function getCustomerByName($name) {
		$this->db->select('id, nickname');
		$this->db->like('nickname', $name, 'after');
		$query = $this->db->get('card');

		return $query->result();
	}
}