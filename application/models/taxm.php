<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class TaxM extends MY_Model {

	public $data_fields = array(
		'name' => array(
			'type' => 'text'
		),
		'percent' => array(
			'type' => 'numeric'
		)
	);

	function __construct() {
		parent::__construct();

		$this->table = 'tax';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}
}
