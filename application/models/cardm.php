<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class CardM extends MY_Model {

	public $data_fields = array(
		'avatar' => array(
			'type' => 'text'
		),
		'display_name' => array(
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

	public $search_fields = array(
		//search first_name + last_name
		array(
			'first_name',
			'last_name',
		),

		//search organization name
		array(
			'organization_name',
		),
	);

	public $sett_fill_address = TRUE;
	public $sett_fill_bank = TRUE;
	public $sett_fill_email = TRUE;
	public $sett_fill_extra = TRUE;
	public $sett_fill_notes = TRUE;
	public $sett_fill_social = TRUE;
	public $sett_fill_tel = TRUE;

	public $sett_fill_invoice = FALSE;
	public $sett_fill_roles = FALSE;

	private $addons = array(
		'address' => 'Card_addressM',
		'bank' => 'Card_BankM',
		'email' => 'Card_EmailM',
		'extra' => 'Card_ExtraM',
		'notes' => 'Card_NotesM',
		'social' => 'Card_SocialM',
		'tel' => 'Card_TelM',
		'access_user' => 'Access_UserM',	//used in adding role access in console add_user, saving password
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

		if ($this->sett_fill_invoice) {
			$this->load->model('InvoiceM');
			$this->InvoiceM->where[] = 'customer_card_id='.$id;
			$result['addon_invoice'] = $this->InvoiceM->get_list();
		}

		if ($this->sett_fill_roles) {
			$result['role'] = $this->AclM->get_user_role_info($id);
			$result['sub_roles'] = $this->AclM->get_user_subroles($id);
		}

		return $result;
	}

	function get_list() {
		$result = parent::get_list();

		$this->fill_addons($result, MULTIPLE_DATA);

		//TODO: inefficiency here
		if ($this->sett_fill_roles) {
			foreach($result AS $k=>$v) {
				$result[$k]['role'] = $this->AclM->get_user_role_info($v['id']);
				$result[$k]['sub_roles'] = $this->AclM->get_user_subroles($v['id']);
			}
		}

		return $result;
	}

	function get_name($card_id) {
		$user = $this->get($card_id);

		if (strlen($user['display_name']) > 0) {
			return $user['display_name'];
		}

		return $user['first_name'].' '.$user['last_name'];
	}

	function get_quickjump($card_id) {
		$this->sett_fill_address = FALSE;
		$this->sett_fill_bank = FALSE;
		$this->sett_fill_email = FALSE;
		$this->sett_fill_extra = FALSE;
		$this->sett_fill_notes = FALSE;
		$this->sett_fill_social = FALSE;
		$this->sett_fill_tel = FALSE;
		$this->sett_fill_invoice = FALSE;

		$this->sett_fill_roles = TRUE;
		return $this->get($card_id);
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
		if ($mode == SINGLE_DATA) {
			$data = array($data);
		}

		$card_ids = get_distinct('id', $data);

		foreach($this->addons AS $name=>$model) {
			$sett_var = 'sett_fill_'.$name;
			if ($this->$sett_var == FALSE) continue;

			$addon_data = $this->$model->set_where('card_id IN ('.implode(',', $card_ids).')')
							->get_list();

			if ($addon_data !== FALSE && count($addon_data) > 0) {
				foreach($data AS $k=>$v) {
					foreach($addon_data AS $ad) {
						if ($ad['card_id'] != $v['id']) continue;

						$data[$k]['addon_'.$name][] = $ad;
					}
				}
			} else {
				foreach($data AS $k=>$v) {
					$data[$k]['addon_'.$name] = array();
				}
			}

			$this->$model->reset();
		}

		if ($mode == SINGLE_DATA) {
			$data = $data[0];
		}
	}

	function get_card_email($id) {
		$this->db->select('*');
		$this->db->where('card_id', $id);
		//$this->db->where('is_default', 1);
		$query = $this->db->get('card_email');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_card_social($id) {
		$this->db->select('*');
		$this->db->where('card_id', $id);
		$query = $this->db->get('card_social');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_card_phone($id) {
		$this->db->select('*');
		$this->db->where('card_id', $id);
		//$this->db->where('is_default', 1);
		$query = $this->db->get('card_tel');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function get_card_address($id) {
		$this->db->select('*');
		$this->db->where('card_id', $id);
		//$this->db->where('is_default', 1);
		$query = $this->db->get('card_address');

		if ($query->result()) {
			return $query->result();
		} else {
			return false;
		}
	}

	function search_staff($search_string) {
		//search first_name + last_name only
		$this->search_fields = array(
			array(
				'first_name',
				'last_name',
			),
		);

		$staff_card_ids = $this->AclM->get_card_ids_in_role('Staff');

		//if there are no STAFF card_ids, return a blank result.
		if (count($staff_card_ids) == 0) return array();

		//limit results to card_ids in STAFF role
		$this->where[] = 'id IN ('.implode(',', $staff_card_ids).')';

		//retrieve only id, first_name and last_name
		$this->select_fields = array('id', 'first_name', 'last_name');

		return parent::search($search_string);
	}

	function get_staff_list() {
		$staff_card_ids = $this->AclM->get_card_ids_in_role('Staff');

		//if there are no STAFF card_ids, return a blank result.
		if (count($staff_card_ids) == 0) return array();

		//limit results to card_ids in STAFF role
		$this->where[] = 'id IN ('.implode(',', $staff_card_ids).')';

		return $this->get_list();
	}
}