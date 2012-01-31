<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Helloworld extends MY_Controller {

	function __construct() {
		parent::__construct();

	}
	
	function sendhtml() {
		$data = array();
		$data['data'] = "I am a some HTML";
		$data['isoutput'] = 1;
		$data['isdiv'] = 0;
		
		return($data);
	}

	function sendjson() {
		$data = array();
		$data['json'] = '{"element_type":"helloworld","message":"Hello World!"}';
		$data['isoutput'] = 1;
		$data['isdiv'] = 0;
		
		return($data);
	}

	function sendjs() {
		$data = array();
		$data['json'] = '//js
		alert("Hello JS");
		';
		$data['isoutput'] = 1;
		$data['isdiv'] = 0;
		
		return($data);
	}	
	
	
}