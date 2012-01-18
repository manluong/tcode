<?php

class StatusM extends MY_Model {

	function __construct() {
		$this->table = 'status';
		$this->id_field = 'id';

		parent::__construct();
	}


	function get_user_current($card_id) {
		$rs = $this->db->select()
				->from('status')
				->where('card_id', $card_id)
				->limit(1)
				->get();

		return $rs->row_array();
	}

	function save($status) {
		$this->db->insert('status_history', $status);
		$new_id = $this->db->insert_id();

		$status['status_history_id'] = $new_id;
		$this->update_current_status($status);
	}

	function delete($status_id) {
		$rs = $this->db->select()
				->from('status_history')
				->where('id', $status_id)
				->limit(1)
				->get();

		$status = $rs->row_array();
		$card_id = $status['card_id'];

		$this->db->where('id', $status_id)
			->limit(1)
			->delete('status_history');

		$current_status = $this->get_status($card_id);
		if ($current_status['status_history_id'] == $status_id) {
			$rs = $this->select()
					->from('status_history')
					->where('card_id', $card_id)
					->order_by('id', 'DESC')
					->limit(1)
					->get();

			$new_status = $rs->row_array();

			$new_status['status_history_id'] = $new_status['id'];
			unset($new_status['id']);

			$this->update_current_status($new_status);
		}
	}

	private function update_current_status($status) {
		$sql = $this->db->insert_string('status', $status);

		unset($status['card_id']);
		$temp = array();
		foreach($status AS $key=>$value) {
			$temp[] = $key.'=VALUES('.$key.')';
		}

		$sql .= ' ON DUPLICATE KEY UPDATE '.implode(',', $temp);

		$this->db->query($sql);
	}

	function save_status_type($status) {
		$this->table = 'status_types';
		$this->save($status, 'id');
		$this->table = 'status';
	}

}
