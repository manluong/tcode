<?php defined('BASEPATH') OR exit('No direct script access allowed');

class EmailL {
	private $_ci; // private CI instance

	private $_cards = array();
	private $_to = array();
	private $_toname = array();
	private $_from = 'hello@8force.net';
	private $_fromname = '8Force';
	private $_bcc = array();

	private $_replace_value = array();
	private $_template = '';

	private $_subject = '';
	private $_content = '';
	private $_attachment_id = array();
	private $_files = array();

	private $_email_storage_dir = 'Email_Content';
	private $_email_attachments_dir = 'Email_Attachments';
	private $_temp_dir = '';
	private $_api_user = '';
	private $_api_key = '';

	private $_log_id = '';
	private $_query_str = '';
	private $_query_post = array();

	function __construct($url = '') {
		$this->_ci = & get_instance();

		$this->_api_user = $this->_ci->eightforce_config['sendgrid_api_user'];
		$this->_api_key = $this->_ci->eightforce_config['sendgrid_api_key'];

		$this->_ci->load->model('EmailM');
		$this->_ci->load->library(array('SmtpApiHeaderL', 'FileL'));

		$this->_temp_dir = $this->_ci->eightforce_config['temp_folder'].$this->_ci->domain.'/';
		if ( ! file_exists($this->_temp_dir)) mkdir($this->_temp_dir, 0777, true);
	}

	/*
	 * Takes the following
	 * @param type				string client or card
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
		$this->_to[] = trim($email);
		$this->_toname[] = trim($name);

		return $this;
	}

	function set_from($email, $name) {
		$this->_from = trim($email);
		$this->_fromname = trim($name);

		return $this;
	}

	function set_card($card_id) {
		$this->_cards[] = $card_id;

		return $this;
	}

	function set_subject($subject) {
		$this->_subject = $subject;

		return $this;
	}

	function set_bcc($bcc) {
		if (strlen($bcc) == 0) return $this;

		$this->_bcc[] = trim($bcc);

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
		hash or id of File
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

	private function _load_to() {
		if (count($this->_cards) == 0) return NULL;

		$this->_ci->load->model('CardM');
		$this->_ci->CardM->sett_fill_address = FALSE;
		$this->_ci->CardM->sett_fill_bank = FALSE;
		$this->_ci->CardM->sett_fill_extra = FALSE;
		$this->_ci->CardM->sett_fill_notes = FALSE;
		$this->_ci->CardM->sett_fill_social = FALSE;
		$this->_ci->CardM->sett_fill_tel = FALSE;
		$this->_ci->CardM->sett_fill_access_user_role = FALSE;

		foreach($this->_cards AS $card_id) {
			$card = $this->_ci->CardM->get($card_id);

			$name = $card['first_name'].' '.$card['last_name'];
			$email = '';

			foreach($card['addon_email'] AS $e) {
				if ($e['is_default']) $email = $e['email'];
			}

			$this->set_to($email, $name);
		}
	}

	//Check settings for any email addresses to be always included in the BCC
	private function _load_bcc() {
		$bcc = $this->_ci->SettingM->get_setting('email', 'always_bcc');
		if ($bcc === NULL) return;

		$bcc_arr = explode(',', $bcc);
		foreach($bcc_arr AS $email) {
			$this->set_bcc($email);
		}
	}

	private function _load_content() {
		if ($this->_content === '' && $this->_template === '') {
			log_message('error', 'Content or template must be specified.');
			exit();
		}

		if ($this->_content === '') {
			$this->_content = $this->_ci->EmailM->get_template_content($this->_template);
		}
	}

	private function _load_replace_value() {
		if (count($this->_replace_value) == 0) return NULL;

		for($i=0; $i<count($this->_replace_value['keys']); $i++) {
			$this->_ci->smtpapiheaderl->addSubVal($this->_replace_value['keys'][$i], $this->_replace_value['values'][$i]);
		}
	}

	/**
	 * File size must be less than 7mb - http://docs.sendgrid.com/documentation/api/web-api/mail/
	 */
	private function _load_attachments() {
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
				'path' => $this->_temp_dir.$file_hash_id['hash'],
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
			'query' => '',
		);
		$this->_log_id = $this->_ci->EmailM->create_log($data);

		foreach($this->_to AS $x=>$to) {
			$data = array(
				'email_sent_log_id' => $this->_log_id,
				'to' => $to,
				'toname' => $this->_toname[$x],
				'is_bcc' => 0,
			);

			$this->_ci->EmailM->insert_new_email($data);
		}

		foreach($this->_bcc AS $to) {
			$data = array(
				'email_sent_log_id' => $this->_log_id,
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
		$this->_ci->smtpapiheaderl->setUniqueArgs(array('email_sent_log_id'=>$this->_log_id));

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

		$this->_load_to();
		$this->_load_bcc();
		$this->_load_content();
		$this->_load_replace_value();
		$this->_load_attachments();

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

		return ($i['message'] === 'success');
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
		$fp = fopen($this->_temp_dir.'sendgrid_'.$type.'.log','a+');
		fwrite($fp, $str);
		fclose($fp);
	}

}