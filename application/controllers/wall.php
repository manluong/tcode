<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Wall extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	function index() {
		$i = $this->LogM->get_wall(get_current_stamp());
		print_r($i);
	}
}