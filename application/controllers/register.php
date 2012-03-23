<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends MY_Controller {

	function __construct() {
		parent::__construct();

	}

	function index() {
		$data = array();
		$data['html'] = $this->load->view(get_template().'/register/index', '', TRUE);
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
		$name = $this->input->post('name');
		$email = $this->input->post('email');
		$domain = $this->input->post('domain');
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$this->load->model('RegisterM');

		if ($this->RegisterM->setup_account($name, $email, $domain, $username, $password)) {
			$this->session->set_flashdata('signup_name', $name);
			$this->session->set_flashdata('signup_domain', $domain);
			redirect('/register/step3');
		} else {
			redirect('/register');
		}
	}

	function step3() {
		$html = array(
			'name' => $this->session->flashdata('signup_name'),
			'domain' => $this->session->flashdata('signup_domain')
		);
		$data = array();
		$data['html'] = $this->load->view(get_template().'/register/success', $html, TRUE);
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = '';
		$data['div']['element_name'] = '';
		$data['div']['element_id'] = '';

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
	}
}