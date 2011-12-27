<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Docs extends MY_Controller {
	private $_views_data, $_dir_path = array();

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

		} else{
			$this->_views_data['folders'] = $this->DocsM->get_folders();
			$i = $this->DocsM->get_parent($this->url['id_plain']);
			if ( ! empty($i)) $this->_views_data['parent'] = $this->DocsM->get_parent($this->url['id_plain']);


			$data = array();
			$data['html'] = $this->load->view('/'.get_template().'/docs/docs_view_start.php', $this->_views_data, TRUE);
		}
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
		$dir_path = $this->get_dir_path($this->url['id_plain']);

		/*/ ----- S3 code ----
		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];
		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				if (S3::putObject(S3::inputFile($_FILES['file']['tmp_name']), 's3subscribers', $_FILES['file']['name'], S3::ACL_PRIVATE)) {
					$this->_upload_status = TRUE;
				} else {
					$this->_upload_status = FALSE;
				}
			}
		}
		// ----- end S3 code ---- */
		//$this->_upload_status
		if (TRUE) {
			$latest_docsid = $this->DocsM->get_latest_docsid();
			$latest_docsid = ( ! empty($latest_docsid)) ? $latest_docsid['a_docs_docsid'] : 0;
			$values['docsid'] =  $latest_docsid + 1;
			$values['dirid'] = $this->input->get('dirid');
			$values['dirpath'] = $dir_path['dir_path_str'];
			$values['filename'] = $_FILES['file']['name'];
			$values['uploadvia'] = 'web';
			$values['filesize'] = $_FILES['file']['size'];
			$values['mime'] = $_FILES['file']['type'];
			$this->DocsM->update_doc($values);
		} else {

		}
	}

	function get_object () {
		//print getcwd();
		$object = S3::getObject('wwwbooks', '0137136692.pdf', FALSE);
		$this->output->set_content_type($object->headers['type']);
		$this->output->set_output($object->body);
	}

	// Called when versioning is enabled.
	function replace_object() {

	}

	// Removes files from server. No way to retrieve.
	function delete_object() {

	}

	function get_dir_path($id) {
		$_dir_path = array();
		$_dir_path_str = '';
		while ($i = $this->DocsM->get_dir_parent_path($id)) {
			$_dir_path[] = $i[0];
			$id = $i[0]['a_docs_dir_parent'];
		};
		if ( ! empty($_dir_path)) {
			$_dir_path = array_reverse($_dir_path);
			$_dir_path_html = '<ul class="apphead_title">';
			foreach ($_dir_path as $path) {
				$_dir_path_str .= '/'.$path['a_docs_dir_name'];
				$_dir_path_html .= '<li><a href="/docs/view/'.$path['a_docs_dir_id'].'/'
					.$this->url['subaction'].'">'.$path['a_docs_dir_name'].'</a></li>';
			}
			$_dir_path_html .= '</ul>';
			return array('dir_path_str'=>$_dir_path_str,
					'dir_path_html'=>$_dir_path_html,
				);
		} else {
			return array('dir_path_str'=>'/',
					'dir_path_html'=>'',
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

	function test() {
		$this->get_dir_path();
		print_r($this->_dir_path);
	}
}