<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Card_EmailM extends MY_Model {

	public $data_fields = array(
		'card_id' => array(
			'type' => 'id'
		),
		'email' => array(
			'type' => 'email'
		),
		'type' => array(
			'type' => 'selection',
			'default' => 0,
			'options' => array(
				0 => 'core_select-select-card_emailtype-work',
				1 => 'core_select-select-card_emailtype-home',
			)
		),
		'is_default' => array(
			'type' => 'numeric',
			'default' => 0
		),
	);

	function __construct() {
		parent::__construct();

		$this->app = 'card';
		$this->table = 'card_email';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}


}