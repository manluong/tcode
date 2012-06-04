<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Helpdesk_GroupM extends MY_Model {

	public $data_fields = array(
		'id' => array(
			'type' => 'id'
		),
		'parent_id' => array(
			'type' => 'id'
		),
		'name' => array(
			'type' => 'text'
		),
		'foreign_key' => array(
			'type' => 'id'
		),
		'lft' => array(
			'type' => 'id'
		),
		'rght' => array(
			'type' => 'id'
		),
	);

	function __construct() {
		parent::__construct();

		$this->table = 'access_ro';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}


}