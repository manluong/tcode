<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class ACLM extends MY_Model {
	var $url = array();

	var $cache_acl = array();
	var $unit_test = array(
		'triggered_rule' => array(),
	);

	function __construct() {
		$this->table = 'access_rights_new';
		$this->id_field = 'id';
		$this->cache_enabled = TRUE;

		parent::__construct();

		$CI =& get_instance();
		$this->url = $CI->url;

		$this->verify_request();
	}

	//check if $app, $an, etc are authorized or not
	function verify_request() {

	}

	function check_id_encryption() {
		if (!id_is_encrypted($this->url['id'])) die('id must be encrypted');
	}

	function check_app_access() {
		if (!$this->UserM->is_logged_in() && !$this->AppM->has_public_access()) {
			header( 'Location: /access/login/'.set_return_url(TRUE));
			exit;
		} elseif ($this->UserM->is_logged_in() && !$this->UserM->is_admin() && !$this->AppM->has_public_access()) {
			$app_access_rights_table = $this->get_rights();

			if ($app_access_rights_table['allow'] == 3) {
				//requestion aved is not allowed/set in AN
				meg(999, 'AN Permission Not Allow. - '.$this->url['subaction']);
			} elseif ($app_access_rights_table['allow'] == 2) {
				//the access is denied by an entry in the access_rights table
				meg(999, 'Access Rights Permission Not Allow. - '.$app_access_rights_table['typeid']);
			} elseif ($app_access_rights_table['allow'] != 1) {
				//not permission is set to allow access, minimum set a Allow all rule for a App for each master group (except Admin)
				meg(999, 'Access Rights Permission Not Allow. - No Permission');
			}
		}
	}





	//function to get rights from access_rights table
	function get_rights(){
		$apps_action = $this->AppM->actions;

		$result = array();

		$action_denied = FALSE;
		$avedfield = '';

		switch($this->url['subaction']){
			case 'a':
				$avedfield = 'access_rights_add';
				if (!$apps_action['core_apps_action_add']) $action_denied = TRUE;
				break;
			case 'v':
				$avedfield = 'access_rights_view';
				if (!$apps_action['core_apps_action_view']) $action_denied = TRUE;
				break;
			case 'e':
				$avedfield = 'access_rights_edit';
				if (!$apps_action['core_apps_action_del']) $action_denied = TRUE;
				break;
			case 'd': $avedfield = 'access_rights_delete';
				if (!$apps_action['core_apps_action_del']) $action_denied = TRUE;
				break;
			case 'l':
				$avedfield = 'access_rights_list';
				if (!$apps_action['core_apps_action_list']) $action_denied = TRUE;
				break;
			case 's':
				$avedfield = 'access_rights_search';
				if (!$apps_action['core_apps_action_search']) $action_denied = TRUE;
				break;
			case 'f':
				$avedfield = 'access_rights_form';
				if (!$apps_action['core_apps_action_form']) $action_denied = TRUE;
				break;
		}

		if ($action_denied) {
			$result['allow'] = 3;
			return $result;
		}

		//get the table with exact mastergp+app+actiongp+cardid

		$where = array();
		$where['accessgp'] = "access_rights_gpmaster = '".$this->UserM->info['accessgp']."'";
		$where['app'] = " AND access_rights_app = '".$this->url['app']."'";
		$where['cardid'] = " AND access_rights_cardid = '".$this->UserM->info['cardid']."'";
		$where['actiongp'] = " AND access_rights_actiongp = '".$apps_action['core_apps_action_gp']."'";

		if ($this->UserM->info['subgp']) {
			$subgp = join(',',$this->UserM->info['subgp']);
			$where['subgp'] = " AND access_rights_gpsub IN ($subgp)";
		}

		$where['cardid_empty'] = " AND access_rights_cardid = ''";
		$where['actiongp_empty'] = " AND access_rights_actiongp = ''";
		$where['subgp_empty'] = " AND access_rights_gpsub = ''";

		//sql check sqeunce
		$sql = array();

		//with acccessgp,app,actiongp,cardid + subgp
		$sql[0] = $where['accessgp'];
		$sql[0] .= $where['subgp'];
		$sql[0] .= $where['app'];
		$sql[0] .= $where['cardid'];
		$sql[0] .= $where['actiongp'];

		//with acccessgp,app,actiongp,cardid
		$sql[1] = $where['accessgp'];
		$sql[1] .= $where['subgp_empty'];
		$sql[1] .= $where['app'];
		$sql[1] .= $where['cardid'];
		$sql[1] .= $where['actiongp'];


		//with acccessgp,app,actiongp + subgp
		$sql[2] = $where['accessgp'];
		$sql[2] .= $where['subgp'];
		$sql[2] .= $where['app'];
		$sql[2] .= $where['cardid_empty'];
		$sql[2] .= $where['actiongp'];

		//with acccessgp,app,actiongp
		$sql[3] = $where['accessgp'];
		$sql[3] .= $where['subgp_empty'];
		$sql[3] .= $where['app'];
		$sql[3] .= $where['cardid_empty'];
		$sql[3] .= $where['actiongp'];

		//with acccessgp,app,cardid + subgp
		$sql[4] = $where['accessgp'];
		$sql[4] .= $where['subgp'];
		$sql[4] .= $where['app'];
		$sql[4] .= $where['cardid'];
		$sql[4] .= $where['actiongp_empty'];

		//with acccessgp,app,cardid
		$sql[5] = $where['accessgp'];
		$sql[5] .= $where['subgp_empty'];
		$sql[5] .= $where['app'];
		$sql[5] .= $where['cardid'];
		$sql[5] .= $where['actiongp_empty'];

		//with acccessgp,app + subgp
		$sql[6] = $where['accessgp'];
		$sql[6] .= $where['subgp'];
		$sql[6] .= $where['app'];
		$sql[6] .= $where['cardid_empty'];
		$sql[6] .= $where['actiongp_empty'];

		//with acccessgp,app
		$sql[7] = $where['accessgp'];
		$sql[7] .= $where['subgp_empty'];
		$sql[7] .= $where['app'];
		$sql[7] .= $where['cardid_empty'];
		$sql[7] .= $where['actiongp_empty'];

		$count = 0;
		if (!$where['subgp']) $count++;

		while (!$result && $sql[$count]){
			$rs = $this->db->query('SELECT * FROM access_rights WHERE '.$sql[$count]);
			if ($rs->num_rows()>0){
				foreach ($rs->result_array() as $field) {
					if (!isset($result['allow']) || $result['allow'] != 1){
						$result['allow'] = $this->check_table_rights($field, $avedfield);
						$result['typeid'] = $field['access_rights_id'];
						$result['type'] = 'rightstable';
					}
				}
			}
			$count++;
			if (!$where['subgp']) $count++;
		}

		return $result;
	}


	function check_table_rights($access,$avedfield){
		$result = 0;

		switch ($access['access_rights_type']){
			case '1':
				$result = 1;
				break;

			case '2':
				$result = 2;
				break;

			case '3':
				$result = ($access[$avedfield])
							? 1
							: 2;
				break;
		}

		if ($result == 1 && $access['access_rights_matchthisid']) {
			$result = ($this->url['id_plain']!=0 && $this->UserM->info[$access['access_rights_matchthisidtype']] == $this->url['plain_id'])
				? 1
				: 2;
		}

		return $result;
	}






	function check($action='', $app_id='', $actiongp='', $app_data_id=0) {
		$app = $this->AppM->get_name($app_id);

		if ($app_id == '') $app_id = $this->url['app_id'];
		if ($actiongp == '') $actiongp = $this->url['actiongp'];
		if ($app_data_id != 0) $app_data_id = array($app_data_id, 0);

		$acl = $this->get_acl($app_id, $actiongp, $app_data_id);
		$cardid = $this->UserM->get_cardid();
		$subgp = $this->UserM->info['subgp'];
		$mastergp = $this->UserM->info['accessgp'];

		foreach($app_data_id AS $adi) {
			$case2_acl = array();
			$this->unit_test['triggered_rule'] = array();

			foreach($acl AS $a) {
				if ($a['app_data_id'] != $adi) continue;

				switch($a['role_type']) {
					case 1:	//card

						if ($a['role_id'] == $cardid) {
							$this->unit_test['triggered_rule'][] = $a['id'];
							return ($a[$action] == 1);
						}

						break;

					case 2: //subgroup

						if (in_array($a['role_id'], $subgp)) {
							$this->unit_test['triggered_rule'][] = $a['id'];
							$case2_acl[] = $a;
						}

						break;

					case 3: //mastergroup

						//if there's any acl from case 2, consolidate them and return result
						if (count($case2_acl) > 0) {
							$case2_acl = $this->consolidate_acl($case2_acl);
							return ($case2_acl[$action] == 1);
						}

						$this->unit_test['triggered_rule'] = array();

						if ($a['role_id'] == $mastergp) {
							$this->unit_test['triggered_rule'][] = $a['id'];
							return ($a[$action] == 1);
						}

						break;
				}
			}
		}

		return FALSE;
	}

	function get_acl($app_id, $actiongp, $app_data_id=array(0)) {
		$app = $this->AppM->get_name($app_id);

		if ($actiongp == '') $actiongp = $this->url['actiongp'];

		if (!is_array($app_data_id)) $app_data_id = array($app_data_id);

		$result = array();

		foreach($app_data_id AS $k=>$adi) {
			$key = $app_id.'_'.$actiongp.'_'.$adi;
			if (isset($this->cache_acl[$key])) {
				$result += $this->cache_acl[$key];
				unset($app_data_id[$k]);
			}
		}

		if (count($app_data_id) == 0) return $result;

		$rs = $this->db->select()
				->from('access_rights_new')
				->where('app_id', $app_id)
				->where('actiongp', $actiongp)
				->where_in('app_data_id', $app_data_id)
				->order_by('role_type', 'ASC')
				->get();

		foreach($rs->result_array() AS $acl) {
			$result[] = $acl;

			$key = $acl['app_id'].'_'.$acl['actiongp'].'_'.$acl['app_data_id'];
			$this->cache_acl[$key][] = $acl;
		}

		return $result;
	}

	function get_acl_app_ids() {
		$result = array();
		$cardid = $this->UserM->get_cardid();
		$subgp = $this->UserM->info['subgp'];
		$mastergp = $this->UserM->info['accessgp'];
		$case2_acl = array();

		$rs = $this->db->select('DISTINCT app_id, role_type, role_id, `read`', FALSE)
				->from($this->table)
				->where('actiongp', '')
				->order_by('role_type', 'DESC')
				->get();

		$acls = $rs->result_array();

		foreach($acls AS $a) {
			switch($a['role_type']) {
				case 3:	//mastergroup

					if ($a['role_id'] == $mastergp) {
						$result[$a['app_id']] = $a['read'];
					}

					break;

				case 2: //subgroup

					if (in_array($a['role_id'], $subgp)) {
						$case2_acl[] = $a;
					}

					break;

				case 1: //card

					//if there's any acl from case 2, consolidate them and return result
					if (count($case2_acl) > 0) {
						$case2_acl = $this->consolidate_acl($case2_acl);
						$result[$a['app_id']] = $case2_acl['read'];
					}

					if ($a['role_id'] == $cardid) {
						$result[$a['app_id']] = $a['read'];
					}

					break;
			}
		}

		foreach($result AS $app_id => $access) {
			if ($access == 0) unset($result[$app_id]);
		}

		return array_keys($result);
	}

	private function consolidate_acl($acl) {
		$result = array(
			'admin' => 1,
			'read' => 1,
			'list' => 1,
			'search' => 1,
			'copy' => 1,
			'download' => 1,
			'write' => 1,
			'add' => 1,
			'move' => 1,
			'rename' => 1,
			'delete' => 1
		);

		foreach($acl AS $a) {
			foreach ($result AS $act=>$val) {
				if (!isset($a[$act])) continue;
				if ($a[$act] < $val) $result[$act] = $a[$act];
			}
		}

		return $result;
	}

	function fill_acl_details(&$acl) {
		$card_ids = $subgp_ids = $gp_ids = array();

		foreach($acl AS $a) {
			switch($a['role_type']) {
				case 1:
					$card_ids[] = $a['role_id'];
					break;
				case 2:
					$subgp_ids[] = $a['role_id'];
					break;
				case 3:
					$gp_ids[] = $a['role_id'];
					break;
			}
		}

		$card_details = $this->UserM->get_batch($card_ids, TRUE);

		$subgp_details = $this->get_subgp_batch($subgp_ids, TRUE);
		foreach($subgp_details AS $s) {
			$gp_ids[] = $s['access_gpsub_gpmaster'];
		}

		$gp_details = $this->get_gp_batch($gp_ids, TRUE);

		foreach($acl AS $k=>$a) {
			switch($a['role_type']) {
				case 1:
					$acl[$k]['name'] = $card_details[$a['role_id']]['card_fname'].' '.$card_details[$a['role_id']]['card_lname'];
					break;
				case 2:
					$gp_id = $subgp_details[$a['role_id']]['access_gpsub_gpmaster'];
					$acl[$k]['name'] = $gp_details[$gp_id]['access_gpmaster_name'].' - '.$subgp_details[$a['role_id']]['access_gpsub_name'];
					break;
				case 3:
					$acl[$k]['name'] = $gp_details[$a['role_id']]['access_gpmaster_name'];
					break;
			}
		}
	}



	function get_subgp_batch($ids, $id_as_key=FALSE) {
		$temp_tb = $this->table;
		$temp_id = $this->id_field;

		$this->table = 'access_gpsub';
		$this->id_field = 'access_gpsub_id';

		$results = parent::get_batch($ids, $id_as_key);

		$this->table = $temp_tb;
		$this->id_field = $temp_id;

		return $results;
	}

	function get_gp_batch($ids, $id_as_key=FALSE) {
		$temp_tb = $this->table;
		$temp_id = $this->id_field;

		$this->table = 'global_setting.access_gpmaster';
		$this->id_field = 'access_gpmaster_code';

		$results = parent::get_batch($ids, $id_as_key);

		$this->table = $temp_tb;
		$this->id_field = $temp_id;

		return $results;
	}

	function get_gp_list() {
		$temp_tb = $this->table;
		$temp_id = $this->id_field;

		$this->table = 'global_setting.access_gpmaster';
		$this->id_field = 'access_gpmaster_code';

		$results = parent::get_list();

		$this->table = $temp_tb;
		$this->id_field = $temp_id;

		return $results;
	}

	function get_subgp($gp='') {
		$this->db->select()
			->from('access_gpsub');

		if ($gp != '') $this->db->where('access_gpsub_gpmaster', $gp);

		$rs = $this->db->get();

		if ($rs->num_rows() == 0) return array();

		return $rs->result_array();
	}

	function get_users($gp='') {
		$roles = $this->get_subgp($gp);

		$role_ids = array();

		foreach($roles AS $r) {
			$role_ids[] = $r['access_gpsub_id'];
		}

		if (count($role_ids) == 0) return array();

		$rs = $this->db->select('DISTINCT access_usergp.access_usergp_cardid, CONCAT(card.card_fname," ",card.card_lname) AS name', false)
				->from('access_usergp')
				->where_in('access_usergp.access_usergp_gpsub', $role_ids)
				->join('card', 'card.card_id=access_usergp.access_usergp_cardid', 'left')
				->get();

		if ($rs->num_rows() == 0) return array();



		return $rs->result_array();
	}

	function save_acl($data) {
		$data['created_cardid'] = $this->UserM->get_cardid();
		$data['created_stamp'] = get_current_stamp();

		parent::save($data, $this->id_field);
	}

	function delete_acl($id) {
		$this->db->where('id', $id)
				->limit(1)
				->delete($this->table);
	}
}