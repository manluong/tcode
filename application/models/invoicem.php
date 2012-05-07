<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class InvoiceM extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table = 'a_invoice';
		$this->cache_enabled = TRUE;
	}

	function getCustomer() {
		$this->db->select('id, nickname');
		$query = $this->db->get('card');

		$results = array();
		foreach ($query->result() as $r) {
			$results[$r->id] = $r->nickname;
		}

		return $results;
	}

	function getTax() {
		//$this->db->select('id, nickname');
		//$query = $this->db->get('card');

		$results = array();
		$results[1] = 1;
		$results[2] = 2;
		//foreach ($query->result() as $r) {
		//	$results[$r->id] = $r->nickname;
		//}

		return $results;
	}
}