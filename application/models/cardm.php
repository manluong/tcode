<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class CardM extends MY_Model {

	public $data_fields = array(
		'avatar' => array(
			'type' => 'text'
		),
		'nickname' => array(
			'type' => 'text'
		),
		'title' => array(
			'type' => 'selection',
			'default' => 0,
			'options' => array(
				0 => '',
				1 => 'core_select-select-cardtitle-Mr.',
				2 => 'core_select-select-cardtitle-Miss.',
				3 => 'core_select-select-cardtitle-Mrs.',
				4 => 'core_select-select-cardtitle-Dr.',
			),
		),
		'first_name' => array(
			'type' => 'text'
		),
		'middle_name' => array(
			'type' => 'text'
		),
		'last_name' => array(
			'type' => 'text'
		),
		'format_name' => array(
			'type' => 'text'
		),
		'organization_name' => array(
			'type' => 'text'
		),
		'organization_number' => array(
			'type' => 'text'
		),
		'organization_title' => array(
			'type' => 'text'
		),
		'timezone' => array(
			'type' => 'numeric',
			'default' => 0,
		),
		'default_language' => array(
			'type' => 'text',
			'default' => 'en'
		),
	);

	public $sett_fill_address = TRUE;
	public $sett_fill_bank = TRUE;
	public $sett_fill_email = TRUE;
	public $sett_fill_extra = TRUE;
	public $sett_fill_notes = TRUE;
	public $sett_fill_social = TRUE;
	public $sett_fill_tel = TRUE;
	public $sett_fill_access_user = FALSE;

	private $addons = array(
		'address' => 'Card_addressM',
		'bank' => 'Card_BankM',
		'email' => 'Card_EmailM',
		'extra' => 'Card_ExtraM',
		'notes' => 'Card_NotesM',
		'social' => 'Card_SocialM',
		'tel' => 'Card_TelM',
		'access_user' => 'Access_UserM',
	);

	function __construct() {
		parent::__construct();

		$this->table = 'card';
		$this->cache_enabled = TRUE;

		foreach($this->addons AS $name=>$model) {
			$this->load->model($model);
		}
	}

	function get($id) {
		$result = parent::get($id);

		$this->fill_addons($result);

		return $result;
	}

	function get_list() {
		$result = parent::get_list();

		$this->fill_addons($result, MULTIPLE_DATA);

		return $result;
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
		$card_id = parent::save($card);

		if ($card_id === FALSE) $has_error = TRUE;

		foreach($this->addons AS $name=>$model) {
			$form_addon = FALSE;
			if (isset($data['addon_'.$name])) $form_addon = $data['addon_'.$name];			//check if it's in $data
			if ($form_addon === FALSE) $form_addon = $this->input->post('addon_'.$name);	//if not, check if it's post var
			if ($form_addon === FALSE) continue;											//if not, skip to next addon

			if ($is_new) {
				foreach($form_addon AS $fa) {
					$addon_set = array(
						'card_id' => $card_id
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
				$this->$model->where[] = 'card_id='.$card_id;
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
					$addon_set = array(
						'id' => $fa['id'],
						'card_id' => $card_id
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
			$result = $card_id;
		}

		return $result;
	}

	private function fill_addons(&$data, $mode=SINGLE_DATA) {
		foreach($this->addons AS $name=>$model) {
			$sett_var = 'sett_fill_'.$name;
			if ($this->$sett_var == FALSE) continue;

			if ($mode == SINGLE_DATA) {
				$data = array($data);
			}

			$card_ids = get_distinct('id', $data);
			$addons = $this->$model
						->set_where('card_id IN ('.implode(',', $card_ids).')')
						->get_list();

			if ($addons !== FALSE && count($addons) > 0) {
				foreach($data AS $k=>$v) {
					foreach($addons AS $addon) {
						if ($addon['card_id'] != $v['id']) continue;

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

}