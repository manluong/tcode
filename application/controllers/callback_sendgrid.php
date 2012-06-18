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

		//Split response into json_result
		$json_result = array();
		preg_match_all('/\{.*\}/', file_get_contents('php://input'), $json_result);

		foreach($json_result[0] AS $json) {
			$sendgrid_response = json_decode($json, TRUE);
			$this->EmailM->update_status(
					$sendgrid_response['log_email_id'],
					$sendgrid_response['email'],
					$sendgrid_response['event'],
					$sendgrid_response['timestamp']
				);
		}

		$this->output->set_header('HTTP/1.1 200');
	}

	function incoming_emails() {
		if ( ! $this->input->post('to')) return '';

		$i = explode('@', $this->input->post('to'));
		$app_name = $i[0];

		$i = explode('.', $i[1]);
		$domain = $i[0];

		$this->_setup_db($domain);

		$app_id = $this->AppM->get_id($app_name);

		//$this->emaill->log_sendgrid('email');

		// Uploads attachment
		$attachments_count = (int)$this->input->post('attachments');
		$attachments = array();
		if ($attachments_count > 0) {
			$this->load->library('filel');

			$files = json_decode($this->input->post('attachment-info'), true);
			foreach($files AS $k => $v) {
				$temp = $this->filel->save_raw(file_get_contents($_FILES[$k]['tmp_name']), $v['filename'], 'Email_Attachments');
				$attachments[] = $temp['hash'];
			}
		}

		$data = array(
			'app_id' => $app_id,
			'status' => 0,

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
			'attachments_hash' => json_encode($attachments),
		);

		$this->EmailM->save_received_email($data);
	}

}