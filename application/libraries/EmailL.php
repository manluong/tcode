<?php defined('BASEPATH') OR exit('No direct script access allowed');

class EmailL {
	private $_ci; // private CI instance

	private $_to = array();
	private $_toname = array();
	private $_type_id = array();
	private $_bcc = array();
	private $_replace_value = array();
	private $_files = array();
	private $_type = '';
	private $_from = '';
	private $_fromname = '';
	private $_template = '';
	private $_subject = '';
	private $_content = '';
	private $_attachment_id = array();
	private $_log_id = '';

	private $_email_storage_dir = 'Email_Content';
	private $_email_attachments_dir = 'Email_Attachments';
	private $temp_dir = '';
	private $_api_user = '';
	private $_api_key = '';

	private $_query_str = '';
	private $_query_post = array();

	function __construct($url = '') {
		$this->_ci = & get_instance();

		$this->_api_user = $this->_ci->eightforce_config['sendgrid_api_user'];
		$this->_api_key = $this->_ci->eightforce_config['sendgrid_api_key'];

		$this->_ci->load->model('EmailM');
		$this->_ci->load->library(array('SmtpApiHeaderL', 'FileL'));

		$this->temp_dir = $this->_ci->eightforce_config['temp_folder'].$this->_ci->domain.'/';
		if ( ! file_exists($this->temp_dir)) mkdir($this->temp_dir, 0777, true);
	}

	/*
	 * Takes the following
	 * @param type				string client or card
	 * @param type_id			array client_id or card_id
	 * @param to				array append address to client email if given
	 * @param toname			array
	 * @param template			template name
	 * @param replace_value		array('keys'=>'key_to_be_replaced', 'values' => array('values_to_replace'))
	 * @param attachment_id		array('docs_id'=>'ver_id')
	 * @param subject			Subject
	 * @param content			To overwrite template
	 * @param bcc				To overwrite email_generalsetting
	 * @param from				To overwrite email_addresssetting and email_generalsetting
	 * @param fromname			To overwrite email_addresssetting and email_generalsetting
	 */

	function set_to($email, $name) {
		$this->_to[] = $email;
		$this->_toname[] = $name;

		return $this;
	}

	function set_from($email, $name) {
		$this->_from = $email;
		$this->_fromname = $name;

		return $this;
	}

	function set_type($type) {
		$this->_type = $type;

		return $this;
	}

	function set_type_id($type_id) {
		$this->_type_id[] = $type_id;

		return $this;
	}

	function set_subject($subject) {
		$this->_subject = $subject;

		return $this;
	}

	function set_bcc($bcc) {
		if (strlen($bcc) == 0) return $this;

		$this->_bcc[] = $bcc;

		return $this;
	}

	function set_template($template) {
		$this->_template = $template;

		return $this;
	}

	function set_content($content) {
		$this->_content = $content;

		return $this;
	}

	/**
	 * Example
	 * 1. Version
	 * array(docs_id=>ver_id)
	 *
	 * 2. Docs (non versioning)
	 * array(docs_id=>'')
	 */
	function set_attachment_id($attachment_id) {
		$this->_attachment_id[] = $attachment_id;

		return $this;
	}

	/*
	 * Example
	 * One user
	 * array('keys' => array('%name%', '%result%'),
	 *		'values' => array(array('Roy'), array('Success')))
	 * Multi user
	 * array('keys' => array('%name%', '%result%'),
	 *		'values' => array(array('Roy1', 'Roy2'), array('Success1', 'Success2')))
	*/
	function set_replace_value($replace_value) {
		if ( ! isset($replace_value['keys']) OR ! isset($replace_value['values'])) {
			log_message('error', 'Replace value must have a key with values to replace');
			exit();
		}

		$this->_replace_value = $replace_value;

		return $this;
	}

	private function _get_to() {
		if ( $this->_type === '' || empty($this->_type_id)) return NULL;

		$to_arr = $this->_ci->EmailM->get_emailaddress_from_type($this->_type, $this->_type_id);
		$this->set_to($to_arr['to'], $to_arr['toname']);
	}

	private function _get_bcc() {
		$bcc_arr = explode(',', $this->_ci->EmailM->check_bcc());
		foreach($bcc_arr AS $bcc) {
			$this->set_bcc($bcc);
		}
	}

	private function _get_content() {
		if ($this->_content === '' && $this->_template === '') {
			log_message('error', 'Content or template must be specified.');
			exit();
		}

		if ($this->_content === '') {
			$this->_content = $this->_ci->EmailM->get_template_content($this->_template);
		}
	}

	private function _get_replace_value() {
		if (count($this->_replace_value) == 0) return NULL;

		for($i=0; $i<count($this->_replace_value['keys']); $i++) {
			$this->_ci->smtpapiheaderl->addSubVal($this->_replace_value['keys'][$i], $this->_replace_value['values'][$i]);
		}
	}

	/**
	 * File size must be less than 7mb - http://docs.sendgrid.com/documentation/api/web-api/mail/
	 * Get file from s3, save to tmp folder for attaching
	 */
	private function _get_attachments() {
		$this->_ci->load->model('DocsM');

		foreach($this->_attachment_id AS $id_or_hash) {
			$docs_detail = $this->_ci->DocsM->get_detail($id_or_hash);

			if ($docs_detail === FALSE) return '';

			// Check file size
			if ($docs_detail['file_size'] > (7168*1024)) {
				log_message('error', 'File size is > then 7168 ID: '.$docs_detail['id'].' Hash: '.$docs_detail['hash']);
				return '';
			}

			//read the current file
			$file = $this->_ci->filel->read($id_or_hash);

			//store a copy of the current file into the email attachments directory
			$file_hash_id = $this->_ci->filel->save_raw($file['contents'], $docs_detail['file_name'].$docs_detail['file_ext'], $this->_email_attachments_dir);

			// Start populating the $_files[] variable
			$this->_files[] = array('name' => $docs_detail['file_name'].$docs_detail['file_ext'],
				'path' => $this->temp_dir.$file_hash_id['hash'],
			);
		}
	}

