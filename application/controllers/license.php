<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class License extends MY_Controller {

	function __construct() {
		parent::__construct();

		if (APP_ROLE != 'TBOSS') die('You Shall Not Pass.');

		$this->load->model('LicenseM');
	}

	function export_licenses() {
		$this->LicenseM->export_license_rules(1);
	}
}