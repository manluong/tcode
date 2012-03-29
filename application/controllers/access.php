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

	public function index()	{
		if ($this->UserM->is_logged_in()) redirect('/dashboard');

		$html = array();
		$html['status_message'] = $this->statuses[$this->UserM->status];
		$html['status'] = $this->UserM->status;
		$html['company_name'] = 'Telcoson'; //TODO: This should be the company name of the tenant.

		$data = array();
		$data['html'] = $this->load->view('/'.get_template().'/access/login', $html, TRUE);

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
	}

	public function about() {
		$this->load->view('access_about','');
	}

	public function login() {
		$username = $this->input->post('access_user_username');
		$password = $this->input->post('access_user_pw');

		if ($this->UserM->is_valid_password($username, $password)) {
			$this->UserM->login($username);
			execute_return_url();
			redirect('/dashboard');
		}

		$this->index();
	}

	public function logout() {
		$this->UserM->logout();
		redirect('/');
	}

	public function test($id='none') {
		if (ENVIRONMENT == 'production') return FALSE;

		echo 'main test id:',$id,'<br />';
		echo '<pre>',print_r($this->ACLM->url,true),'</pre>';
	}

	public function ajax_login() {
		$username = $this->input->post('access_user_username');
		$password = $this->input->post('access_user_pw');

		$success = false;

		if ($this->UserM->is_valid_password($username, $password)) {
			$this->UserM->login($username);
			$success = true;
		}

		$this->RespM->set_message($this->statuses[$this->UserM->status])
			->set_type('view')
			->set_template('')
			->set_success($success)
			->set_title('Login')
			->output_json();
	}
}
