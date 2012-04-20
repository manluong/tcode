<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calendar extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('CalendarM');
	}


	public function index()	{
		$html_data = array();
		$html_data['calendars'] = $this->CalendarM->get_user_calendars($this->UserM->get_card_id());
		$html_data['cuid'] = $this->UserM->get_card_id();

		$data = array();
		$data['html'] = $this->load->view('/'.get_template().'/calendar/view', $html_data, TRUE);
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;

		$data['div']['title'] = 'Calendar';
		$data['div']['element_name'] = 'wincalendar';
		$data['div']['element_id'] = 'divcalendar';

		$this->data[] = $data;

		$this->LayoutM->load_format();

		$this->output();
	}


	function ajax_get_events() {
		$start = $this->input->get('start');
		$end = $this->input->get('end');
		$calendar_id = $this->input->get('calendar_id');

		if ($calendar_id === FALSE) $calendar_id = array();

		$events = $this->CalendarM->get_events($start, $end, '', $calendar_id);

		foreach($events AS $k=>$v) {
			$events[$k]['start'] = parse_stamp_user($v['date_start'], 'ISO_8601');
			$events[$k]['end'] = parse_stamp_user($v['date_end'], 'ISO_8601');
			$events[$k]['allDay'] = ($v['all_day'] == 1);
		}

		echo json_encode($events);
	}

	function ajax_update_event_dragdrop() {
		$event_id = $this->input->post('event_id');
		$delta_day = $this->input->post('delta_day');
		$delta_min = $this->input->post('delta_min');
		$all_day = $this->input->post('all_day');

		$event = $this->CalendarM->get_event($event_id);
		$adj = ($delta_day * 24 * 60 * 60) + ($delta_min * 60);

		$event['date_start'] = parse_timestamp((strtotime($event['date_start']) + $adj), 'MYSQL');
		$event['date_end'] = parse_timestamp((strtotime($event['date_end']) + $adj), 'MYSQL');
		$event['all_day'] = ($all_day == 'true') ? 1 : 0;

		$this->CalendarM->update_event($event);

		$response = array(
			'success' => TRUE,
			'data' => '',
		);

		echo json_encode($response);
	}

	function ajax_update_event() {
		$event = array(
			'id' => $this->input->post('event_id'),
			'title' => $this->input->post('event_title'),
			'date_start' => parse_user_date($this->input->post('event_date_start')),
			'date_end' => parse_user_date($this->input->post('event_date_end')),
			'calendar_id' => $this->input->post('calendar_id'),
			'memo' => $this->input->post('event_memo'),
			'all_day' => $this->input->post('event_allday'),
		);

		$result = $this->CalendarM->update_event($event);

		$response = array(
			'success' => $result,
			'data' => '',
		);

		echo json_encode($response);
	}


	function ajax_save_event() {
		$event = array(
			'title' => $this->input->post('event_title'),
			'date_start' => parse_user_date($this->input->post('event_date_start')),
			'date_end' => parse_user_date($this->input->post('event_date_end')),
			'calendar_id' => $this->input->post('calendar_id'),
			'memo' => $this->input->post('event_memo'),
			'all_day' => $this->input->post('event_allday'),
		);

		$result = $this->CalendarM->save_event($event);

		$response = array(
			'success' => $result,
			'data' => '',
		);

		echo json_encode($response);
	}
}
