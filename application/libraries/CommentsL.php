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

		return $this->CI->load->view(get_template().'/comments/view', $data, TRUE);
	}

	function get_list_html($limit=5) {
		$data = array();
		$data['comments'] = $this->CommentsM->get_list($this->app_id, $this->app_data_id, $limit);
		$data['app_id'] = $this->app_id;
		$data['app_data_id'] = $this->app_data_id;

		return $this->CI->load->view(get_template().'/comments/view', $data, TRUE);
	}


	//this should be the default function to call
	function get_page_html($page=1) {
		$data = array();
		$data['show_replies'] = 5;
		$data['comments'] = $this->CommentsM->get_page($this->app_id, $this->app_data_id, $page);
		$data['app_id'] = $this->app_id;
		$data['app_data_id'] = $this->app_data_id;

		return $this->CI->load->view(get_template().'/comments/view', $data, TRUE);
	}

	function get_lite_html($page=1) {
		$data = array();
		$data['show_replies'] = 1;
		$this->CommentsM->results_per_page = 2;
		$this->CommentsM->older_comments_top = TRUE;
		$data['comments'] = $this->CommentsM->get_page($this->app_id, $this->app_data_id, $page, $data['show_replies']);
		$data['app_id'] = $this->app_id;
		$data['app_data_id'] = $this->app_data_id;

		return $this->CI->load->view(get_template().'/comments/view_lite', $data, TRUE);
	}

}
?>
