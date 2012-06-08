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

		$this->data['content'] = $this->load->view(get_template().'/access/login', $html, TRUE);

		$this->_do_output();
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
		$user = array();

		if ($this->UserM->is_valid_password($email, $password)) {
			$this->UserM->login($email);
			$user = $this->UserM->info();
			$success = true;
		}

		$this->RespM->set_message($this->lang->line('access-login_status-'.$this->UserM->status))
			->set_success($success)
			->set_title('Login')
			->set_detail($user)
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
