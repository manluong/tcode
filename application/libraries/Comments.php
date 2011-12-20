<?php

class Comments {
	var $CI = '';

	var $app = '';
	var $id = '';

	var $CommentsM = '';

	function __construct() {
		$this->CI =& get_instance();

		$this->app = $this->CI->url['app'];
		$this->id = $this->CI->url['id_plain'];

		$this->CI->load->model('CommentsM');
		$this->CommentsM =& $this->CI->CommentsM;
	}

	function get() {

	}

	function save() {

	}


}
?>
