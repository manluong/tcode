<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Invoice_TermsM extends MY_Model {

	public $data_fields = array(
		'name' => array(
			'type' => 'text'
		),
		'content' => array(
			'type' => 'text'
		)
	);

	function __construct() {
		parent::__construct();

		$this->table = 'a_invoice_terms';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}
}
