<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Access_UserM extends MY_Model {

	public $data_fields = array(
		'card_id' => array(
			'type' => 'id'
		),
		'password' => array(
			'type' => 'text'
		),
		'status' => array(
			'type' => 'selection',
			'default' => 0,
			'options' => array(
				0 => 'access-user_status-0',
				1 => 'access-user_status-1',
			)
		),
		'is_default' => array(
			'type' => 'numeric',
			'default' => 0
		),
	);

	function __construct() {
		parent::__construct();

		$this->table = 'access_user';
		$this->cache_enabled = TRUE;
	}

	function save($data=FALSE) {
		if ($data === FALSE) return FALSE;

		$data['password'] = f_password_encrypt($data['password']);

		return parent::save($data);
	}
}