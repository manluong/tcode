<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class ACLM extends MY_Model {
	var $url = array();

	var $cache_acl = array();
	var $unit_test = array(
		'triggered_rule' => array(),
	);

	function __construct() {
		$this->table = 'access_control';
		$this->id_field = 'id';
		$this->cache_enabled = TRUE;

		parent::__construct();

		$this->verify_request();
	}

	//check if $app, $an, etc are authorized or not
	function verify_request() {

	}

	function check_id_encryption() {
		if (!id_is_encrypted($this->url['id'])) die('id must be encrypted');
	}

	function check_app_access($mode='normal') {
		if ($mode=='basic') {
			if (!$this->UserM->is_logged_in() && !$this->AppM->has_public_access()) {
				header( 'Location: /access/login/'.set_return_url(TRUE));
				exit;
			}
		}

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




	function check($action='', $app='', $actiongp='', $app_data_id=0) {
		//$app = $this->AppM->get_name($app);

		if ($app == '') $app = $this->url['app'];
		if ($actiongp == '') $actiongp = $this->url['actiongp'];
		if ($app_data_id != 0) $app_data_id = array($app_data_id, 0);

		$acl = $this->get_acl($app, $actiongp, $app_data_id);
		$cardid = $this->UserM->get_card_id();
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

	function get_acl($app, $actiongp, $app_data_id=array(0)) {
		$app = $this->AppM->get_name($app);

		if ($actiongp == '') $actiongp = $this->url['action'];

		if (!is_array($app_data_id)) $app_data_id = array($app_data_id);

		$result = array();

		foreach($app_data_id AS $k=>$adi) {
			$key = $app.'_'.$actiongp.'_'.$adi;
			if (isset($this->cache_acl[$key])) {
				$result += $this->cache_acl[$key];
				unset($app_data_id[$k]);
			}
		}

		if (count($app_data_id) == 0) return $result;

		$rs = $this->db->select()
				->from($this->table)
				->where('app', $app)
				->where('action', $actiongp)
				->where_in('app_data_id', $app_data_id)
				->order_by('role_type', 'ASC')
				->get();

		foreach($rs->result_array() AS $acl) {
			$result[] = $acl;

			$key = $acl['app'].'_'.$acl['action'].'_'.$acl['app_data_id'];
			$this->cache_acl[$key][] = $acl;
		}

		return $result;
	}

	function get_acl_apps() {
		$result = array();
		$cardid = $this->UserM->get_card_id();
		$subgp = $this->UserM->info['subgp'];
		$mastergp = $this->UserM->info['accessgp'];
		$case2_acl = array();

		$rs = $this->db->select('DISTINCT app, role_type, role_id, `read`', FALSE)
				->from($this->table)
				->where('action', '')
				->order_by('role_type', 'DESC')
				->get();

		$acls = $rs->result_array();

		foreach($acls AS $a) {
			switch($a['role_type']) {
				case 3:	//mastergroup

					if ($a['role_id'] == $mastergp) {
						$result[$a['app']] = $a['read'];
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
						$result[$a['app']] = $case2_acl['read'];
					}

					if ($a['role_id'] == $cardid) {
						$result[$a['app']] = $a['read'];
					}

					break;
			}
		}

		foreach($result AS $app => $access) {
			if ($access == 0) unset($result[$app]);
		}

		return array_keys($result);
	}

	private function consolidate_acl($acl, $default_priority='allow') {
		if ($default_priority == 'allow') {
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
		} else {
			$result = array(
				'admin' => 0,
				'read' => 0,
				'list' => 0,
				'search' => 0,
				'copy' => 0,
				'download' => 0,
				'write' => 0,
				'add' => 0,
				'move' => 0,
				'rename' => 0,
				'delete' => 0
			);
		}

		foreach($acl AS $a) {
			foreach ($result AS $act=>$val) {
				if (!isset($a[$act])) continue;

				if ($default_priority == 'allow') {
					if ($a[$act] < $val) $result[$act] = $a[$act];
				} else {
					if ($a[$act] > $val) $result[$act] = $a[$act];
				}
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
			$gp_ids[] = $s['role_id'];
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
					$acl[$k]['name'] = $gp_details[$a['role_id']]['name'];
					break;
			}
		}
	}



	function get_subgp_batch($ids, $id_as_key=FALSE) {
		$temp_tb = $this->table;
		$temp_id = $this->id_field;

		$this->table = 'access_roles_sub';
		$this->id_field = 'id';

		$results = parent::get_batch($ids, $id_as_key);

		$this->table = $temp_tb;
		$this->id_field = $temp_id;

		return $results;
	}

	function get_gp_batch($ids, $id_as_key=FALSE) {
		$temp_tb = $this->table;
		$temp_id = $this->id_field;

		$this->table = 'global_setting.access_roles';
		$this->id_field = 'code';

		$results = parent::get_batch($ids, $id_as_key);

		$this->table = $temp_tb;
		$this->id_field = $temp_id;

		return $results;
	}

	function get_gp_list() {
		$temp_tb = $this->table;
		$temp_id = $this->id_field;

		$this->table = 'global_setting.access_roles';
		$this->id_field = 'code';

		$results = parent::get_list();

		$this->table = $temp_tb;
		$this->id_field = $temp_id;

		return $results;
	}

	function get_subgp($gp='') {
		$this->db->select()
			->from('access_roles_sub');

		if ($gp != '') $this->db->where('role_id', $gp);

		$rs = $this->db->get();

		if ($rs->num_rows() == 0) return array();

		return $rs->result_array();
	}

	function get_users($gp='') {
		$subroles = $this->get_subgp($gp);

		$subrole_ids = array();

		foreach($subroles AS $r) {
			$subrole_ids[] = $r['id'];
		}

		if (count($subrole_ids) == 0) return array();

		$rs = $this->db->select('DISTINCT ur.card_id, CONCAT(c.card_fname," ",c.card_lname) AS name', false)
				->from('access_user_role AS ur')
				->where_in('ur.roles_sub_id', $subrole_ids)
				->join('card AS c', 'c.id=ur.card_id', 'left')
				->get();

		if ($rs->num_rows() == 0) return array();

		return $rs->result_array();
	}

	function save_acl($data) {
		$data['created_card_id'] = $this->UserM->get_card_id();
		$data['created_stamp'] = get_current_stamp();

		parent::save($data, $this->id_field);
	}

	function delete_acl($id) {
		$this->db->where('id', $id)
				->limit(1)
				->delete($this->table);
	}
}