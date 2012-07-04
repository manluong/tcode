<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_send extends MY_Controller {
	function __construct() {
		$this->allow_unauthed_access = TRUE;

		parent::__construct();
		$this->load->library('EmailL');
	}

	function index() {
		$docs = $this->input->post('docs');
		$ver = $this->input->post('ver');
		$attachment_id = array();
		for($i=0;$i<count($docs);$i++) {
			$attachment_id[$docs[$i]] = ($ver[$i] !== '0') ? $ver[$i] : '';
		}
		$this->emaill->set_to($this->input->post('to'), $this->input->post('toname'))
			->set_attachment_id($attachment_id)
			->set_from($this->input->post('from'), $this->input->post('fromname'))
			->set_subject('test')
			->set_content('some text');

		$result = $this->emaill->send();
		//$result = $this->emaill->debug();die();

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('success' => $result ? '1' : '0')));
	}

	function test() {
		$replace = array(
			'keys' => array('%name%', '%result%', '%subject%'),
			'values' => array('Boo1', 'Success1', 'SubOne')
		);

		$this->emaill
			//->set_card(123456)
			->set_from('docs', 'Docs')
			->set_to('luongtheman87@yahoo.com', 'Test') 
			//->set_attachment_id('ac57b26f30fcb8a3134416f6744fce07')
			->set_template('email', 'test')
			->set_single_replace_value($replace);

		echo ($this->emaill->send()) ? 'sent' : 'not sent';
	}
}