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

	public $sett_fill_item = TRUE;

	private $addons = array(
		'item' => 'InvoiceItemM'
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

	function save($data = FALSE) {
		$has_error = FALSE;

		if ($data === FALSE) $data = $this->get_form_data();
		$is_new = !(isset($data[$this->id_field]) && $data[$this->id_field] !== FALSE);

		//filter out any addon data
		$card = array();
		foreach($data AS $k=>$v) {
			$k = str_replace('addon_', '', $k);
			if (in_array($k, array_keys($this->addons))) continue;
			$card[$k] = $v;
		}
		$invoice_id = parent::save($card);

		if ($invoice_id === FALSE) $has_error = TRUE;

		foreach($this->addons AS $name=>$model) {
			$form_addon = FALSE;
			if (isset($data['addon_'.$name])) $form_addon = $data['addon_'.$name];			//check if it's in $data
			if ($form_addon === FALSE) $form_addon = $this->input->post('addon_'.$name);	//if not, check if it's post var
			if ($form_addon === FALSE) continue;											//if not, skip to next addon

			if ($is_new) {
				foreach($form_addon AS $fa) {
					if ($this->is_empty_array($fa)) continue;

					$addon_set = array(
						'invoice_id' => $invoice_id
					);

					foreach($this->$model->data_fields AS $key=>$detail) {
						if (isset($fa[$key])) $addon_set[$key] = $fa[$key];
					}

					$id = $this->$model->save($addon_set);

					if ($id === FALSE) {
						$this->errors[] = $this->$model->get_error_string();
						$this->field_errors['addon_'.$name] = $this->$model->field_errors;
						$has_error = TRUE;
					}
				}
			} else {
				$this->$model->where[] = 'invoice_id='.$invoice_id;
				$existing_set = $this->$model->get_list();
				$existing_ids = get_distinct('id', $existing_set);
				$form_ids = get_distinct('id', $form_addon);

				$deleted_ids = array_diff($existing_ids, $form_ids);
				if (count($deleted_ids) > 0) {
					foreach($deleted_ids AS $id) {
						if ($id == '') continue;
						$this->$model->delete($id);
					}
				}

				foreach($form_addon AS $fa) {
					if ($this->is_empty_array($fa)) continue;

					$addon_set = array(
						'id' => $fa['id'],
						'invoice_id' => $invoice_id
					);
					foreach($this->$model->data_fields AS $key=>$detail) {
						if (isset($fa[$key])) $addon_set[$key] = $fa[$key];
					}
					$id = $this->$model->save($addon_set);

					if ($id === FALSE) {
						$this->errors[] = $this->$model->get_error_string();
						$this->field_errors['addon_'.$name] = $this->$model->field_errors;
						$has_error = TRUE;
					}
				}
			}
		}

		if ($has_error) {
			$result = FALSE;
		} else {
			$result = $invoice_id;
		}

		return $result;
	}

	private function is_empty_array($array)
	{
		$is_empty = !empty($array);

		foreach ($array as $v) {
			if ($v != '') {
				$is_empty = FALSE;
			}
		}

		return $is_empty;
	}
}