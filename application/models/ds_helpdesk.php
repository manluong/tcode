<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class DS_Helpdesk extends DatasetM {
	function __construct() {
		parent::__construct();

		$this->load('ds_helpdesk');
	}

}