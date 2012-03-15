<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class File extends CI_Controller{
	var $domain = '';
	private $_filename = '';
	private $_filepath = '';

	function __construct() {
		parent::__construct();

		$this->setup_db();
		$this->load->library('fileL');
		$this->load->library('session');
		$this->load->model('LogM');
		$this->load->model('UserM');
		$this->load->model('ACLM');
		$this->load->helpers('form');
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

	function read() {
		$this->_get_file_path();
		$object = $this->filel->read($this->_filepath.$this->_filename);
		$this->output->set_header('Content-type: '.$object->headers['type'].';'
				.'Content-Disposition:inline; filename="'.$this->_filename.'";'
			)
			->set_output($object->body);
	}

	function save() {
		$fp = fopen($_FILES['file']['tmp_name'],'r');
		$content = fread($fp, $_FILES['file']['size']);
		$filename = $_FILES['file']['name'];
		$i = $this->filel->save_new($content, $this->input->post('path'), $filename, $this->input->post('overwrite'), $this->input->post('via'));
		print_r($i);
	}

	function delete() {
		var_dump($this->filel->delete('/vi_cheat_sheet.pdf'));
		/*
		if ($this->input->post('path')) {
			$this->filel->delete($this->_filepath);
		}*/
	}

	private function _get_file_path() {
		$filepath = $this->uri->segment_array();
		array_shift($filepath);array_shift($filepath); //throws controller away
		$filename = array_pop($filepath);
		if ( ! empty($filepath)) {
			$filepath = '/'.implode('/',$filepath);
		}
		else {
			$filepath = '/';
		}
		$this->_filename = urldecode($filename);
		$this->_filepath = $filepath;
	}

	function test() {
		$h = form_open_multipart('/file/save')
			.form_upload('file')
			.form_submit('submit', 'Submit')
			.form_hidden('path','/')
			.form_hidden('overwrite','1')
			.form_hidden('via','web');
		$h .= form_close();

		print $h;
	}

	function save2() {
		$content = 'dd';
		$docs_id = 5;
		$filename = 'nowman3.sql';
		$version = '0';
		$via = 'web';
		$i = $this->filel->save_existing($content, $docs_id, $filename, $version, $via);
		print_r($i);
	}

	function del1() {
		$docs_id = 5;
		$all = '0';
		$ver_id = 4;
		$i = $this->filel->del_by_id($docs_id, $all, $ver_id);
		var_dump($i);
	}

	function delall() {
		$docs_id = 5;
		$all = '1';
		$i = $this->filel->del_by_id($docs_id, $all);
		var_dump($i);
	}
}