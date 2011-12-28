<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Docs extends MY_Controller {
	private $_views_data, $_dir_path = array();
	private $_upload_status = FALSE;

	function __construct() {
		parent::__construct();
		$this->load->library('S3');
		$this->load->model('DocsM');

		$this->_views_data['folder_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/folder-small-horizontal.png">';
		$this->_views_data['docs_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/document-small.png">';
		$this->_views_data['tick_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/tick-circle.png" id="tick-icon">';
		$this->_views_data['cross_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/cross-circle.png" id="cross-icon">';
		$this->_views_data['loader_icon'] = '<img src="/resources/template/'.get_template().'/images/ajax-loader.gif" style="display:none;" id="loader-icon">';
		$this->_views_data['error_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/exclamation-red.png" style="display:none;" id="error-icon">';
	}
	function index() {
		redirect('/docs/view/0/list-view');
	}

	function view() {
		if ($this->url['subaction'] === 'folder-view') {

		} else {
			$folder_exists = $this->DocsM->does_folder_exists($this->url['id_plain']);
			if ( ! empty ($folder_exists)) {
				$this->_views_data['sub_folders'] = $this->DocsM->get_sub_folders();
				$i = $this->DocsM->get_parent($this->url['id_plain']);
				if ( ! empty($i)) $this->_views_data['parent'] = $this->DocsM->get_parent($this->url['id_plain']);
				$this->_views_data['docs'] = $this->DocsM->get_docs($this->url['id_plain']);
			} else {
				// Does user has a root directory
				$root_dir = $this->DocsM->get_root_dir();
				if ( ! empty($root_dir)) {
					redirect('/docs/view/'.$root_dir['a_docs_dir_id'].'/list-view');
				} else {
					// Create root folder
					$values['name'] = 'root';
					$id = $this->DocsM->update_folder($values);
					redirect('/docs/view/'.$id.'/list-view');
				}
			}
		}
		$data = array();
		$data['html'] = $this->load->view('/'.get_template().'/docs/docs_view_start.php', $this->_views_data, TRUE);
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;
		$data['div']['title'] = 'Documents';
		$data['div']['element_name'] = 'loginwin';
		$data['div']['element_id'] = 'divlogin';
		$this->data[] = $data;
		$this->LayoutM->load_format();
		$this->output();
	}

	function permission_form() {

	}

	function create_folder() {
		if ($this->input->get('name')) {
			$values['name'] = $this->input->get('name');
			$dirpath = $this->get_dirpath($this->url['id_plain']);
			$values['dirpath'] = $dirpath['dirpath_str'];
			if ($this->url['id_plain'] !== 0) $values['parent'] = $this->url['id_plain'];
			$this->output->set_content_type('application/json');
			$insert_id = $this->DocsM->update_folder($values);
			if ($insert_id) {
				return $this->output->set_output(json_encode(array('id'=>$insert_id,
					'name'=>$this->input->get('name'),
					'folder_icon'=>$this->_views_data['folder_icon'])));
			}
		}
		return $this->output->set_output(json_encode(array('error'=>'Error processing request')));
	}

	function delete_directory() {

	}

	function upload_form() {

	}

	function put_object() {
		$targetDir = '/Applications/XAMPP/htdocs/tcode/tmp/';
		$dirpath = $this->get_dirpath($this->url['id_plain']);

		// ----- S3 code ----
		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];
		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== FALSE) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				log_message('debug', 'Upload: '.$dirpath['dirpath_str'].$_FILES['file']['name']);
				if (S3::putObject(S3::inputFile($_FILES['file']['tmp_name']), 's3subscribers', $dirpath['dirpath_str'].$_FILES['file']['name'], S3::ACL_PRIVATE)) {
					$this->_upload_status = TRUE;
				} else {
					$this->_upload_status = FALSE;
				}
			}
		}
		// ----- end S3 code ---- /
		//$this->_upload_status
		if ($this->_upload_status) {
			$values['dirid'] = $this->url['id_plain'];
			$values['dirpath'] = $dirpath['dirpath_str'];
			$values['filename'] = $_FILES['file']['name'];
			$values['uploadvia'] = 'web';
			$values['filesize'] = $_FILES['file']['size'];
			$values['mime'] = $_FILES['file']['type'];
			log_message('debug','File type: '.$_FILES['file']['type']);
			$this->DocsM->update_doc($values);
		} else {

		}
	}

	function get_object($bucket, $uri) {
		//print getcwd();
		$object = S3::getObject($bucket, $uri, FALSE);
		return $object;
		/*
		$this->output->set_content_type($object->headers['type']);
		$this->output->set_output($object->body);*/
	}

	function get_object_url($bucket, $uri, $lifetime) {
		return S3::getAuthenticatedURL($bucket, $uri, $lifetime);
	}

	// Called when versioning is enabled.
	function replace_object() {

	}

	// Removes files from server. No way to retrieve.
	function delete_object() {

	}

	function get_dirpath($id) {
		$_dirpath = array();
		$_dirpath_str = '';
		while ($i = $this->DocsM->get_dir_parent_path($id)) {
			$_dirpath[] = $i[0];
			$id = $i[0]['a_docs_dir_parent'];
		};
		if ( ! empty($_dirpath)) {
			$_dirpath = array_reverse($_dirpath);
			$_dirpath_html = '<ul class="apphead_title">';
			foreach ($_dirpath as $path) {
				if ($path['a_docs_dir_name'] !== 'root') $_dirpath_str .= '/'. $path['a_docs_dir_name'];
				$_dirpath_html .= '<li><a href="/docs/view/'.$path['a_docs_dir_id'].'/'
					.$this->url['subaction'].'">'.$path['a_docs_dir_name'].'</a></li>';
			}
			$_dirpath_html .= '</ul>';
			return array('dirpath_str'=>$_dirpath_str,
					'dirpath_html'=>$_dirpath_html,
				);
		} else {
			return array('dirpath_str'=>'/',
					'dirpath_html'=>'',
				);
		}
	}

	function get_docs() {
		$docs = $this->DocsM->get_docs($this->url['id_plain']);
		if ( ! empty($docs)) {
			$this->output->set_content_type('application/json');
			return $this->output->set_output(json_encode($docs));
		} else {
			return $this->output->set_output(json_encode(array('message'=>'Your folder is empty.')));
		}
	}

	function preview() {
		$this->_views_data['docs_detail'] = $this->DocsM->get_docs_detail($this->url['id_plain']);
		$this->_views_data['s3_object'] = $this->get_object_url('s3subscribers', $this->_views_data['docs_detail']['a_docs_dir_dirpath'].$this->_views_data['docs_detail']['a_docs_ver_filename'], '3600');

		switch ($this->_views_data['docs_detail']['a_docs_ver_mime']) {
			case 'image/png':
				$this->_views_data['s3_object'] = '<img src="'.$this->_views_data['s3_object'].'">';
				break;
		}
		
		$data = array();
		$data['html'] = $this->load->view('/'.get_template().'/docs/docs_view_preview.php',$this->_views_data, TRUE);
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;
		$data['div']['title'] = 'Documents';
		$data['div']['element_name'] = 'loginwin';
		$data['div']['element_id'] = 'divlogin';
		$this->data[] = $data;
		$this->LayoutM->load_format();
		$this->output();
	}

	function test() {
		$i = $this->get_dirpath(1);
		print_r($i);
		$img =  S3::getAuthenticatedURL('s3subscribers', 'gravatar-140.png', '3600');
		echo '<img src="'.$img.'">';
	}
}