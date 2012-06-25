<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class UserM extends MY_Model {
	var $id = 0;
	var $email = '';

	var $status = 5;
	// status:
		// 1=return hasid ok
		// 2=login and ok
		// 3=login and failed password
		// 4=logout
		// 5=new arrival, no hasid, no login attempt, no logout

		// 6=login and failed no such username
		// 7=login and failed user not active
		// 8=session timeout
		// 9=user account expired

	var $info = array();

	//var $info = array();

	var $logged_in = FALSE;

	var $loguid = 0;

	function __construct() {
		$this->table = 'card';
		$this->id_field = 'id';
		$this->cache_enabled = TRUE;

		parent::__construct();

		if (!$this->is_cli && !$this->is_callback) $this->setup_loguid();
	}

	public function debug() {
		echo '<p>';
		echo 'email: <pre>',print_r($this->get_email($this->id, 'all'), true),'</pre>';
		echo 'loguid: ',$this->loguid,'<br />';
		echo 'info: <pre>',print_r($this->info, true),'</pre>';
		echo '</p>';
	}

	public function is_logged_in() {
		return $this->logged_in;
	}

	public function is_admin() {
		if (!isset($this->info['role']['name'])) return FALSE;
		if ($this->info['role']['name'] !== 'Staff') return FALSE;

		foreach($this->info['sub_roles'] AS $role_id=>$role_name) {
			if ($role_name == 'Administrators') return TRUE;
		}

		return FALSE;
	}

	public function is_staff() {
		return ($this->info['role']['name'] == 'Staff');
	}

	public function is_client() {
		return ($this->info['role']['name'] == 'Client' || $this->info['role']['name'] == 'Client (Additional)');
	}

	public function is_vendor() {
		return ($this->info['role']['name'] == 'Vendor');
	}

	public function get_loguid() {
		return $this->loguid;
	}

	public function get_card_id() {
		return $this->id;
	}
	public function get_timezone() {
		return $this->info['timezone'];
	}

	public function get_name() {
		if (!$this->logged_in) return '';

		if ($this->id == 0) return 'system';

		return $this->info['first_name'].' '.$this->info['last_name'];
	}


	//$type = primary | secondary | all
	public function get_email($card_id='', $type='primary') {
		if ($card_id == '') $card_id = $this->id;

		$this->db->select('email')
			->from('card_email')
			->where('card_id', $card_id);

		if ($type == 'primary') {
			$this->db->where('is_default', 1)
					->limit(1);
		} elseif ($type == 'secondary') {
			$this->db->where('is_default', 0);
		}

		$rs = $this->db->get();

		if ($rs->num_rows() == 0) return FALSE;

		if ($type == 'primary') {
			$r = $rs->row_array();
			$results = $r['email'];
		} else {
			$results = array();
			foreach($rs->result_array() AS $r) {
				$results[] = $r['email'];
			}
		}

		return $results;
	}

	public function is_valid_password($email, $password) {
		$rs = $this->db->select()
				->from('access_user AS u')
				->join('card_email AS e', 'e.card_id=u.card_id')
				->where('e.email', $email)
				->where('e.is_default', 1)
				->limit(1)
				->get();

		//no username
		if ($rs->num_rows()==0) {
			$this->status = 6;
			return FALSE;
		}

		$access_user = $rs->row_array();

		//got username but not active
		if ($access_user['status'] != '1') {
			$this->status = 7;
			return FALSE;
		}

		//login has expired
		if ($access_user['expire_stamp'] !== NULL) {
			if (strtotime($access_user['expire_stamp']) < time()) {
				$this->status = 9;
				return FALSE;
			}
		}

		//if password mismatch
		$password = f_password_encrypt($password);
		if ($password !== $access_user['password']) {
			$this->status = 3;
			return FALSE;
		}

		return TRUE;
	}

	public function login($email) {
		$rs = $this->db->select('card_id')
				->from('card_email')
				->where('email', $email)
				->where('is_default', 1)
				->limit(1)
				->get();

		$access_user = $rs->row_array();

		$this->id = $access_user['card_id'];

		$this->status = 2;
		$this->info = $this->get_info($access_user['card_id']);
		$this->logged_in = TRUE;

		$this->session->set_userdata('id', $this->id);
		$this->session->set_userdata('user_info', $this->info);
	}

	public function logout() {
		$this->session->sess_destroy();
		$this->status = 4;
		return TRUE;
	}

	//refresh current user's info. used when he updates his particulars
	public function refresh_info() {
		if ($this->id === FALSE || $this->id === NULL) return FALSE;

		$this->info = $this->get_info($this->id);
		$this->session->set_userdata('user_info', $this->info);
	}

	//setup current user's info
	public function setup() {
		// Already Login
		$this->id = $this->session->userdata('id');

		if ($this->id === FALSE || $this->id === NULL) {
			if ($this->input->cookie('ci_session') && $this->has_return) {
				$this->status = 8;
			}

			return FALSE;
		}

		$this->status = 1;
		$this->info = $this->session->userdata('user_info');
		$this->logged_in = TRUE;
	}

	//gets a user's card details and it's roles and subroles
	public function get_info($card_id){
		$this->load->model('CardM');
		$result = $this->CardM->get($card_id);

		$result['role'] = $this->AclM->get_user_role_info($card_id);
		$result['sub_roles'] = $this->AclM->get_user_subroles($card_id);

		return $result;
	}

	private function setup_loguid() {
		$this->loguid = $this->session->userdata('loguid');
		if ($this->loguid==0) {
			$this->loguid = time().rand(1000, 9999);
			$this->session->set_userdata('loguid', $this->loguid);
		}
	}
}