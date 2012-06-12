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

		$this->app = 'invoice';
		$this->table = 'a_invoice_terms';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}

	function get_content($id) {
		$term = $this->get($id);
		if ($term === FALSE) return FALSE;

		return $term['content'];
	}
}