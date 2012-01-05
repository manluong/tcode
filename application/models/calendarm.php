<?php

class CalendarM extends MY_Model {

	function __construct() {
		$this->table = 'a_calendars';
		$this->id_field = 'id';

		parent::__construct();
	}

	//type = '', owner, sharer
	function get_user_calendars($card_id, $type='') {
		$this->db->select()
			->from('a_calendars')
			->join('a_calendars_cards', 'a_calendars_cards.calendar_id=a_calendars.id')
			->where('a_calendars_cards.card_id', $card_id);

		if ($type!='') $this->db->where('type', $type);

		$rs = $this->db->order_by('calendar_order')
				->get();

		return $rs->result_array();
	}

	function get_user_calendar_ids($card_id) {
		$calendars = $this->get_user_calendars($card_id);

		$results = array();
		foreach($calendars AS $c) {
			$results[] = $c['id'];
		}
		return $results;
	}

	function get_event($event_id) {
		$rs = $this->db->select()
				->from('a_calendars_objects')
				->where('id', $event_id)
				->limit(1)
				->get();

		if ($rs->num_rows() == 0) return FALSE;

		return $rs->row_array();
	}

	function get_events($start='', $end='', $card_id='', $calendar_ids=array()) {
		if ($card_id == '') $card_id = $this->UserM->get_cardid();

		if (count($calendar_ids) == 0) $calendar_ids = $this->get_user_calendar_ids($card_id);

		$this->db->select()
			->from('a_calendars_objects')
			->where_in('calendar_id', $calendar_ids);

		if ($start != '' && $end != '') {
			$start = parse_timestamp($start, 'MYSQL');
			$end = parse_timestamp($end, 'MYSQL');
			$this->db->where('date_start BETWEEN "'.$start.'" AND "'.$end.'"');
		}

		$rs = $this->db->get();

		if ($rs->num_rows() == 0) return array();

		return $rs->result_array();
	}

	function update_event($event) {
		$event['modified_cardid'] = $this->UserM->get_cardid();
		$event['modified_stamp'] = get_current_stamp();

		$id = $event['id'];
		unset($event['id']);

		$this->db->where('id', $id)
				->update('a_calendars_objects', $event);
	}


	function save_event($event) {
		$event['created_cardid'] = $this->UserM->get_cardid();
		$event['created_stamp'] = get_current_stamp();

		$this->db->insert('a_calendars_objects', $event);
	}


}
?>
