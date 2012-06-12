<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Card_ExtraM extends MY_Model {

	public $data_fields = array(
		'card_id' => array(
			'type' => 'id'
		),
		'marital' => array(
			'type' => 'selection',
			'default' => 0,
			'options' => array(
				0 => 'core_select-select-card_marital-single',
				1 => 'core_select-select-card_marital-married',
				2 => 'core_select-select-card_marital-other'
			)
		),
		'child' => array(
			'type' => 'numeric',
			'default' => 0
		),
		'gender' => array(
			'type' => 'selection',
			'default' => 1,
			'options' => array(
				0 => 'core_select-select-gender-female',
				1 => 'core_select-select-gender-male'
			)
		),
		'birth_date' => array(
			'type' => 'date',
		),
		'photo' => array(
			'type' => 'text',
		),
		'url' => array(
			'type' => 'text',
		)
	);

	function __construct() {
		parent::__construct();

		$this->app = 'card';
		$this->table = 'card_extra';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}


}