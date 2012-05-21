<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Card_AddressM extends MY_Model {

	public $data_fields = array(
		'card_id' => array(
			'type' => 'id'
		),
		'organization_id' => array(
			'type' => 'id'
		),
		'card_def' => array(
			'type' => 'numeric'
		),
		'org_def' => array(
			'type' => 'numeric'
		),
		'type' => array(
			'type' => 'numeric'
		),
		'country' => array(
			'type' => 'text'
		),
		'postal' => array(
			'type' => 'text'
		),
		'state' => array(
			'type' => 'text'
		),
		'city' => array(
			'type' => 'text'
		),
		'line_1' => array(
			'type' => 'text'
		),
		'line_2' => array(
			'type' => 'text',
		),
		'geo_lat' => array(
			'type' => 'text',
		),
		'geo_lng' => array(
			'type' => 'text',
		),
	);

	function __construct() {
		parent::__construct();

		$this->table = 'card_address';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}


}