<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Card_RoleM extends MY_Model {

	public $data_fields = array(
		'card_id' => array(
			'type' => 'id'
		),
		'role_id' => array(
			'type' => 'id'
		),
	);

	function __construct() {
		parent::__construct();

		$this->app = 'card';
		$this->table = 'access_user_role';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}
}