<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class ProductM extends MY_Model {

	public $data_fields = array(
		'category_id' => array(
			'type' => 'id',
			'required' => true,
			'allow_blank' => false
		),
		'name' => array(
			'type' => 'text',
			'required' => true,
			'allow_blank' => false
		),
		'sku' => array(
			'type' => 'text'
		),
		'type' => array(
			'type' => 'text'
		),
		'status' => array(
			'type' => 'text'
		),
		'charge_type' => array(
			'type' => 'text'
		),
		'tax_id' => array(
			'type' => 'id'
		)
	);

	public $sett_fill_desp = TRUE;
	public $sett_fill_price = TRUE;

	private $addons = array(
		'desp' => 'Product_DespM',
		'price' => 'Product_PriceM'
	);

	function __construct() {
		parent::__construct();

		$this->app = 'product';
		$this->table = 'a_product';
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

			$product_ids = get_distinct('id', $data);
			$addons = $this->$model
					->set_where('product_id IN ('.implode(',', $product_ids).')')
					->get_list();

			if ($addons !== FALSE && count($addons) > 0) {
				foreach ($data AS $k => $v) {
					foreach ($addons AS $addon) {
						if ($addon['product_id'] != $v['id']) continue;

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

	function get_product_in_category($category_id) {
		$this->set_where('category_id = '.$category_id);
		return $this->get_list();
	}

	function get_form_data() {
		$data = parent::get_form_data();

		foreach ($this->addons AS $name => $model) {
			$form_addon = FALSE;
			if (isset($data['addon_'.$name])) $form_addon = $data['addon_'.$name];			//check if it's in $data
			if ($form_addon === FALSE) $form_addon = $this->input->post('addon_'.$name);	//if not, check if it's post var
			if ($form_addon === FALSE) continue;											//if not, skip to next addon

			foreach ($form_addon AS $fa) {
// 				if ($this->is_empty_array($fa)) continue;

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

		foreach ($this->addons AS $name => $model) {
			if (!isset($data['addon_'.$name])) continue;

			foreach ($data['addon_'.$name] AS $addon_set) {
				if ($this->$model->is_valid($addon_set) == FALSE) {
					$this->errors[] = $this->$model->get_error_string();
					$this->field_errors['addon_'.$name] = $this->$model->field_errors;
					$has_error = TRUE;
				}
			}
		}

		return !$has_error;
	}

	function save($data = FALSE) {
		$has_error = FALSE;

		if ($data === FALSE) $data = $this->get_form_data();
		$is_new = !(isset($data[$this->id_field]) && $data[$this->id_field] !== FALSE);

		//filter out any addon data
		$product = array();
		foreach($data AS $k=>$v) {
			$k = str_replace('addon_', '', $k);
			if (in_array($k, array_keys($this->addons))) continue;
			$product[$k] = $v;
		}
		$product_id = parent::save($product);

		if ($product_id === FALSE) $has_error = TRUE;

		foreach($this->addons AS $name=>$model) {
			$form_addon = FALSE;
			if (isset($data['addon_'.$name])) $form_addon = $data['addon_'.$name];			//check if it's in $data
			if ($form_addon === FALSE) $form_addon = $this->input->post('addon_'.$name);	//if not, check if it's post var
			if ($form_addon === FALSE) continue;											//if not, skip to next addon

			if ($is_new) {
				foreach($form_addon AS $fa) {
// 					if ($this->is_empty_array($fa)) continue;

					$addon_set = array(
						'product_id' => $product_id
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
				$this->$model->where[] = 'product_id = '.$product_id;
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
// 					if ($this->is_empty_array($fa)) continue;

					$addon_set = array(
						'id' => $fa['id'],
						'product_id' => $product_id
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
			$result = $product_id;
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