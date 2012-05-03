<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class DS_Comment extends DatasetM {
	function __construct() {
		parent::__construct();

		$this->load('ds_commen');
	}

}