<?php

class RespM extends CI_Model {

	public $success = TRUE;
	public $message = '';
	public $template = 'widget';
	public $title = '';
	public $type = '';
	public $details = array();

	function __construct() {
		parent::__construct();
	}

	function set_success($success = TRUE) {
		$this->success = $success;
		return $this;
	}

	function set_message($message = '') {
		$this->message = $message;
		return $this;
	}

	function set_template($template = 'widget') {
		$this->template = $template;
		return $this;
	}

	function set_title($title = '') {
		$this->title = $title;
		return $this;
	}

	function set_type($type = '') {
		$this->type = $type;
		return $this;
	}

	function set_details($details = array()) {
		$this->details = $details;
		return $this;
	}

	function output() {
		$result = array(
			'success' => $this->success,
			'message' => $this->message,
			'template' => $this->template,
			'title' => $this->title,
			'type' => $this->type,
			'details' => $this->details
		);

		return $result;
	}

	function output_json() {
		$this->output->set_content_type('application/json');
		echo json_encode($this->output());
		exit;
	}

	function return_json() {
		return json_encode($this->output());
	}

}
?>
