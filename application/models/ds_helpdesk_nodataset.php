<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class DS_Helpdesk_Nodataset extends MY_Model {
	function __construct() {
		parent::__construct();
		
		$this->table = 'a_helpdesk';
		$this->cache_enabled = TRUE;
	}
	
}