	private function _log_email() {
		$content_file_id_hash = $this->_ci->filel->save_raw($this->_content, '', $this->_email_storage_dir);

		$data = array(
			'app_id' => $this->_ci->url['app_id'],
			'subject' => $this->_subject,
			'content_file_hash' => $content_file_id_hash['hash'],
			'from' => $this->_from,
			'fromname' => $this->_fromname,
			'replyto' => '',
			'date' => get_current_stamp(),
			'file' => '',
			'headers' => '',
			'x-smtpapi' => '',
			'respond' => '',
			'result' => '',
			'query' => '',
		);
		$this->_log_id = $this->_ci->EmailM->create_log($data);

		foreach($this->_to AS $x=>$to) {
			$data = array(
				'log_email_id' => $this->_log_id,
				'to' => $to,
				'toname' => $this->_toname[$x],
				'is_bcc' => 0,
			);

			$this->_ci->EmailM->insert_new_email($data);
		}

		foreach($this->_bcc AS $to) {
			$data = array(
				'log_email_id' => $this->_log_id,
				'to' => $to,
				'toname' => '',
				'is_bcc' => 1,
			);

			$this->_ci->EmailM->insert_new_email($data);
		}
	}

	private function _update_log($respond) {
		$data = array(
			'query' => 'GET: '.$this->_query_str.' - POST: '.json_encode($this->_query_post),
			'x-smtpapi' => $this->_ci->smtpapiheaderl->asJSON(),
			'respond' => $respond,
		);

		$this->_ci->EmailM->update_log($this->_log_id, $data);
	}

	private function _build_email() {
		$this->_ci->smtpapiheaderl->addFilterSetting('domainkeys', 'enable', 1);
		$this->_ci->smtpapiheaderl->addFilterSetting('domainkeys', 'domain', 'www.8force.com');
		$this->_ci->smtpapiheaderl->setUniqueArgs(array('log_email_id'=>$this->_log_id));

		// Start converting arrays to strings
		$to = array();
		foreach($this->_to AS $t) {
			$to[] = rawurlencode($t);
		}
		$this->_query_str = '?to[]=' . implode('&to[]=', $to);

		$to_name = array();
		foreach($this->_toname AS $t) {
			$to_name[] = rawurlencode($t);
		}
		$this->_query_str .= '&toname[]=' . implode('&toname[]=', $to_name);

		if (count($this->_bcc) > 0) {
			$bcc = array();
			foreach($this->_bcc AS $t) {
				$bcc[] = rawurlencode($t);
			}
			$this->_query_str .= '&bcc[]=' . implode('&bcc[]=', $bcc);
		}

		$this->_query_str .= '&from='.rawurlencode($this->_from);
		$this->_query_str .= '&fromname='.rawurlencode($this->_fromname);

		$data = array(
			'api_user' => $this->_api_user,
			'api_key' => $this->_api_key,
			'x-smtpapi' => $this->_ci->smtpapiheaderl->asJSON(),
			'subject' => $this->_subject,
			'html' => $this->_content,
		);
		foreach($this->_files as $file) {
			$data['files['.$file['name'].']'] = '@'.$file['path'];
		}
		$this->_query_post = $data;
	}

	function send_email () {
		$api_url = 'http://sendgrid.com/api/mail.send.json';

		$this->_get_to();
		$this->_get_bcc();
		$this->_get_content();
		$this->_get_replace_value();
		$this->_get_attachments();

		$this->_log_email();
		$this->_build_email();

		$session = curl_init($api_url.$this->_query_str);
		curl_setopt($session, CURLOPT_POST, true);
		curl_setopt($session, CURLOPT_POSTFIELDS, $this->_query_post);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$i = curl_exec($session);
		curl_close($session);
		$i = json_decode($i, true);

		$this->_update_log($i['message']);
		$this->log_send_response($i);

		return ($i['message'] === 'success');
	}

	function log_send_response($response) {
		$str = date('F j, Y, g:i a') .": ";
		$str .= 'Sendgrid response: '.print_r($response, true)."\n";
		$fp = fopen($this->temp_dir.'sendgrid_send_response.log','a+');
		fwrite($fp, $str);
		fclose($fp);
	}

	// Log incoming sendgrid Events
	function log_sendgrid($type) {
		$allowed_type = array('events', 'email');
		if ( ! in_array($type, $allowed_type)) {
			log_message('debug', 'Log type not allowed'); return '';
		}
		$str = date('F j, Y, g:i a')."\n";
		$str .= "====================\n";
		$str .= '$_POST = '.print_r($_POST, true);
		$str .= "====================\n\n";
		$str .= '$_FILES = '.print_r($_FILES, true);
		$str .= "====================\n\n";
		$str .= 'PHP input = '.file_get_contents('php://input');
		$str .= "====================\n\n";
		$str .= '$_SERVER = '.print_r($_SERVER, true);
		$str .= "====================\n\n";
		$fp = fopen($this->temp_dir.'sendgrid_'.$type.'.log','a+');
		fwrite($fp, $str);
		fclose($fp);
	}

}