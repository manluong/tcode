<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class UserM extends MY_Model {
	var $id = array();
	var $username = '';

	var $status = 5;
	// status:
		// 1=return hasid ok
		// 2=login and ok
		// 3=login and failed password
		// 4=logout
		// 5=new arrival, no hasid, no login attempt, no logout

		// 6=login and failed no such username
		// 7=login and failed user not active

	var $info = array();

	//var $info = array();

	var $admin = FALSE;
	var $logged_in = FALSE;

	var $loguid = 0;

	function __construct() {
		$this->table = 'users';
		$this->id_field = 'card_id';
		$this->cache_enabled = TRUE;

		parent::__construct();

		$this->setup_loguid();
	}

	public function debug() {
		echo '<p>';
		echo 'username: ',$this->username,'<br />';
		echo 'loguid: ',$this->loguid,'<br />';
		echo 'admin: ',$this->admin,'<br />';
		echo 'info: <pre>',print_r($this->info, true),'</pre>';
		echo '</p>';
	}

	public function is_logged_in() {
		return $this->logged_in;
	}

	public function is_admin() {
		return $this->admin;
	}

	public function get_loguid() {
		return $this->loguid;
	}


	public function is_valid_password($username, $password) {
		$rs = $this->db->select()
				->where('access_user_username', $username)
				->get('access_user', 1);

		//no username
		if ($rs->num_rows()==0) {
			$this->status = 6;
			return FALSE;
		}

		$access_user = $rs->row_array();

		//got username but not active
		if ($access_user['access_user_status'] != '1') {
			$this->status = 7;
			return FALSE;
		}

		$password = f_password_encrypt($password);
		$rs = $this->db->select()
				->where('access_p_uid', $access_user['access_user_id'])
				->where('access_p_p', $password)
				->get('access_p', 1);

		if ($rs->num_rows()==0) {
			$this->status = 3;
			return FALSE;
		}

		return TRUE;
	}

	public function login($username) {
		$rs = $this->db->select()
				->where('access_user_username', $username)
				->get('access_user', 1);
		$access_user = $rs->row_array();

		$this->id = $access_user['access_user_id'];
		$this->username = $access_user['access_user_username'];
		$this->status = 2;
		$this->logged_in = TRUE;

		$core_app_userinfo = $this->core_app_userinfo($username);

		//echo 'startt<pre>',print_r($core_app_userinfo,true),'</pre>endd';
		//die();

		$this->info['name'] = $core_app_userinfo['name'];
		$this->info['cardid'] = $core_app_userinfo['cardid'];
		$this->info['accessgp'] = $core_app_userinfo['accessgp'];
		$this->info['clientid'] = $core_app_userinfo['clientid'];
		$this->info['staffid'] = $core_app_userinfo['staffid'];
		$this->info['vendorid'] = $core_app_userinfo['vendorid'];
		$this->info['memberid'] = $core_app_userinfo['memberid'];
		$this->info['subgp'] = $core_app_userinfo['subgp'];

		$this->admin = ($this->info['accessgp']==1);
		/*
		//platform
		if ($this->platform == "1" || $this->platform == "2" || $this->platform == "3") {
			$this->platform = $platform;
		} else {
			//go detect platform
			$sess_userdata['platform'] = $this->platform;
		}
		*/

		$this->session->set_userdata('id', $this->id);
		$this->session->set_userdata('username', $this->username);
		$this->session->set_userdata('user_info', $this->info);
	}

	public function logout() {
		$this->session->sess_destroy();
		$this->status = 4;
	}


	private function setup_loguid() {
		$this->loguid = $this->session->userdata('loguid');
		if ($this->loguid==0) {
			$this->loguid = time().rand(1000, 9999);
			$this->session->set_userdata('loguid', $this->loguid);
		}
	}

	public function setup() {
		// Already Login
		$this->id = $this->session->userdata('id');
		$this->username = $this->session->userdata('username');

		if ($this->id) {
			$this->status = 1;
			$this->info = $this->session->userdata('user_info');
			$this->logged_in = TRUE;
			$this->admin = ($this->info['accessgp']==1);
		}
	}

	private function core_app_userinfo($username){
		$rs = $this->db->select()
				->join('card', 'access_user.access_user_cardid=card.card_id', 'left')
				->where('access_user_username', $username)
				->get('access_user', 1);
		$result = $rs->row_array();

		$thisresult = array();
		if ($result['card_fname']){
			$thisresult['name'] = $result['card_fname'];
		}elseif ($result['card_lname']){
			$thisresult['name'] = $result['card_lname'];
		}elseif ($result['card_orgname']){
			$thisresult['name'] = $result['card_orgname'];
		}

		$thisresult['cardid'] = $result['card_id'];

		$thisresult['subgp'] = $this->core_app_getsubgp($thisresult['cardid']);
		$core_app_getaccessgp = $this->core_app_getaccessgp($thisresult['cardid']);

		$thisresult = array_merge($thisresult, $core_app_getaccessgp);

		return $thisresult;
	}

	private function core_app_getsubgp($cardid){
		$rs = $this->db->select('access_usergp_gpsub')
				->where('access_usergp_cardid', $cardid)
				->get('access_usergp');

		$result = array();
		if ($rs->num_rows()>0){
			foreach ($rs->result_array() as $field1) {
				$result[] = $field1['access_usergp_gpsub'];
			}
		}

		return $result;
	}

	private function core_app_getaccessgp($cardid){
		$rs = $this->db->select('access_link_gpmaster')
				->where('access_link_cardid', $cardid)
				->get('access_link', 1);

		$result = array();
		$temp = $rs->row_array();
		$result['accessgp'] = $temp['access_link_gpmaster'];

		switch($result['accessgp']){
			case '1':  break;
			case '2':
				//staff
				$rs = $this->db->select('staff_id')
						->where('staff_cardid', $cardid)
						->get('staff', 1);
				if ($rs->num_rows()>0) {
					$temp = $rs->row_array();
					$result['staffid'] = $temp['staff_id'];
					$result['en']['staffid'] = f_thisid_encode($temp['staff_id']);
				}
				break;
			case '3':
				//client
				$rs = $this->db->select('client_id')
						->where('client_cardid', $cardid)
						->get('client', 1);
				if ($rs->num_rows()>0) {
					$temp = $rs->row_array();
					$result['clientid'] = $temp['client_id'];
					$result['en']['clientid'] = f_thisid_encode($temp['client_id']);
				}
				break;
			case "4":  break;
			case "5":
				//vendor
				$rs = $this->db->select('vendor_id')
						->where('vendor_cardid', $cardid)
						->get('vendor', 1);
				if ($rs->num_rows()>0) {
					$temp = $rs->row_array();
					$result['vendorid'] = $temp['vendor_id'];
					$result['en']['vendorid'] = f_thisid_encode($temp['vendor_id']);
				}
				break;
			case "6":  break;
			case "7":  break;
			case "8":
				$rs = $this->db->select('spu_id')
						->where('spu_cardid', $cardid)
						->get('spu', 1);
				if ($rs->num_rows()>0) {
					$temp = $rs->row_array();
					$result['spuid'] = $temp['spu_id'];
					$result['en']['spuid'] = f_thisid_encode($temp['spu_id']);
				}
				break;
		}

		return $result;
	}

}