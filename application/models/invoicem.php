<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class InvoiceM extends MY_Model {

	public $data_fields = array(
		'customer_card_id' => array(
			'type' => 'id',
			'required' => true,
			'allow_blank' => false
		),
		'invoice_stamp' => array(
			'type' => 'datetime',
			'required' => true,
			'allow_blank' => false
		),
		'total' => array(
			'type' => 'numeric',
			'required' => true,
			'allow_blank' => false
		),
		'payment_due_stamp' => array(
			'type' => 'datetime',
			'required' => true,
			'allow_blank' => false
		),
		'currency' => array(
			'type' => 'text'
		),
		'tax_id' => array(
			'type' => 'id'
		),
		'purchase_order_number' => array(
			'type' => 'text',
			'required' => true,
			'allow_blank' => false
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
	public $sett_fill_tax = TRUE;
	public $sett_fill_pay_item = FALSE;

	private $addons = array(
		'item' => 'Invoice_ItemM',
		'tax' => 'Invoice_TaxM',
		'pay_item' => 'Invoice_Pay_ItemM'
	);

	function __construct() {
		parent::__construct();

		$this->app = 'invoice';
		$this->table = 'a_invoice';
		$this->cache_enabled = TRUE;
		$this->sett_fill_card_info = TRUE;

		foreach ($this->addons AS $name => $model) {
			$this->load->model($model);
		}

		if ($this->UserM->is_client()) {
			$this->set_where('customer_card_id = '.$this->UserM->get_card_id());
		}
	}

	function get($id) {
		$result = parent::get($id);
		if ($result === FALSE) return FALSE;

		$this->fill_addons($result);

		if ($this->sett_fill_pay_item) {
			$paid_total = 0;
			if (isset($result['addon_pay_item'])) {
				foreach ($result['addon_pay_item'] as $r) {
					$paid_total += $r['amount'];
				}
			}

			$result['paid_total'] = $paid_total;
			$result['final_total'] = $result['total'] - $paid_total;
		}

		return $result;
	}

	function get_list() {
		$result = parent::get_list();
		if ($result === FALSE) return FALSE;

		$this->fill_addons($result, MULTIPLE_DATA);

		return $result;
	}

	private function fill_addons(&$data, $mode = SINGLE_DATA) {
		foreach ($this->addons AS $name => $model) {
			$sett_var = 'sett_fill_'.$name;
			if ($this->$sett_var == FALSE) continue;

			if ($mode == SINGLE_DATA) {
				$data = array($data);
			}

			$invoice_ids = get_distinct('id', $data);
			if ($name == 'item') {
				$this->db->select('a_invoice_item.*, a_product.name, tax_use.name as tax_use_name');
// 				$this->db->select('a_invoice_item.*, a_product.name, a_product_pricetype.a_product_pricetype_name, a_product_durationtype.a_product_durationtype_name, tax_use.name as tax_use_name');
				$this->db->from('a_invoice_item');
				$this->db->join('a_product', 'a_invoice_item.product_id = a_product.id');
// 				$this->db->join('a_product_pricetype', 'a_invoice_item.price_type = a_product_pricetype.a_product_pricetype_id', 'left');
// 				$this->db->join('a_product_durationtype', 'a_invoice_item.duration_type = a_product_durationtype.a_product_durationtype_id', 'left');
				$this->db->join('tax_use', 'a_invoice_item.tax_use_id = tax_use.id', 'left');
				$this->db->where('a_invoice_item.invoice_id IN ('.implode(',', $invoice_ids).')');
				$this->db->order_by('a_invoice_item.sort_order ASC');
				$query = $this->db->get();

				$addons = $query->result_array();
			} elseif ($name == 'pay_item') {
				$this->db->select('a_invoice_payitem.*, a_invoice_pay.transaction_stamp, a_invoice_pay.note');
				$this->db->from('a_invoice_payitem');
				$this->db->join('a_invoice_pay', 'a_invoice_payitem.invoice_pay_id = a_invoice_pay.id');
				$this->db->where('a_invoice_payitem.invoice_id IN ('.implode(',', $invoice_ids).')');
				$query = $this->db->get();

				$addons = $query->result_array();
			} else {
				$addons = $this->$model
						->set_where('invoice_id IN ('.implode(',', $invoice_ids).')')
						->get_list();
			}

			if ($addons !== FALSE && count($addons) > 0) {
				foreach ($data AS $k => $v) {
					foreach ($addons AS $addon) {
						if ($addon['invoice_id'] != $v['id']) continue;

						$data[$k]['addon_'.$name][] = $addon;
					}
				}
			}

			$this->$model->reset();

			if ($mode == SINGLE_DATA) {
				$data = $data[0];
			}
		}
	}

	function search($param, $count = false) {
		if (array_key_exists('customer_id', $param) && $param['customer_id']) {
			$this->db->where('customer_card_id', $param['customer_id']);
		} else {
			if (array_key_exists('customer_name', $param) && $param['customer_name']) {
				$this->db->where('CONCAT(card.first_name, \' \', card.last_name) LIKE', '%'.$param['customer_name'].'%');
			}
		}
		if (array_key_exists('date_range_from', $param) && $param['date_range_from']) {
			$this->db->where('payment_due_stamp >=', $param['date_range_from']);
		}
		if (array_key_exists('date_range_to', $param) && $param['date_range_to']) {
			$this->db->where('payment_due_stamp <=', $param['date_range_to']);
		}
		if (array_key_exists('status', $param) && $param['status'] != -1) {
			if ($param['status'] == 1) {
				$this->db->where('IF(invoice_pay.amount IS NULL, 0, invoice_pay.amount) >= total');
			} elseif ($param['status'] == 0) {
				$this->db->where('IF(invoice_pay.amount IS NULL, 0, invoice_pay.amount) < total');
			}
		}
		if (array_key_exists('total_min', $param) && $param['total_min']) {
			$this->db->where('total >=', $param['total_min']);
			//$this->db->where('invoice_total.total >=', $param['total_min']);
		}
		if (array_key_exists('total_max', $param) && $param['total_max']) {
			$this->db->where('total <=', $param['total_max']);
			//$this->db->where('invoice_total.total <=', $param['total_max']);
		}
		if (array_key_exists('invoice_id', $param) && $param['invoice_id']) {
			$this->db->where('a_invoice.id', $param['invoice_id']);
		}
		if (array_key_exists('po_number', $param) && $param['po_number']) {
			$this->db->where('purchase_order_number', $param['po_number']);
		}
		if (array_key_exists('notes', $param) && $param['notes']) {
			$this->db->where('memo LIKE', '%'.$param['notes'].'%');
		}

		$this->db->select('a_invoice.*, card.display_name, card.first_name, card.last_name, IF(IF(invoice_pay.amount IS NULL, 0, invoice_pay.amount) >= total, 1, 0) AS paid_status', FALSE);
		//$this->db->select('a_invoice.*, card.first_name, card.last_name, invoice_total.total');
		$this->db->from('a_invoice');
		$this->db->join('card', 'a_invoice.customer_card_id = card.id', 'left');
		//$this->db->join('(SELECT invoice_id, SUM(total) AS total FROM a_invoice_item GROUP BY invoice_id) invoice_total', 'a_invoice.id = invoice_total.invoice_id', 'left');
		$this->db->join('(SELECT invoice_id, SUM(amount) AS amount FROM a_invoice_payitem GROUP BY invoice_id) invoice_pay', 'a_invoice.id = invoice_pay.invoice_id', 'left');
		$this->db->where('a_invoice.deleted', 0);

		if ($this->UserM->is_client()) {
			$this->db->where('customer_card_id', $this->UserM->get_card_id());
		}

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

// 	function get_invoice_total($id) {
// 		$this->load->model('TaxM');
// 		$this->load->model('Tax_UseM');

// 		$invoice = $this->InvoiceM->get($id);
// 		if ($invoice === FALSE) return FALSE;

// 		$sub_total = 0;
// 		$invoice_total = 0;

// 		$tax_detail = array();
// 		foreach ($this->TaxM->get_list() as $tax) {
// 			$tax_detail[] = array('id' => $tax['id'], 'name' => $tax['name'], 'amount' => 0);
// 		}

// 		foreach ($invoice['addon_item'] as $item) {
// 			$price = $item['unit_price'];
// 			$qty = $item['quantity'];
// 			$discount = $item['discount'];

// 			$tax = 0;
// 			if ($item['tax_use_id']) {
// 				$t = $this->Tax_UseM->calculate_tax($item['tax_use_id'], $price * $qty * (100 - $discount) / 100);
// 				$tax = $t[count($t) - 1]['amount'];

// 				foreach ($tax_detail as $item_1) {
// 					foreach ($t as $item_2) {
// 						if ($item_1['id'] == $item_2['id']) {
// 							$item_1['amount'] += $item_2['amount'];
// 						}
// 					}
// 				}
// 			}

// 			$sub_total += $price * $qty * (100 - $discount) / 100;
// 			$invoice_total += $price * $qty * (100 - $discount) / 100 + $tax;
// 		}

// 		return $invoice_total;
// 	}

	function get_invoice_summary($card_id) {
		$this->db->select('
			COUNT(*) AS all_count,
			SUM(total) AS all_total,
			SUM(IF(IF(invoice_pay.amount IS NULL, 0, invoice_pay.amount) < total, 1, 0)) AS unpaid_count,
			SUM(IF(IF(invoice_pay.amount IS NULL, 0, invoice_pay.amount) < total, total - IF(invoice_pay.amount IS NULL, 0, invoice_pay.amount), 0)) AS unpaid_total', FALSE);
		$this->db->from('a_invoice');
		$this->db->join('(SELECT invoice_id, SUM(amount) AS amount FROM a_invoice_payitem GROUP BY invoice_id) invoice_pay', 'a_invoice.id = invoice_pay.invoice_id', 'left');
		$this->db->where('deleted', 0);
		$this->db->where('customer_card_id', $card_id);

		$query = $this->db->get();
		return $query->row_array();
	}

// 	function get_price_type() {
// 		$this->db->select('*');
// 		$query = $this->db->get('a_product_pricetype');

// 		return $query->result();
// 	}

// 	function get_duration_type() {
// 		$this->db->select('*');
// 		$query = $this->db->get('a_product_durationtype');

// 		return $query->result();
// 	}

	function get_product_by_name($name) {
		$this->db->select('a_product.*, a_product_price.amount');
		$this->db->from('a_product');
		$this->db->join('a_product_price', 'a_product_price.product_id = a_product.id');
		$this->db->like('a_product.name', $name);
		$query = $this->db->get();

		return $query->result();
	}

	function get_min_max_invoice_total() {
		$this->db->select('MIN(total) as min_total, MAX(total) as max_total');
		$this->db->where('deleted', 0);

		if ($this->UserM->is_client()) {
			$this->db->where('customer_card_id', $this->UserM->get_card_id());
		}

		$query = $this->db->get('a_invoice');

		$result = $query->result();
		$min = (float)($result[0]->min_total ? $result[0]->min_total : 0);
		$max = (float)($result[0]->max_total ? $result[0]->max_total : 0);

		return array('min' => $min, 'max' => $max);
	}

	/*function get_min_max_invoice_total() {
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
	}*/

	function get_form_data() {
		$data = parent::get_form_data();

		foreach ($this->addons AS $name => $model) {
			$form_addon = FALSE;
			if (isset($data['addon_'.$name])) $form_addon = $data['addon_'.$name];			//check if it's in $data
			if ($form_addon === FALSE) $form_addon = $this->input->post('addon_'.$name);	//if not, check if it's post var
			if ($form_addon === FALSE) continue;											//if not, skip to next addon

			foreach ($form_addon AS $fa) {
				if ($this->is_empty_array($fa)) continue;

				$addon_set = array();
				if (isset($fa[$this->$model->id_field])) $addon_set[$this->$model->id_field] = $fa[$this->$model->id_field];

				foreach ($this->$model->data_fields AS $key => $detail) {
					if (isset($fa[$key])) $addon_set[$key] = $fa[$key];
				}

				$data['addon_'.$name][] = $addon_set;
			}
		}

		return $data;
	}

	function is_valid(&$data) {
		$has_error = FALSE;

		//filter out any addon data
		$set = array();
		foreach ($data AS $k => $v) {
			$k = str_replace('addon_', '', $k);
			if (in_array($k, array_keys($this->addons))) continue;
			$set[$k] = $v;
		}
		if (parent::is_valid($set) == FALSE) {
			$has_error = TRUE;
		}

		if (!isset($this->field_errors['customer_card_id'])) {
			if ($this->AclM->is_client($set['customer_card_id']) == FALSE) {
				$this->errors[] = 'Non-customer cannot have invoice';
				$this->field_errors['customer_card_id'] = array('Non-customer cannot have invoice');
				$has_error = TRUE;
			}
		}

		$count_item = 0;

		foreach ($this->addons AS $name => $model) {
			if (!isset($data['addon_'.$name])) continue;

			foreach ($data['addon_'.$name] AS $addon_set) {
				if ($this->$model->is_valid($addon_set) == FALSE) {
					$this->errors[] = $this->$model->get_error_string();
					$this->field_errors['addon_'.$name] = $this->$model->field_errors;
					$has_error = TRUE;
				}

				if ($name == 'item') $count_item += 1;
			}
		}

		if ($count_item == 0) {
			$this->errors[] = 'Please add invoice items';
			$this->field_errors['addon_item'] = array('Please add invoice items');
			$has_error = TRUE;
		}

		return !$has_error;
	}

	function save($data = FALSE) {
		$has_error = FALSE;

		if ($data === FALSE) $data = $this->get_form_data();
		$is_new = !(isset($data[$this->id_field]) && $data[$this->id_field] !== FALSE);

		//filter out any addon data
		$invoice = array();
		foreach($data AS $k=>$v) {
			$k = str_replace('addon_', '', $k);
			if (in_array($k, array_keys($this->addons))) continue;
			$invoice[$k] = $v;
		}
		$invoice_id = parent::save($invoice);

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

					if ($name == 'tax') {
						if (!isset($addon_set['amount']) || $addon_set['amount'] == 0) {
							continue;
						}
					}

					$id = $this->$model->save($addon_set);

					if ($id === FALSE) {
						$this->errors[] = $this->$model->get_error_string();
						$this->field_errors['addon_'.$name] = $this->$model->field_errors;
						$has_error = TRUE;
					}
				}
			} else {
				$this->$model->where[] = 'invoice_id = '.$invoice_id;
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

					if ($name == 'tax') {
						if (!isset($addon_set['amount']) || $addon_set['amount'] == 0) {
							if (isset($addon_set['id'])) {
								$this->$model->delete($addon_set['id']);
							}
							continue;
						}
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

	private function is_empty_array($array) {
		$is_empty = !empty($array);

		foreach ($array as $k => $v) {
			if ($k == 'sort_order') continue;
			if ($v != '') {
				$is_empty = FALSE;
			}
		}

		return $is_empty;
	}
}