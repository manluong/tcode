<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Helpdesk_PriorityM extends MY_Model {

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

		$this->app = 'helpdesk';
		$this->table = 'a_priority';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}
}