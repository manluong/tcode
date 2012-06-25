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

	public function get_wall($id=0, $limit=10, $card_id=0) {
		$this->load->model('CommentsM');
		$this->CommentsM->results_per_page = 2;
		$this->CommentsM->older_comments_top = TRUE;
		$this->CommentsM->threaded = FALSE;

		$this->db->select()
			->from($this->table)
			->order_by('modified_stamp', 'desc')
			->limit($limit);

		if ($id != 0) $this->db->where('id <', $id);
		if ($card_id != 0) $this->db->where('created_card_id', $card_id);

		$rs = $this->db->get();

		if ($rs->num_rows() == 0) return array();

		$results = $rs->result_array();

		$result = array();
		foreach ($results as $k=>$v) {
			if ($v['type'] != 'text') {
				if ($v['content'] !== '') {
					$msg = $this->_get_custom_msg($v['msg']);
				} else {
					$msg = $this->_get_default_msg($v['subaction'], $v['app'], $v['app_data_id']);
					$msg .= $v['created_stamp'];
				}
			}

			$result[$k]['card_name'] = $this->CardM->get_name($v['created_card_id']);
			$result[$k]['created_stamp_iso8601'] = parse_user_date($v['created_stamp'], 'ISO_8601');
			$result[$k]['created_stamp_iso'] = parse_user_date($v['created_stamp'], 'ISO');

			$result[$k]['comments'] = $this->CommentsM->get_page(1, $v['id'], 1, 0);
			$comment_count = $this->CommentsM->get_comment_count(1, $v['id']);
			$result[$k]['comments_more'] = ($comment_count > 2);
		}

		return $result;
	}
}
