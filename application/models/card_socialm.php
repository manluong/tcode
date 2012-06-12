<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Card_SocialM extends MY_Model {

	public $data_fields = array(
		'card_id' => array(
			'type' => 'id'
		),
		'type' => array(
			'type' => 'selection',
			'default' => 0,
			'options' => array(
				0 => 'core_select-select-card_social-other',
				1 => 'core_select-select-card_social-facebook',
				2 => 'core_select-select-card_social-linkedin',
				3 => 'core_select-select-card_social-msn',
				4 => 'core_select-select-card_social-yahoo',
				5 => 'core_select-select-card_social-skype',
				6 => 'core_select-select-card_social-qq',
				7 => 'core_select-select-card_social-google',
				8 => 'core_select-select-card_social-website',
			)
		),
		'name_id' => array(
			'type' => 'text',
		),
	);

	function __construct() {
		parent::__construct();

		$this->app = 'card';
		$this->table = 'card_social';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}


}