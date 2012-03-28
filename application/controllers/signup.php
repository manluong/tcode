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
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = '';
		$data['div']['element_name'] = '';
		$data['div']['element_id'] = '';

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
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = '';
		$data['div']['element_name'] = '';
		$data['div']['element_id'] = '';

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
	}

	function ajax_begin_setup() {
		$signup_info = $this->session->userdata('signup_info');
		$this->load->model('SignupM');

		$result = $this->SignupM->setup_account($signup_info);
		$messages = $this->SignupM->get_messages();

		$this->load->library('S3');
		S3::putBucket('t-'.$signup_info['domain'], S3::ACL_PRIVATE, 'ap-southeast-1');

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

		$this->RespM->set_success($result)
				->set_details($messages)
				->output_json();
	}
}