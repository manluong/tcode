<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ical extends MY_Controller {

	function __construct() {
		$this->allow_unauthed_access = TRUE;
		
		parent::__construct();

		$this->load->model('CalendarM');
	}


	public function index($id, $user_id)	{
		$id = decode_id($id);
		$user_id = decode_id($user_id);

		$this->UserM->load_info($user_id);
		$events = $this->CalendarM->get_events('','','-',array($id));
		$cal = $this->CalendarM->get($id);

		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: inline; filename=calendar.ics');
		echo $this->to_ical($events, $cal);
	}


	private function to_ical($events, $cal) {
		$nl = "\n";

		$output = 'BEGIN:VCALENDAR'.$nl;
		$output .= 'X-WR-CALNAME:'.$cal['display_name'].$nl;

		foreach($events AS $e) {
			$output .= 'BEGIN:VEVENT'.$nl;
			$output .= 'DTSTART:'.parse_stamp_user($e['date_start'], 'ICAL_DATETIME').$nl;
			$output .= 'DTEND:'.parse_stamp_user($e['date_end'], 'ICAL_DATETIME').$nl;
			$output .= 'SUMMARY:'.$e['title'].$nl;
			$output .= 'DESCRIPTION:'.$e['memo'].$nl;
			$output .= 'END:VEVENT'.$nl;
		}

		$output .= 'END:VCALENDAR'.$nl;

		return $output;
	}
}
