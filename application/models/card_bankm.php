<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Card_BankM extends MY_Model {

	public $data_fields = array(
		'card_id' => array(
			'type' => 'id'
		),
		'name' => array(
			'type' => 'text'
		),
		'account_number' => array(
			'type' => 'text'
		),
		'sort_code' => array(
			'type' => 'text'
		),
		'swift' => array(
			'type' => 'text'
		),
		'ibank' => array(
			'type' => 'text'
		),
	);

	function __construct() {
		parent::__construct();

		$this->app = 'card';
		$this->table = 'card_bank';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}


}