<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class DS_Card_Name extends DatasetM {
	function __construct() {
		parent::__construct();

		$this->load('card_name');
	}

}