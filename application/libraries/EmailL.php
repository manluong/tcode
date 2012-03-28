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
	private $_date = '';
	private $_attachment_id = '';
	private $_query_str = '';
	private $_insert_id = '';

	private $_email_storage_dir = '/email/content/';
	private $_email_attachements_storage = '/email/content/attachments/';
	private $_temp_dir = '';
	private $_api_user = 'tcsteam'; // sendgrid
	private $_api_key = 'express08)*'; // sendgrid
	private $_bucket = '';

	private $_query_array = array();

	function __construct($url = '') {
		$this->_ci = & get_instance();
		$this->_ci->load->model('EmailM');
		$this->_ci->load->model('DocsM');
		$this->_ci->load->spark('curl/1.2.0');
		$this->_ci->load->library(array('SmtpApiHeaderL', 'S3'));

		$domain = explode('.', $_SERVER['SERVER_NAME']);
		$domain = $domain[0];
		$this->_bucket = 't-'.$domain;
		if ( ! $this->_get_bucket($this->_bucket)) {
			//$this->_create_bucket($this->_bucket);
		}
		create_dir($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$domain.'/', 0777);
		$this->_temp_dir = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.$domain.'/';
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
	function set_bucket($bucket) {
		$this->_bucket = $bucket; return this;
	}

	function set_type($type) {
		$this->_type = $type;
		return $this;
	}

	function set_type_id($type_id) {
		$this->_type_id = $type_id;
		return $this;
	}

	function set_subject($subject) {
		$this->_subject = $subject;
		return $this;
	}

	function set_bcc($bcc) {
		$this->_bcc = $bcc;
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
		$this->_attachment_id = $attachment_id;
		return $this;
	}

	function set_to($to) {
		$this->_to = array_merge($to);
		return $this;
	}

	function set_toname($toname) {
		$this->_toname = array_merge($toname);
		return $this;
	}

	function set_from($from) {
		$this->_from = $from;
		return $this;
	}

	function set_fromname($fromname) {
		$this->_fromname = $fromname;
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
			log_message('error', 'Replace value must have a key with values to replace');exit();
		}
		$this->_replace_value = $replace_value;
		return $this;
	}

	private function _get_to() {
		if ( $this->_type !== '' && ! empty($this->_type_id)) {
			$to_arr = $this->_ci->EmailM->get_emailaddress_from_type($this->_type, $this->_type_id);
			$this->_to = array_merge($this->_to, $to_arr['to']);
			$this->_toname = array_merge($this->_toname, $to_arr['toname']);
		}
	}

	private function _get_bcc() {
		$bcc = $this->_ci->EmailM->check_bcc();
		$this->_bcc = array_merge($this->_bcc, explode(',', $bcc));
	}

	private function _get_content() {
		if ($this->_content === '' && $this->_template === '') {
			log_message('error', 'Content or template must be specified.');exit();
		}
		if ($this->_content === '') {
			$this->_content = $this->_ci->EmailM->get_template_content($this->_template);
		}
	}

	private function _get_replace_value() {
		for($i=0;$i<count($this->_replace_value['keys']);$i++) {
			$this->_ci->smtpapiheaderl->addSubVal($this->_replace_value['keys'][$i], $this->_replace_value['values'][$i]);
		}
	}

	/**
	 * File size must be less than 7mb - http://docs.sendgrid.com/documentation/api/web-api/mail/
	 * Get file from s3, save to tmp folder for attaching
	 */
	private function _get_attachements() {
		foreach($this->_attachment_id as $docs_id => $ver_id) {
			if ($ver_id !== '') {
				$docs_detail = $this->_ci->DocsM->get_docs_ver_detail($ver_id);
			} else {
				$docs_detail = $this->_ci->DocsM->get_docs_detail($docs_id);
			}

			if (empty($docs_detail)) {
				return '';
			}

			// Check file size
			if ($docs_detail['a_docs_ver_filesize'] > (7168*1024)) {
				log_message('error', 'File size is > then 7168 Docsid: '.$docs_detail['a_docs_id'].' Verid: '.$docs_detail['a_docs_ver_id']);
				return '';
			}
			$uri = format_dirpath($docs_detail['a_docs_dir_dirpath'], $docs_detail['a_docs_ver_filename']);
			$object = S3::getObject($this->_bucket, $uri);

			$fp = fopen($this->_temp_dir.$docs_detail['a_docs_ver_filename'], 'wb');
			$i = fwrite($fp, $object->body);
			fclose($fp);

			// Start populating the $_files[] variable
			$this->_files[] = array('name' => $docs_detail['a_docs_ver_filename'],
				'path' => $this->_temp_dir.$docs_detail['a_docs_ver_filename'],
			);
		} // end foreach

	}

	private function _insert_email() {
		// Domain keys
		$this->_ci->smtpapiheaderl->addFilterSetting('domainkeys', 'enable', 1);
		$this->_ci->smtpapiheaderl->addFilterSetting('domainkeys', "domain", "www.telcoson.com");

		$data = array(
			'app_id' => $this->_ci->url['app_id'],
			'to' => serialize($this->_to),
			'toname' => serialize($this->_toname),
			'subject' => $this->_subject,
			's3file' => '', // path to s3 where the contents of email is stored
			'from' => $this->_from,
			'fromname' => $this->_fromname,
			'bcc' => serialize($this->_bcc),
			'replyto' => '',
			'date' => $this->_date,
			'file' => '',
			'headers' => '',
			'x-smtpapi' => $this->_ci->smtpapiheaderl->asJSON(),
			'respond' => '',
			'result' => '',
		);
		$this->_insert_id = $this->_ci->EmailM->insert_new_email($data);
		// Set unique args
		$this->_ci->smtpapiheaderl->setUniqueArgs(array('email_id'=>$this->_insert_id));
	}

	private function _upload_s3file() {
		$tfile = $this->_insert_id.'.txt';
		$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$tfile, 'w');
		fwrite($fp, $this->_content);
		fclose($fp);
		if (S3::putObject(S3::inputFile($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$tfile),
			$this->_bucket, $this->_email_storage_dir.$tfile, S3::ACL_PRIVATE)) {
			log_message('debug', 'Email content uploaded to S3 file: '.$tfile);
		}
	}

	private function _update_email() {
		if ($this->_attachment_id === '') {
			$data = array('query'=>$this->_query_str, 's3file'=>$this->_email_storage_dir.$this->_insert_id.'.txt');
		} else {
			$data = array('query'=>serialize($this->_query_array), 's3file'=>$this->_email_storage_dir.$this->_insert_id.'.txt');
		}

		$this->_ci->EmailM->update_email($this->_insert_id, $data);
	}

	private function _build_query() {
		// Start converting arrays to strings
		$to_str = '';
		for($i=0;$i<count($this->_to);$i++) {
			$to_str .= '&to[]='.$this->_to[$i].'&toname[]='.$this->_toname[$i];
		}
		$to_str = substr($to_str, 1);  //remove the first '&'

		if ( ! empty($this->_bcc[0])) {
			for($i=0;$i<count($this->_bcc);$i++) {
				if ($this->_bcc[$i] !== '') {
					$to_str .= '&bcc[]='.$this->_bcc[$i];
				}
			}
		}

		$this->_query_str = $to_str;
		$this->_query_str .= '&x-smtpapi='.$this->_ci->smtpapiheaderl->asJSON();
		/*
		if ( ! empty($this->_files)) {
			foreach ($this->_files as $file) {
				$this->_query_str .= '&files['.$file['name'].']=@'.$file['path'];
			}
		}*/
		$this->_query_str .= '&subject='.$this->_subject.
			'&from='.$this->_from.
			'&fromname='.$this->_fromname.
			'&html='.$this->_content.
			'&api_user='.$this->_api_user.
			'&api_key='.$this->_api_key;
	}

	function _build_query_array() {
		/** Limitations when using smptapiheader:
		 * There can only be one bcc
		 * To address cannot have name
		 */
		// Here, to and bcc has to go into smtpapiheader
		$this->_ci->smtpapiheaderl->addTo($this->_to);
		if ( ! empty($this->_bcc)) {
			$this->_ci->smtpapiheaderl->addFilterSetting('bcc','enable', 1);
			$this->_ci->smtpapiheaderl->addFilterSetting('bcc','email', $this->_bcc[0]);
		}

		$data = array(
			'api_user' => $this->_api_user,
			'api_key' => $this->_api_key,
			'to' => $this->_to,
			'bcc' => $this->_bcc,
			'x-smtpapi' => $this->_ci->smtpapiheaderl->asJSON(),
			'subject' => $this->_subject,
			'html' => $this->_content,
			'from' => $this->_from,
		);
		foreach($this->_files as $file) {
			$data['files['.$file['name'].']'] = '@'.$file['path'];
		}
		$this->_query_array = $data;
	}

	function debug() {
		$this->_get_to();
		$this->_get_bcc();
		$this->_get_content();
		if ( ! empty($this->_replace_value)) $this->_get_replace_value();
		$this->_date = date('r');
		if ($this->_attachment_id !== '') $this->_get_attachements();
		$this->_insert_email();
		if ($this->_attachment_id === '') {
			$this->_build_query(); // use query string when here is no attachment.
		} else {
			$this->_build_query_array();
		}
		$this->_upload_s3file();
		$this->_update_email();
		if ($this->_attachment_id === '') {
			print '<h1>DEBUG: </h1>'.$this->_query_str;
		} else {
			print '<h1>DEBUG: </h1>'.print_r($this->_query_array, true);
		}
	}

	function send_email () {
		$this->_get_to();
		$this->_get_bcc();
		$this->_get_content();
		if ( ! empty($this->_replace_value)) $this->_get_replace_value();
		$this->_date = date('r');
		if ($this->_attachment_id !== '') $this->_get_attachements();
		$this->_insert_email();
		if ($this->_attachment_id === '') {
			$this->_build_query(); // build query string when there is no attachment.
		} else {
			$this->_build_query_array();
		}
		$this->_upload_s3file();
		$this->_update_email();
		if ($this->_attachment_id === '') {
			$i = $this->_ci->curl->simple_post('https://sendgrid.com/api/mail.send.json', $this->_query_str); // use query string when there is no attachment. this will enables multiple bcc receipients.
		} else {
			// curl library doesnt allow passing in of arrays as parameter
			$request =  'http://sendgrid.com/api/mail.send.json';
			$session = curl_init($request);
			curl_setopt ($session, CURLOPT_POST, true);
			curl_setopt ($session, CURLOPT_POSTFIELDS, $this->_query_array);
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			$i = curl_exec($session);
			curl_close($session);
		}
		$i = json_decode($i, true);
		$this->log_send_response($i);
		return ($i['message'] === 'success') ? TRUE : FALSE;
	}

	function log_send_response($response) {
		$data = array('respond'=>$response['message']);
		$this->_ci->EmailM->update_email($this->_insert_id, $data);
		$str = date('F j, Y, g:i a') .": ";
		$str .= 'Sendgrid response: '.print_r($response, true)."\n";
		$fp = fopen($this->_temp_dir.'sendgrid_send_response.log','a+');
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
		$fp = fopen($this->_temp_dir.'sendgrid_'.$type.'.log','a+');
		fwrite($fp, $str);
		fclose($fp);
	}

	// $files_arr is from $_FILES, you need the tmp_name here.
	// $files is from [attachment-info], you need the filename here.
	function upload_attachment_s3 ($files_arr, $files) {
		foreach ($files_arr as $key => $file) {
			$f = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.$files[$key]['filename'];
			move_uploaded_file($file['tmp_name'], $f);
			if (S3::putObject(S3::inputFile($f), $this->_bucket, $this->_email_attachements_storage.$files[$key]['filename'], S3::ACL_PRIVATE)) {
				log_message('debug', 'Email attachement uploaded: '.$f);
			} else {
				log_message('error', 'Email attachement upload failed');
			}
		}
	}

	private function _get_bucket(){
		if (($contents = S3::getBucket($this->_bucket)) !== false) {
			return TRUE;
		}
		return FALSE;
	}

	private function _create_bucket() {
		if (S3::putBucket($this->_bucket, S3::ACL_PRIVATE)) {
			return TRUE;
		}
		return FALSE;
	}
}