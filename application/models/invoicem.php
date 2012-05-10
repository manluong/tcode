<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class InvoiceM extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table = 'a_invoice';
		$this->cache_enabled = TRUE;
	}

	function search($param) {
		if (array_key_exists('customer_id', $param) && $param['customer_id']) {
			$this->db->where('customer_card_id =', $param['customer_id']);
		} else {
			if (array_key_exists('customer_name', $param) && $param['customer_name']) {
				$this->db->where('card.nickname LIKE', '%'.$param['customer_name'].'%');
			}
		}
		if (array_key_exists('date_range_from', $param) && $param['date_range_from']) {
			$this->db->where('payment_due_stamp >=', $param['date_range_from']);
		}
		if (array_key_exists('date_range_to', $param) && $param['date_range_to']) {
			$this->db->where('payment_due_stamp <=', $param['date_range_to']);
		}
		if (array_key_exists('invoice_id', $param) && $param['invoice_id']) {
			$this->db->where('a_invoice.id =', $param['invoice_id']);
		}
		if (array_key_exists('po_number', $param) && $param['po_number']) {
			$this->db->where('purchase_order_number =', $param['po_number']);
		}
		if (array_key_exists('notes', $param) && $param['notes']) {
			$this->db->where('memo LIKE', '%'.$param['notes'].'%');
		}

		$this->db->select('a_invoice.*, card.nickname, (SELECT SUM(a_invoice_item.total) FROM a_invoice_item WHERE a_invoice_item.invoice_id = a_invoice.id) AS total');
		$this->db->from('a_invoice');
		$this->db->join('card', 'a_invoice.customer_card_id = card.id', 'left');
		$this->db->where('a_invoice.deleted', 0);
		$query = $this->db->get();

		return $query->result();
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

	function getProductByName($name) {
		$this->db->select('*');
		$this->db->from('a_product');
		$this->db->join('a_product_price', 'a_product_price.a_product_price_productid = a_product.a_product_id');
		$this->db->like('a_product.a_product_name', $name, 'after');
		$query = $this->db->get();

		return $query->result();
	}
}