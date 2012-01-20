<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email extends MY_Controller {

	private $_views_data = array();
	private $_api_user = 'tcsteam';
	private $_api_key = 'express08)*';

	function __construct() {
		parent::__construct();

		$this->load->spark('curl/1.2.0');
		$this->load->library('EmailL');
	}

	function index() {
		$data['html'] = $this->load->view('/'.get_template().'/email/email_start.php', $this->_views_data, TRUE);
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;
		$data['div']['title'] = 'Email';
		$data['div']['element_name'] = 'email';
		$data['div']['element_id'] = 'email';
		$this->data[] = $data;
		$this->LayoutM->load_format();
		$this->output();
	}

	function test () {
		$this->emaill->set_type('card');
		$this->emaill->set_type_id(array(2));
		$this->emaill->set_to(array('roy.wong.80@gmail.com'));
		$this->emaill->set_toname(array('roygmail'));
		$this->emaill->set_template('testplate');
		$this->emaill->set_content('hhhh');
		$this->emaill->set_subject('test');
		$this->emaill->set_attachment_id(array(5=>4));
		$this->emaill->set_replace_value(array('keys'=>array('%name%', '%result%'), 'values'=>array(array('Roy'), array('Success!!'))));
		$this->emaill->set_from('docs@telcoson.com');
		$this->emaill->set_fromname('Docs');
		$i = $this->emaill->debug();
	}

	function send_email() {
		if ($this->input->post('to') && $this->input->post('toname') && $this->input->post('subject')
			&& $this->input->post('text') && $this->input->post('from')
		) {
			/*
			$data = $this->_get_url();
			$i = $this->curl->simple_post('https://sendgrid.com/api/mail.send.json', $data);
			$this->output->set_content_type('application/json');
			$this->output->set_output($i); */

			$result = json_decode($i);
			$this->EmailM->record_sent_mail($data, $result['message']);
		}
	}

	private function _get_url() {
		$url = '';
		$url_arr = array();

		$to_arr = explode(',', $this->input->post('to'));
		$toname_arr = explode(',', $this->input->post('toname'));
		if (count($to_arr) > 1 && count($to_arr) === count($toname_arr))  { // multi receivers
			for($i=0;$i<count($to_arr);$i++) {
				$url .= '&to[]='.$to_arr[$i].'&toname[]='.$toname_arr[$i];
			}
			$url .= '&subject='.$this->input->post('subject').'&text='.$this->input->post('text')
			.'&from='.$this->input->post('from').'&api_user='.$this->_api_user
			.'&api_key='.$this->_api_key.'&date='.date('r');

			$bcc = $this->emailM->check_bcc();
			if ($bcc !== '') {
				$url .= '&bcc='.$bcc;
			}

			$url = substr($url, 1);
			return $url;
		} else { // single receiver
			$url_arr = array(
				'to' => $this->input->post('to'),
				'toname' => $this->input->post('toname'),
				'subject' => $this->input->post('subject'),
				'text' => $this->input->post('text'),
				'from' => $this->input->post('from'),
				'api_user' => $this->_api_user,
				'api_key' => $this->_api_key,
				'date' => date('r'),
			);
			$bcc = $this->emailM->check_bcc();
			if ($bcc !== '') {
				$url_arr['bcc'] = $bcc;
			}
			return $url_arr;
		}
	}
}