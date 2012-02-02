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
	private $_api_user = 'tcsteam';
	private $_api_key = 'express08)*';
	private $_bucket = 's3subscribers';

	function __construct($url = '') {
		$this->_ci = & get_instance();
		$this->_ci->load->model('EmailM');
		$this->_ci->load->model('DocsM');
		$this->_ci->load->spark('curl/1.2.0');
		$this->_ci->load->library(array('SmtpApiHeaderL', 'S3'));
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
			if ($docs_detail['a_docs_ver_filesize'] > 7168) {
				log_message('error', 'File size is > then 7168 Docsid: '.$docs_detail['a_docs_id'].' Verid: '.$docs_detail['a_docs_ver_id']);
				return '';
			}
			$uri = format_dirpath($docs_detail['a_docs_dir_dirpath'], $docs_detail['a_docs_ver_filename']);
			$object = S3::getObject($this->_bucket, $uri);

			$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$docs_detail['a_docs_ver_filename'], 'wb');
			$i = fwrite($fp, $object->body);
			fclose($fp);

		} // end foreach
		// Start populating the $_files[] variable
		$this->_files[] = array('name' => $docs_detail['a_docs_ver_filename'],
			'path' => $_SERVER['DOCUMENT_ROOT'].'/tmp/'.$docs_detail['a_docs_ver_filename'],
		);
	}

	private function _insert_email() {
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
		$data = array('query'=>$this->_query_str, 's3file'=>$this->_email_storage_dir.$this->_insert_id.'.txt');
		$this->_ci->EmailM->update_email($this->_insert_id, $data);
	}

	private function _build_query() {
		// Start converting arrays to strings
		$to_str = '';
		for($i=0;$i<count($this->_to);$i++) {
			$to_str .= '&to[]='.$this->_to[$i].'&toname[]='.$this->_toname[$i];
		}
		$to_str = substr($to_str, 1);  //remove the first '&'

		for($i=0;$i<count($this->_bcc);$i++) {
			$to_str .= '&bcc[]='.$this->_bcc[$i];
		}

		$this->_query_str = $to_str;
		$this->_query_str .= '&x-smtpapi='.$this->_ci->smtpapiheaderl->asJSON();
		if ( ! empty($this->_files)) {
			foreach ($this->_files as $file) {
				$this->_query_str .= '&files['.$file['name'].']=@'.$file['path'];
			}
		}
		$this->_query_str .= '&subject='.$this->_subject.
			'&from='.$this->_from.
			'&fromname='.$this->_fromname.
			'&html='.$this->_content.
			'&api_user='.$this->_api_user.
			'&api_key='.$this->_api_key;
	}

	function debug() {
		$this->_get_to();
		$this->_get_bcc();
		$this->_get_content();
		$this->_get_replace_value();
		$this->_date = date('r');
		$this->_get_attachements();
		$this->_insert_email();
		$this->_build_query();
		$this->_upload_s3file();
		$this->_update_email();
		print '<h1>DEBUG: </h1>'.$this->_query_str;
	}

	function send_email () {
		$this->_get_to();
		$this->_get_bcc();
		$this->_get_content();
		$this->_get_replace_value();
		$this->_date = date('r');
		$this->_get_attachements();
		$this->_insert_email();
		$this->_build_query();
		$this->_upload_s3file();
		$this->_update_email();
		$i = $this->_ci->curl->simple_post('https://sendgrid.com/api/mail.send.json', $this->_query_str);
		$i = json_decode($i, true);
		return ($i['message'] === 'success') ? TRUE : FALSE;
	}
}