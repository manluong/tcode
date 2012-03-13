<?php

class CommentsL {
	var $CI = '';

	var $app_id = '';
	var $app_data_id = '';

	var $CommentsM = '';

	function __construct() {
		$this->CI =& get_instance();

		$this->app_id = $this->CI->url['app_id'];
		$this->app_data_id = $this->CI->url['id_plain'];

		$this->CI->load->model('CommentsM');
		$this->CommentsM =& $this->CI->CommentsM;

		$this->CI->load->helper('form');
	}




	function get($id) {
		return $this->CommentsM->get($id);
	}

	function get_list($limit=5) {
		return $this->CommentsM->get_list($this->app_id, $this->app_data_id, $limit);
	}

	function get_page($page=1) {
		return $this->CommentsM->get_list($this->app_id, $this->app_data_id, $page);
	}




	function get_html($id) {
		$data = array();
		$data['comments'][] = $this->CommentsM->get($id);
		$data['app_id'] = $this->app_id;
		$data['app_data_id'] = $this->app_data_id;

		return $this->to_html($data);
	}

	function get_list_html($limit=5) {
		$data = array();
		$data['comments'] = $this->CommentsM->get_list($this->app_id, $this->app_data_id, $limit);
		$data['app_id'] = $this->app_id;
		$data['app_data_id'] = $this->app_data_id;

		return $this->to_html($data);
	}


	//this should be the default function to call
	function get_page_html($page=1) {
		$data = array();
		$data['comments'] = $this->CommentsM->get_page($this->app_id, $this->app_data_id, $page);
		$data['app_id'] = $this->app_id;
		$data['app_data_id'] = $this->app_data_id;

		return $this->to_html($data);
	}


	

	private function to_html($data) {
		foreach($data['comments'] AS $k=>$v) {
			$data['comments'][$k]['created_stamp_iso8601'] = parse_stamp_user($v['created_stamp'], 'ISO_8601');
			$data['comments'][$k]['created_stamp_iso'] = parse_stamp_user($v['created_stamp'], 'ISO_DATE');

			foreach($data['comments'][$k]['replies'] AS $rk=>$rv) {
				$data['comments'][$k]['replies'][$rk]['created_stamp_iso8601'] = parse_stamp_user($rv['created_stamp'], 'ISO_8601');
				$data['comments'][$k]['replies'][$rk]['created_stamp_iso'] = parse_stamp_user($rv['created_stamp'], 'ISO_DATE');
			}
		}

		return $this->CI->load->view(get_template().'/comments/view', $data, TRUE);
	}

}
?>
