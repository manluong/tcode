<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Comment_FileM extends MY_Model {

	public $data_fields = array(
		'id_comment' => array(
			'type' => 'id'
		),
		'role_id' => array(
			'type' => 'text'
		),
	);

	function __construct() {
		parent::__construct();

		$this->app = 'card';
		$this->table = 'a_comment_file';
		$this->cache_enabled = TRUE;
		$this->sett_has_system_fields = FALSE;
	}
}