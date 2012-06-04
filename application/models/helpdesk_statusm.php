<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Helpdesk_StatusM extends MY_Model {

	public $data_fields = array(
		'id' => array(
			'type' => 'id'
		),
		'name' => array(
			'type' => 'text'
		),
	);

	function __construct() {
		parent::__construct();

		$this->table = 'a_status';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}


}