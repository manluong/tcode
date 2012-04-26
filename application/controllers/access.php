<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access extends MY_Controller {
	var $statuses = array(
			1 => 'return user',
			2 => 'login ok',
			3 => 'wrong password',
			4 => 'logout',
			5 => 'you must be new here, log in!',
			6 => 'no such username',
			7 => 'username not activated',
		);

	function __construct() {
		$this->allow_unauthed_access = TRUE;

		parent::__construct();
	}

	public function index()	{
		if ($this->UserM->is_logged_in()) redirect('/dashboard');

		$html = array();
		$html['status_message'] = $this->statuses[$this->UserM->status];
		$html['status'] = $this->UserM->status;
		$html['company_name'] = 'Telcoson'; //TODO: This should be the company name of the tenant.

		$this->data['content'] = $this->load->view(get_template().'/access/login', $html, TRUE);

		$this->_output();
	}

	public function about() {
		$this->load->view('access_about','');
	}

	public function login() {
		$email = $this->input->post('login_email');
		$password = $this->input->post('login_password');

		if ($this->UserM->is_valid_password($email, $password)) {
			$this->UserM->login($email);
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

		if ($this->UserM->is_valid_password($email, $password)) {
			$this->UserM->login($email);
			$success = true;
		}

		$this->RespM->set_message($this->statuses[$this->UserM->status])
			->set_type('view')
			->set_template('')
			->set_success($success)
			->set_title('Login')
			->output_json();
	}

	public function ajax_logout() {
		$this->UserM->logout();

		$this->RespM->set_message('You have been logged out.')
			->set_type('view')
			->set_template('')
			->set_success(TRUE)
			->set_title('Login')
			->output_json();
	}
}
