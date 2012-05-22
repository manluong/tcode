<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Card_TelM extends MY_Model {

	public $data_fields = array(
		'card_id' => array(
			'type' => 'id'
		),
		'type' => array(
			'type' => 'selection',
			'default' => 0,
			'options' => array(
				0 => 'core_select-select-card_addtype-office',
				1 => 'core_select-select-card_addtype-home',
				2 => 'core_select-select-card_teltype-work',
				3 => 'core_select-select-card_teltype-mobile',
				4 => 'core_select-select-card_teltype-home',
				5 => 'core_select-select-card_teltype-fax',
			),
		),
		'number' => array(
			'type' => 'text'
		),
		'country' => array(
			'type' => 'text'
		),
		'area' => array(
			'type' => 'text'
		),
		'extension' => array(
			'type' => 'text'
		),
	);

	function __construct() {
		parent::__construct();

		$this->table = 'card_tel';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}


}