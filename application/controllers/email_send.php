<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_send extends MY_Controller {
	function __construct() {
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
		$this->emaill->set_to($this->input->post('to'))
			->set_toname($this->input->post('toname'))
			->set_attachment_id($attachment_id)
			->set_from($this->input->post('from'))
			->set_fromname($this->input->post('fromname'))
			->set_subject('test')
			->set_content('some text');

		$result = $this->emaill->send_email();
		//$result = $this->emaill->debug();die();

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('success' => $result ? '1' : '0')));
	}

	function test2() {
	$this->emaill->set_type('card')
            ->set_type_id(array(2))
            ->set_to(array('roy@telcoson.com'))
            ->set_toname(array('roygmail'))
			->set_bcc(array('roy.wong.80@gmail.com'))
            ->set_template('testplate')
            ->set_content('hhhh')
            ->set_subject('test')
            ->set_attachment_id(array(2=>'',3=>'')) // docs_id => ver_id or just docs_id => ''
            ->set_replace_value(array('keys'=>array('%name%', '%result%'), 'values'=>array(array('Roy'), array('Success!!'))))
            ->set_from('docs@telcoson.com')
            ->set_fromname('Docs');
			//$this->emaill->debug(); // prints the parameters to send
			$i = $this->emaill->send_email(); var_dump($i);// actual email sending returns TRUE or FALSE

	}

	function test() {
		$url = 'http://sendgrid.com/';
		$user = 'tcsteam';
		$pass = 'express08)*';

		$fileName = 'gravatar-140.png';
		//$filePath = dirname(__FILE__);

		$filePath = 'tmp';

		$params = array(
			'api_user'  => $user,
			'api_key'   => $pass,
			'to'        => 'roy@telcoson.com',
			'subject'   => 'test of file sends',
			'html'      => '<p> the HTML </p>',
			'text'      => 'the plain text',
			'from'      => 'example@sendgrid.com',
			'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		);

		print_r($params);

		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);

		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);

		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);

		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

		// obtain response
		$response = curl_exec($session);
		log_message('debug', 'Message sent!!');
		curl_close($session);

		// print everything out
		print_r($response);
		var_dump($response);
	}
}