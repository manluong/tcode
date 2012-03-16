<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Callback_sendgrid extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->setup_db();
		$this->load->spark('curl/1.2.0');
		$this->load->library('EmailL');
		$this->load->model('EmailM');
	}

	private function setup_db() {
		if (ENVIRONMENT != 'development') {
			$domain = explode('.', $_SERVER['SERVER_NAME']);
			//if ($domain[1]!=='8force' || $domain[2]!=='net') die('There is a problem with the domain name.');
			$this->domain = $domain[0];
		}

		if (ENVIRONMENT == 'development') return NULL;

		//load the default db settings in the configuration files
		include(APPPATH.'config/'.ENVIRONMENT.'/database.php');
		$config = $db['default'];

		//subdomain defines database table to use
		$config['database'] = 't_'.$this->domain;

		if (APP_ROLE == 'TSUB') {
			$config['username'] = 't_'.$this->domain;
		}

		if (APP_ROLE == 'TBOSS' && ENVIRONMENT == 'testing') {
			$config['database'] = 't_'.$this->domain.'2';
		}

		$this->load->database($config);
	}

	// Updates email result in serailized array
	function event_parser() {
		$this->emaill->log_sendgrid('events');
		$result = file_get_contents('php://input');
		if ($_SERVER['HTTP_USER_AGENT'] === 'SendGrid Event API') {
			// Split response into individual json_result
			preg_match_all('/\{.*\}/', $result, $json_result);

			// Get exisiting result array
			$i = json_decode($json_result[0][0],true);
			$email_id = $i['email_id'];
			$i = $this->EmailM->get_result_arr($email_id);
			//log_message('debug', '$i'.print_r($i,true));
			$result_arr = ($i !== '') ? unserialize($i) : array();
			log_message('debug', 'First $result_arr = '.print_r($result_arr,true));
			$key_exists = FALSE;
			foreach ($json_result[0] as $result) {
				$i = json_decode($result,true);
				foreach ($i as $key => $val) {
					if ($key === 'email') $k[] = $val;
					if ($key === 'event') $v[] = $val;
				}
				$combined_arr = array_combine($k, $v);
				log_message('debug', '$combined_arr = '.print_r($combined_arr,true));
				// Before pushing, check if key already exists
				if ( ! empty($result_arr)) {
					foreach ($result_arr as $key => &$val) {
						if ($key === $k[0]) $key_exists = TRUE;
						// Replace previous status
						$result_arr[$k[0]] = $v[0];
					}
				}
				if ( ! $key_exists) $result_arr = $combined_arr;
				unset($k);
				unset($v);
				$key_exists = FALSE;
			}
			// Update result array
			log_message('debug', '$new_result_arr = '.print_r($result_arr,true));
			$data = array('result' => serialize($result_arr));
			if ($this->EmailM->update_email_events_result($data, $email_id))
				log_message('debug', 'Email events updated');
			$this->output->set_header('HTTP/1.1 200');
		}
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

	function test () {
		$this->emaill->set_type('card')
			->set_type_id(array(2))
			->set_template('testplate')
			->set_subject('test')
			//->set_attachment_id(array(5=>4)) // docs_id => ver_id or just docs_id => ''
			->set_replace_value(array('keys'=>array('%name%', '%result%'), 'values'=>array(array('Roy'), array('Success!!'))))
			->set_from('docs@telcoson.com')
			->set_fromname('Docs');
		//$this->emaill->debug();die();
		var_dump($this->emaill->send_email());
	}
}