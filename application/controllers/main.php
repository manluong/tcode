<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends MY_Controller {
	public function index()	{
		if ($this->User->is_logged_in()) redirect('/dashboard');

		$statuses = array(
			1 => 'return user',
			2 => 'login ok',
			3 => 'wrong password',
			4 => 'logout',
			5 => 'you must be new here, log in!',
			6 => 'no such username',
			7 => 'username not activated',
		);

		$html = array();
		$html['status_message'] = $statuses[$this->User->status];
		$html['status'] = $this->User->status;

		$this->data['html'] = $this->load->view('main_login', $html, true);
		$this->data['outputdiv'] = 1;

		$this->layout = $this->Html->html_template($this->App->actions);

		$this->output();
	}

	public function about() {
		$this->load->view('main_about','');
	}

	public function login() {
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		if ($this->User->is_valid_password($username, $password)) {
			$this->User->login($username);
		} else {
			$this->index();
			return false;
		}

		redirect('/dashboard');
	}

	public function logout() {
		$this->User->logout();
		redirect('/');
	}

	public function test($id='none') {
		echo 'main test id:',$id,'<br />';
		echo '<pre>',print_r($this->ACL->url,true),'</pre>';

	}
}