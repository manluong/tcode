<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Signup extends MY_Controller {

	function __construct() {
		parent::__construct();

		//if (APP_ROLE!='TBOSS') redirect('http://my.8force.net/signup');
	}

	function index() {
		$html['errors'] = $this->session->flashdata('signup_errors');
		$html['signup'] = $this->session->userdata('signup_info');

		$data = array();
		$data['html'] = $this->load->view(get_template().'/signup/index', $html, TRUE);

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
	}

	function step2() {
		$signup_info = array();
		$signup_info['name'] = $this->input->post('name');
		$signup_info['email'] = $this->input->post('email');
		$signup_info['domain'] = $this->input->post('domain');
		$signup_info['username'] = $this->input->post('username');
		$signup_info['password'] = $this->input->post('password');

		$this->load->model('SignupM');
		$this->session->set_userdata('signup_info', $signup_info);

		if ($this->SignupM->validate_details($signup_info)) {
			redirect('/signup/step3');
		} else {
			$this->session->set_flashdata('signup_errors', $this->SignupM->messages);
			redirect('/signup');
		}
	}

	function step3() {
		$signup_info = $this->session->userdata('signup_info');

		$data = array();
		$data['html'] = $this->load->view(get_template().'/signup/process', $signup_info, TRUE);

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
	}

	function ajax_begin_setup() {
		$this->load->model('SignupM');

		$signup_info = $this->session->userdata('signup_info');

		$result = $this->SignupM->setup_account($signup_info);

		if ($result === true) {
			//create folder in storage system
			$this->load->library('FileL');
			$this->filel->create_physical_folder('tenants/'.$signup_info['domain'].'/');

			//send welcome email
			$welcome_message = '<p>You can now access your account at: <a href="http://'.$signup_info['domain'].'.8force.net/">http://'.$signup_info['domain'].'.8force.net/</a></p>';
			$welcome_message .= '<p>Your username is: '.$signup_info['username'].'</p>';
			$this->load->library('EmailL');
			$this->emaill->set_to(array($signup_info['email']))
					->set_toname(array($signup_info['name']))
					->set_subject('Welcome to 8force')
					->set_content($welcome_message)
					->set_from('support@8force.com')
					->set_fromname('8Force')
					->send_email();

			$this->session->unset_userdata('signup_info');
		}

		$this->RespM->set_success($result)
				->set_details($this->SignupM->get_messages())
				->output_json();
	}

	function test() {
		$this->load->library('FileL');

		$this->filel->save_new('contents of the first file', 'a/b', 'something1.txt');
		$this->filel->save_new('contents of the second file', 'a/b', 'something2.txt');

		echo 'done';
	}

	function read() {
		$this->load->library('FileL');
		$file = $this->filel->read(1);
		echo '<pre>', print_r($file, TRUE), '</pre>';

		$file = $this->filel->read(2);
		echo '<pre>', print_r($file, TRUE), '</pre>';
	}

	function overwrite() {
		$this->load->library('FileL');
		$this->filel->save_existing('contents of the third file', 1, 'something3.txt');
		$this->filel->save_existing('contents of the fourth file', 2, 'something4.txt', TRUE);
	}

	function dir_one() {
		$this->load->model('DocsM');
		$dir_id = $this->DocsM->create_dir('a/b/c/d/e/f/g');

		echo $this->DocsM->get_dir_path($dir_id);
	}
}