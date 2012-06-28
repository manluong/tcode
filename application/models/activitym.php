<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class ActivityM extends MY_Model {

	function __construct() {
		$this->table = 'activities';

		parent::__construct();

		$this->load->model('CardM');
	}

	function insert_wall_post($text) {
		$data = array(
			'log_type_id' => $this->_log_data['log_type']['id'],
			'app_id' => $this->url['app_id'],
			'app_data_id' => $this->url['id_plain'],
			'saveid' => $this->_log_data['saveid'],
			'card_id' => $this->UserM->get_card_id(),
			'sub_group' => '',
			'type' => 'text',
			'applink' => '',
			'msg' => $text,
			'html' => '',
			'gpmid' => $this->UserM->info['role']['role_id'],
			'timeline' => $this->_log_data['log_type']['timeline'],
			'stamp' => get_current_stamp(),
			'lastupdate' => get_current_stamp(),
			'priority' => 0,
		);

		$this->db->insert('log_event', $data);
		$data['id'] = $this->db->insert_id();
		$data['card_name'] = $this->CardM->get_name($data['card_id']);
		$data['created_stamp_iso8601'] = parse_user_date($data['stamp'], 'ISO_8601');
		$data['created_stamp_iso'] = parse_user_date($data['stamp'], 'ISO');

		return $data;
	}

	public function get_wall($id=0, $limit=10, $card_id=0, $sticky=0) {
		$this->load->model('CommentsM');
		$this->CommentsM->results_per_page = 2;
		$this->CommentsM->older_comments_top = TRUE;
		$this->CommentsM->threaded = FALSE;

		$prefs = $this->UserM->get_follow_preferences();

		$result = array();

		$bookmark_id = $id;

		while(count($result) < $limit) {
			$this->db->select()
				->from($this->table)
				->order_by('modified_stamp', 'desc')
				->limit($limit+10);

			if ($sticky != 0) $this->db->where('sticky', 1);
			if ($bookmark_id != 0) $this->db->where('id <', $bookmark_id);
			if ($card_id != 0) $this->db->where('created_card_id', $card_id);

			$rs = $this->db->get();

			if ($rs->num_rows() == 0) return $result;

			foreach ($rs->result_array() as $v) {
				$display = ($v['display'] == 1);
				$bookmark_id = $v['id'];

				//check prefs
				if (isset($prefs[$v['type']][$v['app_data_id']])) {
					$display = ($prefs[$v['type']][$v['app_data_id']] == 1);
				} elseif (isset($prefs[$v['type']][0])) {
					$display = ($prefs[$v['type']][0] == 1);
				}

				if (!$display) continue;

				$temp = $v;
				if ($v['type'] != 'text') {
					$temp['content'] = $this->render_event($v);
				}

				$temp['card_name'] = $this->CardM->get_name($v['created_card_id']);
				$temp['created_stamp_iso8601'] = parse_user_date($v['created_stamp'], 'ISO_8601');
				$temp['created_stamp_iso'] = parse_user_date($v['created_stamp'], 'ISO');

				$temp['comments'] = $this->CommentsM->get_page(1, $v['id'], 1, 0);
				$comment_count = $this->CommentsM->get_comment_count(1, $v['id']);
				$temp['comments_more'] = ($comment_count > 2);

				$result[] = $temp;

				if (count($result) == $limit) return $result;
			}
		}

		return $result;
	}

	private function render_event($activity) {
		//get the event language line. Return empty string if not found.
		$action_line = $this->lang->line('events-'.$activity['type']);
		if ($action_line === FALSE) return '';

		//get various details of this action
		$app_language_name = $this->AppM->get_language_name($activity['app_id']);
		$app_model = $this->AppM->get_model($activity['app_id']);
		$this->load->model($app_model);
		$app_data_name = $this->$app_model->get_data_name($activity['app_data_id']);

		$action_person = $this->CardM->get_name($activity['created_card_id']);

		//update the event line with the details and return the result
		$search = array('#app_name#', '#data_name#', '#action_person#');
		$replace = array($app_language_name, $app_data_name, $action_person);

		return str_replace($search, $replace, $action_line);
	}
}
