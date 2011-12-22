<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Docs extends MY_Controller {
	private $_views_data = array();

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
		$root_folder = $this->DocsM->get_root_dir();
		$root_folder = ( ! empty($root_folder)) ? $root_folder->a_docs_dir_id : "#";
		redirect('/docs/view/'.$root_folder.'/list-view');
	}

	function view() {
		if ($this->url['subaction'] === 'folder-view') {

		} else{
			$this->_views_data['folders'] = $this->DocsM->get_folders();
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

	function setting_form() {

	}

	function permission_form() {

	}

	function create_folder() {
		$values['name'] = $this->input->get('name');
		$this->output->set_content_type('application/json');
		$insert_id = $this->DocsM->update_folder($values);
		if ($insert_id) {
			return $this->output->set_output(json_encode(array('id'=>$insert_id, 'name'=>$this->input->get('name'), 'folder_icon'=>$this->_views_data['folder_icon'])));
		} else {
			return $this->output->set_output(json_encode(array('error'=>'Error processing request')));
		}
	}

	function delete_directory() {

	}

	function upload_form() {

	}

	function put_object() {
		if (S3::putObject(S3::inputFile('tmp/get_object.pdf'), 's3subscribers', 'user1/get_something.pdf', S3::ACL_PRIVATE)) {
			echo "File uploaded.";
		} else {
			echo "Failed to upload file.";
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
}