<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Callback_sendgrid extends MY_Controller {

	function __construct() {
		$this->allow_unauthed_access = TRUE;
		$this->is_callback = TRUE;

		parent::__construct();
		$this->load->library('EmailL');
		$this->load->model('EmailM');
	}

	//Sendgrid calls this to update us on the result of emails sent
	function event_parser() {
		if ($_SERVER['HTTP_USER_AGENT'] !== 'SendGrid Event API') return NULL;

		//$this->emaill->log_sendgrid('events');

		// Split response into json_result
		$json_result = array();
		preg_match_all('/\{.*\}/', file_get_contents('php://input'), $json_result);

		foreach($json_result AS $json) {
			$sendgrid_response = json_decode($json);

			$log_email_id = $sendgrid_response['log_email_id'];
			$this->EmailM->update_status($log_email_id, $sendgrid_response['email'], $sendgrid_response['event'], $sendgrid_response['timestamp']);
		}

		$this->output->set_header('HTTP/1.1 200');
	}

	function email_parser() {
		$this->emaill->log_sendgrid('email');
		if ( ! $this->input->post('to')) return '';
		// Log to
		$i = explode('@', $this->input->post('to'));
		$app = $i[0];
		$data = array(
			'headers' => $this->input->post('headers'),
			'text' => $this->input->post('text'),
			'html' => $this->input->post('html'),
			'from' => $this->input->post('from'),
			'to' => $this->input->post('to'),
			'cc' => $this->input->post('cc') ? $this->input->post('cc') : '',
			'subject' => $this->input->post('subject'),
			'dkim' => $this->input->post('dkim'),
			'SPF' => $this->input->post('SPF'),
			'envelope' => $this->input->post('envelope'),
			'charsets' => $this->input->post('charsets'),
			'spam_score' => $this->input->post('spam_score'),
			'spam_report' => $this->input->post('spam_report'),
			'attachments' => $this->input->post('attachments') ? $this->input->post('attachments') : 0,
			'attachment-info' => $this->input->post('attachment-info') ? $this->input->post('attachment-info') : 0,
			'status' => 1,
			'app_id' => 1,
		);
		$insert_id = $this->EmailM->save_received_email($data);
		log_message('debug', 'Received email saved id:'.$insert_id.' app: '.$app);

		// Uploads attachment to s3: bucket/email/content/attachments
		if ($this->input->post('attachments')) {
			$attach = $this->input->post('attachments');
			settype($attach, 'int'); // explicitly set it to int
			if ($attach !== 0) {
				$files = json_decode($this->input->post('attachment-info'), true);
				$this->emaill->upload_attachment_s3($_FILES, $files);
			}
		}
		// Redirect to respective app
		// fixed method appname/receive_email
		//redirect($app.'/receive_email?id='.$insert_id);
	}

}