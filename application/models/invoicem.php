<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class InvoiceM extends MY_Model {

	public $data_fields = array(
		'customer_card_id' => array(
			'type' => 'id'
		),
		'invoice_stamp' => array(
			'type' => 'datetime'
		),
		'payment_due_stamp' => array(
			'type' => 'datetime'
		),
		'currency' => array(
			'type' => 'text'
		),
		'tax_id' => array(
			'type' => 'id'
		),
		'purchase_order_number' => array(
			'type' => 'text'
		),
		'acc_code' => array(
			'type' => 'text'
		),
		'memo' => array(
			'type' => 'text'
		),
		'terms_id' => array(
			'type' => 'id'
		),
		'terms_content' => array(
			'type' => 'text'
		)
	);

	function __construct() {
		parent::__construct();

		$this->table = 'a_invoice';
		$this->cache_enabled = TRUE;
	}

	function search($param, $count = false) {
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
		if (array_key_exists('total_min', $param) && $param['total_min']) {
			$this->db->where('invoice_total.total >=', $param['total_min']);
		}
		if (array_key_exists('total_max', $param) && $param['total_max']) {
			$this->db->where('invoice_total.total <=', $param['total_max']);
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

		$this->db->select('a_invoice.*, card.nickname, invoice_total.total');
		$this->db->from('a_invoice');
		$this->db->join('card', 'a_invoice.customer_card_id = card.id', 'left');
		$this->db->join('(SELECT invoice_id, SUM(total) AS total FROM a_invoice_item GROUP BY invoice_id) invoice_total', 'a_invoice.id = invoice_total.invoice_id', 'left');
		$this->db->where('a_invoice.deleted', 0);

		if ($count) {
			return $this->db->count_all_results();
		} else {
			if (array_key_exists('row_per_page', $param) && $param['row_per_page']) {
				if ($param['row_per_page'] != '-1') {
					$this->db->limit($param['row_per_page'], ($param['page']-1)*$param['row_per_page']);
				}
			}

			$query = $this->db->get();

			return $query->result();
		}
	}

	function get_customer() {
		$this->db->select('id, nickname');
		$query = $this->db->get('card');

		return $query->result();
	}

	function get_tax() {
		//$this->db->select('set_tax_id, set_tax_name');
		//$query = $this->db->get('set_tax');

		//return $query->result();

		$results = array();
		$results[1] = 1;
		$results[2] = 2;

		return $results;
	}

	function get_price_type() {
		$this->db->select('*');
		$query = $this->db->get('a_product_pricetype');

		return $query->result();
	}

	function get_duration_type() {
		$this->db->select('*');
		$query = $this->db->get('a_product_durationtype');

		return $query->result();
	}

	function get_terms() {
		$this->db->select('*');
		$query = $this->db->get('a_invoice_terms');

		return $query->result();
	}

	function get_terms_by_id($id) {
		$this->db->select('*');
		$this->db->where('id', $id);
		$query = $this->db->get('a_invoice_terms');

		$result = $query->result();
		if ($result) {
			return $result[0];
		} else {
			return false;
		}
	}

	function get_customer_by_id($id) {
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

	function get_customer_by_name($name) {
		$this->db->select('id, nickname');
		$this->db->like('nickname', $name);
		$query = $this->db->get('card');

		return $query->result();
	}

	function get_product_by_name($name) {
		$this->db->select('*');
		$this->db->from('a_product');
		$this->db->join('a_product_price', 'a_product_price.a_product_price_productid = a_product.a_product_id');
		$this->db->like('a_product.a_product_name', $name);
		$query = $this->db->get();

		return $query->result();
	}

	function get_min_max_invoice_total() {
		$this->db->select('*, (SELECT SUM(a_invoice_item.total) FROM a_invoice_item WHERE a_invoice_item.invoice_id = a_invoice.id) AS total');
		$this->db->where('deleted', 0);
		$this->db->order_by('total', 'asc');
		$query = $this->db->get('a_invoice');

		$min = 0;
		$max = 0;
		$result = $query->result();
		if ($result) {
			$min = (float)($result[0]->total ? $result[0]->total : 0);
			$max = (float)($result[count($result) - 1]->total ? $result[count($result) - 1]->total : 0);
		}

		return array('min' => $min, 'max' => $max);
	}
}