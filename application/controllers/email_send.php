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

		$result = $this->emaill->send_email();
		//$result = $this->emaill->debug();die();

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('success' => $result ? '1' : '0')));
	}

	function test() {
		$replace = array(
			'keys' => array('%name%', '%result%'),
			'values' => array(array('Boo1', 'Boo2'), array('Success1', 'Success2'))
		);

		$this->emaill
			->set_card(211)
			->set_subject('test')
			//->set_attachment_id('ac57b26f30fcb8a3134416f6744fce07')
			->set_template('email', 'test')
			->set_replace_value($replace)
			->set_from('docs@telcoson.com', 'Docs');

		echo ($this->emaill->send_email()) ? 'sent' : 'not sent';
	}
}