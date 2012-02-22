<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class HelloworldM extends DatasetM {
	function __construct() {
		parent::__construct();

		$this->load('ds_helloworld');
	}

}