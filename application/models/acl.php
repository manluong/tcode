<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class ACL extends CI_Model {
	var $url = array();

	function __construct() {
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
		if (!$this->User->is_logged_in() && !$this->App->has_public_access()) {
			header( 'Location: /main/login/'.set_return_url(TRUE));
			exit;
		} elseif ($this->User->is_logged_in() && !$this->User->is_admin() && !$this->App->has_public_access()) {
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
		$apps_action = $this->App->actions;

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
		$where['accessgp'] = "access_rights_gpmaster = '".$this->User->info['accessgp']."'";
		$where['app'] = " AND access_rights_app = '".$this->url['app']."'";
		$where['cardid'] = " AND access_rights_cardid = '".$this->User->info['cardid']."'";
		$where['actiongp'] = " AND access_rights_actiongp = '".$apps_action['core_apps_action_gp']."'";

		if ($this->User->info['subgp']) {
			$subgp = join(',',$this->User->info['subgp']);
			$where['subgp'] = " AND access_rights_gpsub IN ($subgp)";
		}

		$where['cardid_empty'] = " AND access_rights_cardid = ''";
		$where['actiongp_empty'] = " AND access_rights_actiongp = ''";
		$where['subgp_empty'] = " AND access_rights_gpsub = ''";


		//sql check sqeunce
		$sql = array();

		//with acccessgp,app,actiongp,cardid + subgp
		$sql[0] .= $where['accessgp'];
		$sql[0] .= $where['subgp'];
		$sql[0] .= $where['app'];
		$sql[0] .= $where['cardid'];
		$sql[0] .= $where['actiongp'];

		//with acccessgp,app,actiongp,cardid
		$sql[1] .= $where['accessgp'];
		$sql[1] .= $where['subgp_empty'];
		$sql[1] .= $where['app'];
		$sql[1] .= $where['cardid'];
		$sql[1] .= $where['actiongp'];


		//with acccessgp,app,actiongp + subgp
		$sql[2] .= $where['accessgp'];
		$sql[2] .= $where['subgp'];
		$sql[2] .= $where['app'];
		$sql[2] .= $where['cardid_empty'];
		$sql[2] .= $where['actiongp'];

		//with acccessgp,app,actiongp
		$sql[3] .= $where['accessgp'];
		$sql[3] .= $where['subgp_empty'];
		$sql[3] .= $where['app'];
		$sql[3] .= $where['cardid_empty'];
		$sql[3] .= $where['actiongp'];

		//with acccessgp,app,cardid + subgp
		$sql[4] .= $where['accessgp'];
		$sql[4] .= $where['subgp'];
		$sql[4] .= $where['app'];
		$sql[4] .= $where['cardid'];
		$sql[4] .= $where['actiongp_empty'];

		//with acccessgp,app,cardid
		$sql[5] .= $where['accessgp'];
		$sql[5] .= $where['subgp_empty'];
		$sql[5] .= $where['app'];
		$sql[5] .= $where['cardid'];
		$sql[5] .= $where['actiongp_empty'];

		//with acccessgp,app + subgp
		$sql[6] .= $where['accessgp'];
		$sql[6] .= $where['subgp'];
		$sql[6] .= $where['app'];
		$sql[6] .= $where['cardid_empty'];
		$sql[6] .= $where['actiongp_empty'];

		//with acccessgp,app
		$sql[7] .= $where['accessgp'];
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
					if ($result['allow'] != 1){
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
			$result = ($this->url['id_plain']!=0 && $this->User->info[$access['access_rights_matchthisidtype']] == $this->url['plain_id'])
				? 1
				: 2;
		}

		return $result;
	}

}