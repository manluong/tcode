<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access extends MY_Controller {
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

		$data = array();
		$data['html'] = $this->load->view('/'.get_template().'/access/login', $html, TRUE);
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = 'Login';
		$data['div']['element_name'] = 'loginwin';
		$data['div']['element_id'] = 'divlogin';

		$this->data[] = $data;

		$this->Html->load_format();

		$this->output();
	}

	public function about() {
		$this->load->view('access_about','');
	}

	public function login() {
		$username = $this->input->post('access_user_username');
		$password = $this->input->post('access_user_pw');

		if ($this->User->is_valid_password($username, $password)) {
			$this->User->login($username);
		} else {
			$this->index();
			return false;
		}

		execute_return_url();
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
