<?php

class SettingM extends MY_Model {
	var $cache = '';

	var $settings = array(
		'general' => array(
			'company_name' => array(
				'type' => 'string'
			),
			'timezone' => array(
				'type' => 'string'
			),
		),

		'email' => array(
			'always_bcc' => array(
				'type' => 'string'
			),
			'domain' => array(
				'type' => 'string'
			),
			'from_email_default' => array(
				'type' => 'string'
			),
			'from_name_default' => array(
				'type' => 'string'
			),
		),

		'invoice' => array(
			'headline' => array(
				'type' => 'string'
			),
			'invoice_title' => array(
				'type' => 'string'
			),
			'quotation_title' => array(
				'type' => 'string'
			),
		),

		'helpdesk' => array(
			'mail_delimiter' => array(
				'type' => 'string'
			),
			'allow_rating' => array(
				'type' => 'boolean'
			),
			'allow_new_case' => array(
				'type' => 'numeric'
			),
		),
	);

	function __construct() {
		$this->table = 'setting';
		$this->id_field = 'id';

		parent::__construct();
	}

	//combines settings from different levels, overwrites when neccessary
	//used for actual apps to get configuration values
	function get($app_name='') {
		$app_id = ($app_name=='')
			? $this->url['app_id']
			: $this->AppM->get_id($app_name);

		if (isset($this->cache[$app_id])) return $this->cache[$app_id];

		$results = array();

		$card_id = $this->UserM->get_card_id();
		$local = $this->get_local($app_id);
		$global = $this->get_global($app_id);

		//begin with the user's settings
		foreach($local AS $s) {
			if ($s['setting_level'] != 'user') continue;
			if ($s['card_id']!=$card_id) continue;

			$results[$s['setting_name']] = $s['setting_value'];
		}

		//load settings defined by tenant admin, override when neccessary
		foreach($local AS $s) {
			if ($s['setting_level'] != 'tenant') continue;
			if (isset($results[$s['setting_name']]) && $s['can_override']==1) continue;

			$results[$s['setting_name']] = $s['setting_value'];
		}

		//finish off with the default values from the global DB
		foreach($global AS $s) {
			if ($s['setting_level'] != 'global') continue;
			if (isset($results[$s['setting_name']]) && $s['can_override']==1) continue;

			$results[$s['setting_name']] = $s['setting_value'];
		}

		$this->cache[$app_id] = $results;

		return $results;
	}

	function get_setting($app_name, $setting_name) {
		$setting = $this->get($app_name);

		if (!isset($setting[$setting_name])) return NULL;

		return $setting[$setting_name];
	}



	//combines settings from different levels but does not overwrite each
	//other's settings by appending the level with the setting name
	//this is mainly for the "setting" app
	function get_for_configuration($app_name='') {
		$app_id = ($app_name=='')
			? $this->url['app_id']
			: $this->AppM->get_id($app_name);

		$results = array();

		$card_id = $this->UserM->get_card_id();
		$local = $this->get_local($app_id);
		$global = $this->get_global($app_id);

		$all_settings = array_merge($local, $global);

		foreach($all_settings AS $s) {
			if ($s['setting_level']=='user' && $s['card_id']!=$card_id) continue;

			$results[$s['setting_level']][$s['setting_name']] = array(
				'value' => $s['setting_value'],
				'can_override' => $s['can_override'],
			);
		}

		return $results;
	}



	function get_local($app_id) {
		$rs = $this->db->select()
				->from($this->table)
				->where('app_id', $app_id)
				->get();

		if ($rs->num_rows() == 0) return array();

		return $rs->result_array();
	}

	function get_global($app_id) {
		$rs = $this->db->select()
				->from('global_setting.setting')
				->where('app_id', $app_id)
				->get();

		if ($rs->num_rows() == 0) return array();

		return $rs->result_array();
	}


