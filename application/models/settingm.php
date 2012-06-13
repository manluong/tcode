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
			)
		),
		'helpdesk' => array(
			'priority' => array(
				'type' => 'selection'
			)
		)
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

					if ($s_details['type'] == 'selection') $field = json_encode($field);

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
			foreach($this->settings[$app_name] AS $s => $s_details) {
				$field = $this->input->post($l.'-'.$s);
				if ($field !== FALSE) {
					$field_override = $this->input->post($l.'-'.$s.'-override');
					if ($field_override === FALSE) $field_override = 1;

					if ($s_details['type'] == 'selection') $field = json_encode($field);

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



}

