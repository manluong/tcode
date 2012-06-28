<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Email_TemplateM extends MY_Model {

	public $data_fields = array(
		'app_id' => array(
			'type' => 'id',
			'required' => true,
			'allow_blank' => false
		),
		'sort_order' => array(
			'type' => 'numeric'
		),
		'name' => array(
			'type' => 'text'
		),
		'subject' => array(
			'type' => 'text'
		),
		'content' => array(
			'type' => 'text',
			'required' => true,
			'allow_blank' => false
		),
		'notes' => array(
			'type' => 'text'
		)
	);

	function __construct() {
		parent::__construct();

		$this->app = 'email';
		$this->table = 'email_template';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}
}