	function save($app_name) {
		$app_id = $this->AppM->get_id($app_name);
		$card_id = $this->UserM->get_card_id();

		if (APP_ROLE=='TBOSS' && $this->UserM->is_admin()) {
			$data = array();
			$sql = 'INSERT INTO global_setting.setting (app_id, card_id, setting_level, setting_name, setting_value, can_override) VALUES ';

			foreach($this->settings[$app_name] AS $s => $s_details) {
				$field = $this->input->post('global-'.$s);

				if ($field !== FALSE) {
					$field_override = $this->input->post('global-'.$s.'-override');
					if ($field_override === FALSE) $field_override = 1;

					//if ($s_details['type'] == 'selection') $field = json_encode($field);

					$data[] = "(".$app_id.",0,'global','".$s."','".$field."',".$field_override.")";
				}
			}

			if (count($data) > 0) {
				$sql .= implode(',',$data);
				$sql .= " ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value), can_override=VALUES(can_override)";
				$this->db->query($sql);
			}
		}

		$levels = array('tenant','user');
		$data = array();
		$sql = 'INSERT INTO setting (app_id, card_id, setting_level, setting_name, setting_value, can_override) VALUES ';
		foreach($levels AS $l) {
			if ($l == 'tenant') {
				$card_id = 0;
			} else {
				$card_id = $this->UserM->get_card_id();
			}

			foreach($this->settings[$app_name] AS $s => $s_details) {
				$field = $this->input->post($l.'-'.$s);
				if ($field !== FALSE) {
					$field_override = $this->input->post($l.'-'.$s.'-override');
					if ($field_override === FALSE) $field_override = 1;

					//if ($s_details['type'] == 'selection') $field = json_encode($field);

					$data[] = "(".$app_id.",".$card_id.",'".$l."','".$s."','".$field."',".$field_override.")";
				}
			}
		}
		if (count($data) > 0) {
			$sql .= implode(',',$data);
			$sql .= " ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value), can_override=VALUES(can_override)";
			$this->db->query($sql);
		}

		return TRUE;
	}

	function get_options($app_name, $name) {
		$results_array = $this->db->select('key, value, language_key')
							->from('core_select')
							->where('app_id', $this->AppM->get_id($app_name))
							->where('name', $name)
							->order_by('sort_order', 'ASC')
							->get()
							->result_array();

		$results = array();
		foreach($results_array AS $r) {
			$results[$r['key']] = ( empty($r['language_key']) )
									? $r['value']
									: $this->lang->line($r['language_key']);
		}

		return $results;
	}

	function get_options_for_configuration($app_name, $name, $id_as_key=FALSE) {
		$app_id = $this->AppM->get_id($app_name);

		$results = $this->db->select('id, value, sort_order, language_key')
					->from('core_select')
					->where('app_id', $app_id)
					->where('name', $name)
					->order_by('sort_order', 'ASC')
					->get()
					->result_array();

		if ($id_as_key) {
			$temp = array();
			foreach($results AS $r) {
				$temp[$r['id']] = $r;
			}
			$results = $temp;
		}

		return $results;
	}

	function delete_option($id) {
		$this->db->where('id', $id)
				->limit(1)
				->delete('core_select');

		return TRUE;
	}

	function add_option($data) {
		$this->db->insert('core_select', $data);
		$new_id = $this->db->insert_id();

		$this->db->set('key', $new_id)
				->where('id', $new_id)
				->update('core_select');

		return $new_id;
	}

	function save_options() {
		//$app_id = $this->AppM->get_id($app_name);
		$values = $this->input->get_post('value');
		$sort_order = $this->input->get_post('sort_order');

		$data = array();
		foreach($values AS $id=>$value) {
			$data[] = array(
				'id' => $id,
				'value' => $value,
				'sort_order' => $sort_order[$id],
			);
		}

		$this->db->update_batch('core_select', $data, 'id');

		return TRUE;
	}

}

