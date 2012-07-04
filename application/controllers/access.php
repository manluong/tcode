<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access extends MY_Controller {
	function __construct() {
		$this->allow_unauthed_access = TRUE;

		parent::__construct();
	}

	public function index()	{
		if ($this->UserM->is_logged_in()) redirect('/dashboard');

		$html = array();
		$html['status'] = $this->UserM->status;
		$html['data'] = $this->data;
		$html['cookie_login'] = FALSE;

		//Checks if there's a cookie_hash set, used to remember username(email) for login
		$login_cookie_hash = $this->input->cookie('oreo', TRUE);
		if (!empty($login_cookie_hash)) {
			$user = $this->UserM->get_by_cookie_hash($login_cookie_hash);
			$html['cookie_login'] = TRUE;
			$html['user_avatar'] = $user['avatar'];
		}

		$this->load->view(get_template().'/access/login', $html);
	}

	public function change_user() {
		$domain = (ENVIRONMENT == 'production')
					? $this->domain.'.8force.net'
					: $this->domain.'.8force.local';

		$this->input->set_cookie('oreo', NULL, NULL, $domain);

		redirect('/access');
	}

	public function about() {
		$this->load->view('access_about','');
	}

	public function login() {
		$email = $this->input->post('login_email');
		$password = $this->input->post('login_password');

		$domain = (ENVIRONMENT == 'production')
					? $this->domain.'.8force.net'
					: $this->domain.'.8force.local';

		if ($email == FALSE) {
			$login_cookie_hash = $this->input->cookie('oreo', TRUE);
			if ($login_cookie_hash !== FALSE) {
				$card_id = $this->UserM->get_by_cookie_hash($login_cookie_hash);
				if (empty($card_id)) {
					$this->input->set_cookie('oreo', NULL, NULL, $domain);
				} else {
					$email = $this->UserM->get_email($card_id);
				}
			}
		}

		if ($this->UserM->is_valid_password($email, $password)) {
			$this->UserM->login($email);

			//Set a cookie(cookie_hash) for remembering username(email) used to login
			//Store cookie for 30 days.
			$hash = $this->UserM->assign_cookie_hash();
			$this->input->set_cookie('oreo', $hash, 2592000, $domain);

			execute_return_url();
			redirect('/dashboard');
		}

		$this->index();
	}

	public function logout() {
		$this->UserM->logout();
		redirect('/');
	}

	public function ajax_login() {
		$email = $this->input->post('login_email');
		$password = $this->input->post('login_password');

		$success = false;
		$user = array();

		if ($this->UserM->is_valid_password($email, $password)) {
			$this->UserM->login($email);
			$user = $this->UserM->info;
			$success = true;
		}

		$this->RespM->set_message($this->lang->line('access-login_status-'.$this->UserM->status))
			->set_success($success)
			->set_title('Login')
			->set_details($user)
			->output_json();
	}

	public function ajax_logout() {
		$this->UserM->logout();

		$this->RespM->set_message($this->lang->line('access-logout-success'))
			->set_type('view')
			->set_template('')
			->set_success(TRUE)
			->set_title('Login')
			->output_json();
	}
}
