<?php defined('BASEPATH') OR exit('No direct script access allowed');

class EmailL {
	private $_ci; // private CI instance
	private $_to = array();
	private $_toname = array();
	private $_type_id = array();
	private $_bcc = array();
	private $_replace_value = array();
	private $_type = '';
	private $_from = '';
	private $_fromname = '';
	private $_template = '';
	private $_subject = '';
	private $_content = '';
	private $_date = '';
	private $_attachment_id = '';
	private $_query_str = '';

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
	}

	function set_type_id($type_id) {
		$this->_type_id = $type_id;
	}

	function set_subject($subject) {
		$this->_subject = $subject;
	}

	function set_bcc($bcc) {
		$this->_bcc = $bcc;
	}

	function set_template($template) {
		$this->_template = $template;
	}

	function set_content($content) {
		$this->_content = $content;
	}

	/**
	 * Example
	 * Version
	 * array(docs_id=>ver_id)
	 * Doc
	 * array(docs_id=>'')
	 */
	function set_attachment_id($attachment_id) {
		$this->_attachment_id = $attachment_id;
	}

	function set_to($to) {
		$this->_to = array_merge($to);
	}

	function set_toname($toname) {
		$this->_toname = array_merge($toname);
	}

	function set_from($from) {
		$this->_from = $from;
	}

	function set_fromname($fromname) {
		$this->_fromname = $fromname;
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

			print_r($docs_detail);
			$uri = $this->_ci->DocsM->format_dirpath($docs_detail['a_docs_dir_dirpath'], $docs_detail['a_docs_ver_filename']);
			print $uri;

			//$object = S3::getObject($this->_bucket, $uri);
		}
	}

	private function _insert_email() {
		$data = array(
			'app_id' => $this->_ci->url['app_id'],
			'to' => serialize($this->_to),
			'toname' => serialize($this->_toname),
			'subject' => $this->_subject,
			's3file' => '', // path to s3 files?
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
		$insert_id = $this->_ci->EmailM->insert_new_email($data);
		// Set unique args
		$this->_ci->smtpapiheaderl->setUniqueArgs(array('email_id'=>$insert_id));
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
		if (isset($this->_replace_value['keys'])) {
			$this->_query_str .= '&x-smtpapi='.$this->_ci->smtpapiheaderl->asJSON();
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
		print $this->_query_str;
	}

	function send_email () {
		$this->_get_to();
		$this->_get_bcc();
		$this->_get_content();
		$this->_get_replace_value();
		$this->_date = date('r');
		$this->_insert_email();

		$this->_build_query();
		$i = $this->_ci->curl->simple_post('https://sendgrid.com/api/mail.send.json', $this->_query_str);
		return $i;
	}
}