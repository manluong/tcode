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

		$this->app_id = 100;
		$this->app_data_id = 10;

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

	function get_html($id) {
		$data = array();
		$data['comments'][] = $this->CommentsM->get($id);
		$data['app_id'] = $this->app_id;
		$data['app_data_id'] = $this->app_data_id;

		$result = $this->CI->load->view(get_template().'/comments/view', $data, TRUE);

		return $result;
	}

	function get_list_html($limit=5) {
		$data = array();
		$data['comments'] = $this->CommentsM->get_list($this->app_id, $this->app_data_id, $limit);
		$data['app_id'] = $this->app_id;
		$data['app_data_id'] = $this->app_data_id;

		$result = $this->CI->load->view(get_template().'/comments/view', $data, TRUE);

		return $result;
	}

}
?>
