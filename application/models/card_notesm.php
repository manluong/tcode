<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Card_NotesM extends MY_Model {

	public $data_fields = array(
		'card_id' => array(
			'type' => 'id'
		),
		'note' => array(
			'type' => 'text',
		),
	);

	function __construct() {
		parent::__construct();

		$this->app = 'card';
		$this->table = 'card_notes';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}


}