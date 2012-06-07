<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Invoice_PayM extends MY_Model {

	public $data_fields = array(
		'transaction_stamp' => array(
			'type' => 'text'
		),
		'transaction_number' => array(
			'type' => 'text',
			'required' => true,
			'allow_blank' => false
		),
		'amount' => array(
			'type' => 'text'
		),
		'status' => array(
			'type' => 'text'
		),
		'docs_id' => array(
			'type' => 'text'
		),
		'void_type' => array(
			'type' => 'text'
		),
		'void_stamp' => array(
			'type' => 'text'
		),
		'void_docs_id' => array(
			'type' => 'text'
		),
		'note' => array(
			'type' => 'text'
		)
	);

	public $sett_fill_item = TRUE;

	private $addons = array(
		'item' => 'Invoice_Pay_ItemM'
	);

	function __construct() {
		parent::__construct();

		$this->table = 'a_invoice_pay';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;

		foreach ($this->addons AS $name => $model) {
			$this->load->model($model);
		}
	}

	function get($id) {
		$result = parent::get($id);
		if ($result === FALSE) return FALSE;

		$this->fill_addons($result);

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

			$invoice_pay_ids = get_distinct('id', $data);
			$addons = $this->$model
						->set_where('invoice_pay_id IN ('.implode(',', $invoice_pay_ids).')')
						->get_list();

			if ($addons !== FALSE && count($addons) > 0) {
				foreach ($data AS $k => $v) {
					foreach ($addons AS $addon) {
						if ($addon['invoice_pay_id'] != $v['id']) continue;

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

		$count_item = 0;

		foreach ($this->addons AS $name => $model) {
			if (!isset($data['addon_'.$name])) continue;

			foreach ($data['addon_'.$name] AS $addon_set) {
				if ($this->$model->is_valid($addon_set) == FALSE) {
					$this->errors[] = $this->$model->get_error_string();
					$this->field_errors['addon_'.$name] = $this->$model->field_errors;
					$has_error = TRUE;
				}

				if ($name == 'item') {
					if (!isset($this->field_errors['addon_item']['invoice_id']) && !isset($this->field_errors['addon_item']['amount'])) {
						$total = $this->InvoiceM->get_invoice_total($addon_set['invoice_id']);
						if ($total === FALSE) {
							$this->errors[] = 'Invoice not exist';
							$this->field_errors['invoice_id'] = array('Invoice not exist');
							$has_error = TRUE;
						} else {
							if ($addon_set['amount'] > $total) {
								$this->errors[] = 'Invoice #'.$addon_set['invoice_id'].' Amount paying must less than the outstanding amount';
								$this->field_errors['amount'] = array('Invoice #'.$addon_set['invoice_id'].' Amount paying must less than the outstanding amount');
								$has_error = TRUE;
							}
						}
					}

					$count_item += 1;
				}
			}
		}

		if ($count_item == 0) {
			$this->errors[] = 'Please add invoice';
			$this->field_errors['addon_item'] = array('Please add invoice');
			$has_error = TRUE;
		}

		return !$has_error;
	}

	function save($data = FALSE) {
		$has_error = FALSE;

		if ($data === FALSE) $data = $this->get_form_data();
		$is_new = !(isset($data[$this->id_field]) && $data[$this->id_field] !== FALSE);

		//filter out any addon data
		$invoice_pay = array();
		foreach($data AS $k=>$v) {
			$k = str_replace('addon_', '', $k);
			if (in_array($k, array_keys($this->addons))) continue;
			$invoice_pay[$k] = $v;
		}
		$invoice_pay['transaction_stamp'] = get_current_stamp();
		$invoice_pay_id = parent::save($invoice_pay);

		if ($invoice_pay_id === FALSE) $has_error = TRUE;

		foreach($this->addons AS $name=>$model) {
			$form_addon = FALSE;
			if (isset($data['addon_'.$name])) $form_addon = $data['addon_'.$name];			//check if it's in $data
			if ($form_addon === FALSE) $form_addon = $this->input->post('addon_'.$name);	//if not, check if it's post var
			if ($form_addon === FALSE) continue;											//if not, skip to next addon

			if ($is_new) {
				foreach($form_addon AS $fa) {
					if ($this->is_empty_array($fa)) continue;

					$addon_set = array(
						'invoice_pay_id' => $invoice_pay_id
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
				$this->$model->where[] = 'invoice_pay_id = '.$invoice_pay_id;
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
						'invoice_pay_id' => $invoice_pay_id
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
			$result = $invoice_pay_id;
		}

		return $result;
	}

	private function is_empty_array($array) {
		$is_empty = !empty($array);

		foreach ($array as $v) {
			if ($v != '') {
				$is_empty = FALSE;
			}
		}

		return $is_empty;
	}
}