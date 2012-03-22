<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Docs extends MY_Controller {
	private $_views_data, $_dir_path = array();
	private $_upload_status = FALSE;
	private $_temp_dir = '';
	private $_bucket = '';

	function __construct() {
		parent::__construct();
		$this->load->library('fileL');
		$this->load->library('CommentsL');
		$this->_bucket = $this->domain .'.telcoson.net.test';
		if ( ! $this->_get_bucket($this->_bucket)) {
			if ($this->_create_bucket($this->_bucket)) {
				log_message('debug', 'Created bucket '.$this->_bucket);
			} else {
				log_message('debug', 'Failed to create bucket ' . $this->_bucket);
			}
		}

		$this->load->model('DocsM');
		$this->_temp_dir = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.$this->domain.'/docs/files/';

		$this->_views_data['folder_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/folder-small-horizontal.png">';
		$this->_views_data['docs_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/document-small.png">';
		$this->_views_data['tick_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/tick-circle.png" id="tick-icon">';
		$this->_views_data['cross_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/cross-circle.png" id="cross-icon">';
		$this->_views_data['loader_icon'] = '<img src="/resources/template/'.get_template().'/images/ajax-loader.gif" style="display:none;" id="loader-icon">';
		$this->_views_data['error_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/exclamation-red.png" style="display:none;" id="error-icon">';
	}

	function set_bucket($bucket) {
		$this->_bucket = $bucket; return this;
	}

	function ajax_create_folder() {
		$insert_id = $this->_create_folder($this->url['id_plain'], $this->input->post('cardid'), $this->input->post('name'));
		$this->output->set_content_type('application/json');
		($insert_id)
			? $this->output->set_output(json_encode(array('success' => '1')))
			: $this->output->set_output(json_encode(array('success' => '0')));
	}

	private function _create_folder($id, $cardid, $name) {
		//dirpath finds the full directory patht to update
		if ($name === 'root') {
			$path = '/'.$this->domain;
		} else {
			$_dirpath = $this->DocsM->get_dirpath_dir($id);
			$path = $this->format_dirpath($_dirpath['a_docs_dir_dirpath'], $name);
		}

		$data = array(
			'a_docs_parentid' => $id,
			'a_docs_isdir' => TRUE,
			'a_docs_displayname' => $name,
			'a_docs_desc' => '',
			'a_docs_status' => '',
			'a_docs_stamp' => get_current_stamp(),
			'a_docs_app_data_id' => '',
		);
		$insert_id = $this->DocsM->insert_a_docs_entry($data);

		$data = array(
			'a_docs_dir_docs_id' => $insert_id,
			'a_docs_dir_dirpath' => $path,
			'a_docs_dir_cardid' => $cardid,
			'a_docs_dir_dirtype' => '',
			'a_docs_dir_hide' => '',
			'a_docs_dir_nofile' => '',
			'a_docs_dir_nodir' => '',
			'a_docs_dir_filemaxsize' => '',
			'a_docs_dir_dirmaxsize' => '',
			'a_docs_dir_listmime' => '',
			'a_docs_dir_listext' => '',
			'a_docs_dir_listtype' => '',
			'a_docs_dir_socialcomment' => '',
			'a_docs_dir_sociallike' => '',
			'a_docs_dir_socialstar' => '',
			'a_docs_dir_socialack' => '',
			'a_docs_dir_encrypt' => '',
			'a_docs_dir_browsestyle' => '',
			'a_docs_dir_app' => '',
			'a_docs_dir_action' => '',
			'a_docs_dir_subaction' => '',
			'a_docs_dir_noocrindex' => '',
		);
		$insert_id = $this->DocsM->insert_a_docs_dir_entry($data);
		return $insert_id;
	}

	// Removes files from server. No way to retrieve.
	function delete_all_docs() {
		$this->output->set_content_type('application/json');
		$_obj_details = $this->DocsM->get_docs_detail($this->url['id_plain']);
		if (empty($_obj_details)) {
			return $this->output->set_header('HTTP/1.1 400');
		}
		$versions = $this->DocsM->get_all_versions($this->url['id_plain']);
		foreach ($versions as $version) {
			if (S3::deleteObject($this->_bucket, $this->format_dirpath($_obj_details['a_docs_dir_dirpath'], $version['a_docs_ver_filename']))) {
				log_message('debug', 'Docs: Deleted '. $this->format_dirpath($_obj_details['a_docs_dir_dirpath'], $version['a_docs_ver_filename']));
			}
		}
		$i = '';
		$i = $this->DocsM->delete_all_docs($_obj_details['a_docs_id']);
		($i)
		 ? $this->output->set_output(json_encode(array('success' => '1')))
		 : $this->output->set_output(json_encode(array('success' => '0')));
	}

	function delete_single_ver() {
		// Usage /docs/delete_single_ver/:docs_id/d/:ver_id
		$this->output->set_content_type('application/json');
		$docs_id = $this->input->post('docs_id');
		$ver_id = $this->input->post('ver_id');
		$i = $this->filel->del_by_id($docs_id, '0', $ver_id);
		($i)
		? $this->output->set_output(json_encode(array('success' => '1')))
		 : $this->output->set_output(json_encode(array('success' => '0')));
	}

	/*
	function display_file_functions () {
		$this->_views_data['root_dir'] = $this->DocsM->get_root_dir();
		$this->_views_data['tree'] = $this->get_tree($this->_views_data['root_dir']['a_docs_id']);
		$data = array();
		$data['html'] = $this->load->view('/'.get_template().'/docs/docs_view_file_functions.php',$this->_views_data,TRUE);
		$data['isoutput'] = 1;
		$data['isdiv'] = 1;
		return $data;
	} */

	function download_file() {
		$i = $this->DocsM->get_docs_ver_detail($this->url['id_plain']);
		if ( ! empty($i)) {
			$uri = $this->format_dirpath($i['a_docs_dir_dirpath'], $i['a_docs_ver_filename']);
			$_filecontent = $this->get_object($this->_bucket, $uri);
			$this->output->set_content_type($i['a_docs_ver_mime']);
			$this->output->set_header('Content-Disposition: attachment; filename="'.$i['a_docs_ver_filename'].'"');
			$this->output->set_output($_filecontent->body);
		}
	}

	// Helper function to format full path correctly
	function format_dirpath($dirpath, $filename) {
		if ($dirpath === '/') {
			return $dirpath.$filename;
		} else {
			return $dirpath.'/'.$filename;
		}
	}

	/*
	function get_docs() {
		$docs = $this->DocsM->get_docs($this->url['id_plain']);
		if ( ! empty($docs)) {
			$this->output->set_content_type('application/json');
			return $this->output->set_output(json_encode($docs));
		} else {
			return $this->output->set_output(json_encode(array('message'=>'Your folder is empty.')));
		}
	}*/

	function get_object($bucket, $uri) {
		$object = S3::getObject($bucket, $uri, FALSE);
		return $object;
	}

	function get_object_url($bucket, $uri, $lifetime) {
		return S3::getAuthenticatedURL($bucket, $uri, $lifetime);
	}

	function json_tree() {
		$tree = $this->_get_tree(1); //gets tree from root dir

		$this->output->set_content_type('application/json')
			->set_output(json_encode($tree));
	}

	private function _get_tree($a_docs_id) {
		$i = $this->DocsM->get_sub_folders($a_docs_id);
		foreach($i as &$sub_folder) {
			$j = $this->_get_tree($sub_folder['a_docs_id']);
			if ( ! empty($j)) $sub_folder['child'] = $j;
		}
		return $i;
	}

	/*
	function index() {
		redirect('/docs/view/0/list-view');
	}*/

	function move_file() {
		if ($this->input->post('folder_id')) {
			$this->output->set_content_type('application/json');
			$versions = $this->DocsM->get_all_versions($this->url['id_plain']);
			$new_dir = $this->DocsM->get_dirpath_dir($this->input->post('folder_id'));
			foreach ($versions as $version) {
				if (S3::copyObject($this->_bucket, $this->format_dirpath($version['a_docs_dir_dirpath'],$version['a_docs_ver_filename']), $this->_bucket, $this->format_dirpath($new_dir['a_docs_dir_dirpath'], $version['a_docs_ver_filename']), S3::ACL_PRIVATE)) {
					log_message('debug', 'Docs: Copied file '.$this->format_dirpath($version['a_docs_dir_dirpath'],$version['a_docs_ver_filename'].' to '.$this->format_dirpath($new_dir['a_docs_dir_dirpath'], $version['a_docs_ver_filename'])));
					if (S3::deleteObject($this->_bucket, $this->format_dirpath($version['a_docs_dir_dirpath'],$version['a_docs_ver_filename']))) {
						log_message('debug', 'Docs: Removed file '. $this->format_dirpath($version['a_docs_dir_dirpath'],$version['a_docs_ver_filename']));
					}
				}
				$this->DocsM->update_docs_location($this->input->post('folder_id'), $this->url['id_plain']);
				$this->output->set_output(json_encode(array('success'=>'1')));
			}
		} else {
			$this->output->set_output(json_encode(array('success'=>'0')));
		}
	}

	/**
	 * Returns json details of document
	 */
	function get_file_details() {
		if ($this->uri->segment(5)) {
			$docs_details = $this->DocsM->get_docs_ver_detail($this->uri->segment(5,0));
		} else {
			$docs_details = $this->DocsM->get_docs_detail($this->url['id_plain']);
		}

		if (empty($docs_details)) {
			$this->output->set_content_type('application/json')
				->set_output(json_encode(array('success'=>0, 'message'=>'No details found')));
			return;
		}
		$past_versions = $this->DocsM->get_all_versions($this->url['id_plain']);
		switch ($docs_details['a_docs_ver_mime']) {
			case 'image/gif':
			case 'image/jpeg':
			case 'image/png':
				$s3object = html_entity_decode($this->get_object_url($this->_bucket,$this->format_dirpath($docs_details['a_docs_dir_dirpath'],$docs_details['a_docs_ver_filename']), 3600));
				//$s3object = '<img src="'.$this->_views_data['s3_object'].'">';
				break;
			case 'application/pdf':
				$s3object = $this->get_object($this->_bucket, $this->format_dirpath($docs_details['a_docs_dir_dirpath'],$docs_details['a_docs_ver_filename']));
				$this->save_to_file($s3object->body, $docs_details['a_docs_id'].'.pdf');
				break;
		}
		$data = array('docs_details'=>$docs_details, 's3object'=>$s3object, 'versions'=>$past_versions);
		$this->output->set_content_type('application/json')
			->set_output(json_encode($data));

		/*
		$data = array();
		$data['html'] = $this->load->view('/'.get_template().'/docs/docs_view_preview.php',$this->_views_data, TRUE);
		$data['isoutput'] = 1;
		$data['isdiv'] = 1;
		return $data; */
	}


	function load_file_html() {
		$data = array();
		$vars['url'] = $this->url;
		$vars['page'] = '/'.get_template().'/docs/docs_file_html';
		$data['html'] = $this->load->view('/'.get_template().'/docs/docs_view',$vars,TRUE);
		$data['isoutput'] = 1;
		$data['isdiv'] = 1;
		return $data;
	}

	function check_version_settings() {
		// Check docs_dir and return, if not found default to docs_settings table
		$dir_id = $this->DocsM->get_dir_id_from_docs_id($this->url['id_plain']);
		if (! $dir_id) {
			log_message('debug', 'Cannot locate dir_id of docs_id: '.$this->url['id_plain']);
			return FALSE;
		}
		$_docs_dir_ver = $this->DocsM->get_docs_dir_ver($dir_id);
		// 3 will take a_docs_setting's versioning
		if ( ! empty($_docs_dir_ver) && $_docs_dir_ver['a_docs_dir_versioning'] !== '3') {
			return $_docs_dir_ver['a_docs_dir_versioning'];
		}
		$_docs_app_ver = $this->DocsM->get_docs_settings();
		return $_docs_app_ver['a_docs_setting_versioning'];
	}

	function upload() {
		// Insert new doc entry for every upload
		$filename = $this->check_filename($_FILES['file']['name']);
		$this->put_object($this->url['id_plain'], $filename);
	}

	function upload_single() {
		// eg http://apple.telcoson.local/docs/upload_single/:docs_id/upload/:ver_id
		$_ver_setting = $this->check_version_settings();
		$_filename = $this->check_filename($_FILES['file']['name']); // Get new filename if filename conflict
		if ($_ver_setting !== '0') {
			$this->create_new_ver($_filename, $this->uri->segment(5,0));
			return TRUE;
		}
		$this->replace_file($this->url['id_plain'], $_filename, $this->uri->segment(5,0));
	}

	function create_new_ver($filename, $ver_id) {
		$dirpath = $this->DocsM->get_dirpath($this->url['id_plain']);
		$this->s3_put_object($dirpath, $filename);
		if ($this->_upload_status) {
			$values = array(
				'a_docs_ver_docsid' => $this->url['id_plain'],
				'a_docs_ver_uploadvia' => 'web',
				'a_docs_ver_filename' => $filename,
				'a_docs_ver_stamp'=> get_current_stamp(),
				'a_docs_ver_filesize' => $_FILES['file']['size'],
				'a_docs_ver_cardid' => $this->UserM->info['cardid'],
				'a_docs_ver_mime'=> $_FILES['file']['type'],
				'a_docs_ver_current_version' => 1,
			);
			$this->rename_old_ver($dirpath, $this->url['id_plain']);
			$this->DocsM->set_all_current_ver($this->url['id_plain'], 0);
			$this->DocsM->insert_docs_ver($values);
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode(array('success'=>'1')));
		} else {
			$this->output->set_header('HTTP/1.1 500');exit();
		}
	}

	function rename_old_ver(&$dirpath, &$docs_id) {
		$ver_id = $this->DocsM->get_docs_detail($docs_id);
		$ver_detail = $this->DocsM->get_docs_ver_detail($ver_id['a_docs_ver_id']);
		$values['a_docs_ver_id'] = $ver_detail['a_docs_ver_id'];
		$values['a_docs_ver_filename'] = '._'.$ver_detail['a_docs_ver_filename'];
		if (S3::copyObject($this->_bucket, $this->format_dirpath($dirpath['a_docs_dir_dirpath'],$ver_detail['a_docs_ver_filename']),
			$this->_bucket, $this->format_dirpath($dirpath['a_docs_dir_dirpath'], $values['a_docs_ver_filename']), S3::ACL_PRIVATE)) {
			log_message('debug', 'Docs: Copied file to '. $this->format_dirpath($dirpath['a_docs_dir_dirpath'], $values['a_docs_ver_filename']));
			if (S3::deleteObject($this->_bucket, $this->format_dirpath($dirpath['a_docs_dir_dirpath'],$ver_detail['a_docs_ver_filename']))) {
				log_message('debug', 'Docs: Removed old renamed file: '.$this->format_dirpath($dirpath['a_docs_dir_dirpath'],$ver_detail['a_docs_ver_filename']));
			}
			$this->DocsM->update_docs_ver($values);
		}
	}

	function replace_file($docs_id, $filename, $ver_id) {
		$_dirpath = $this->DocsM->get_dirpath($docs_id);
		$this->s3_put_object($_dirpath, $filename);
		if ($this->_upload_status) {
			$values['a_docs_ver_id'] = $ver_id;
			$values['a_docs_ver_filename'] = $filename;
			$values['a_docs_ver_stamp'] = get_current_stamp();
			$values['a_docs_ver_mime'] = $_FILES['file']['type'];
			$values['a_docs_ver_filesize'] = $_FILES['file']['size'];
			$values['a_docs_ver_stamp'] = get_current_stamp();
		} else {
			log_message('debug', 'Docs: Error uploading File: '. $filename);exit();
		}
		$this->remove_old_file($ver_id, $_dirpath);
		$this->DocsM->update_docs_ver($values);exit();
	}

	function remove_old_file ($ver_id, $dirpath) {
		$filename = $this->DocsM->get_file_name($ver_id);
		$uri = $this->format_dirpath($dirpath['a_docs_dir_dirpath'], $filename['a_docs_ver_filename']);
		if(S3::deleteObject($this->_bucket, $uri)) {
			log_message('debug', 'Docs: Removed old File:'.$uri);
		} else {
			log_message('debug', 'Docs: Error old File:'.$filename['a_docs_ver_filename']);
		}
	}

	function check_filename($filename, $i=0) {
		$i++;
		// Break filename into filename and extension
		$_ext = substr(strrchr($filename, '.'), 0);
		$_ext_length = strlen($_ext);
		$_filename = substr($filename,0,strlen($filename)-$_ext_length);

		if ( ! $this->is_file_exists($_filename.$_ext)) {
			return $filename;
		} else {
			if ($this->is_file_exists($_filename.'_'.randStr(3).$_ext)) {
				$this->check_filename($_filename, $i);
			}
		}
		return $_filename.'_'.randStr(3).$_ext;
	}

	function is_file_exists($filename) {
		$i = $this->DocsM->search_filename($filename);
		if ( ! empty ($i)) return TRUE;
		return FALSE;
	}

	// For Mass upload
	function put_object($folder_id, $filename) {
		$dirpath = $this->DocsM->get_dirpath_dir($folder_id);
		$this->s3_put_object($dirpath, $filename);
		if ($this->_upload_status) {
			$values['a_docs_parentid'] = $folder_id;
			$values['a_docs_ver_filename'] = $filename;
			$values['a_docs_ver_uploadvia'] = 'web';
			$values['a_docs_ver_filesize'] = $_FILES['file']['size'];
			$values['a_docs_ver_mime'] = $_FILES['file']['type'];
			$values['a_docs_ver_current_version'] = 1;
			$this->DocsM->insert_docs($values);exit();
		}
		$this->output->set_header('HTTP/1.1 500');
		log_message('debug',"Docs: Upload failed.\n ".'Content-type:'.$contentType."\n");exit();
	}

	function s3_put_object(&$dirpath, &$filename) {
		// ----- S3 code ----
		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];
		log_message('debug','Content-type:'.$contentType."\n");
		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== FALSE) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				if (S3::putObject(S3::inputFile($_FILES['file']['tmp_name']), $this->_bucket,
					$this->format_dirpath($dirpath['a_docs_dir_dirpath'], $filename), S3::ACL_PRIVATE)) {
					$this->_upload_status = TRUE;
					log_message('debug', 'Docs: Upload: '.$this->format_dirpath($dirpath['a_docs_dir_dirpath'], $filename));
				} else {
					$this->_upload_status = FALSE;
				}
			}
		}
		// ----- end S3 code ---- /
		return;
	}

	function save_to_file(&$binary, $filename){
		// Create dir if it doesnt exist
		if (! is_dir('tmp/'.$this->domain.'/docs/files/')) {
			$oldumask = umask(0);
			mkdir('tmp/'.$this->domain.'/docs/files/', 0777, true);
			umask($oldumask);
		}

		$fp = fopen($this->_temp_dir.$filename, 'wb');
		$r = fwrite($fp, $binary);
		fclose($fp);
	}

	/**
	 * Returns json contents of directory for datatable.
	 */
	function get_dir_contents() {
		$rows = array();
		$sub_folders = $this->DocsM->get_sub_folders($this->url['id_plain']);
		if ($this->url['id_plain'] !== '1') {
			$parent = $this->DocsM->get_parent_id($this->url['id_plain']);
			$parent = anchor('/docs/view/'.encode_id($parent['a_docs_parentid']).'/list-view','prev', 'class="ajax"');
			array_push($rows, array($parent,'--','--'));
		}

		$docs = $this->DocsM->get_docs($this->url['id_plain']);
		foreach ($sub_folders as $subfolder) {
			$name = anchor('/docs/view/'.encode_id($subfolder['a_docs_id']).'/list-view',$subfolder['a_docs_displayname'], 'class="ajax"');
			array_push($rows, array($name, '--', '--'));
		}
		foreach ($docs as $doc) {
			$name = ($doc['a_docs_displayname'] !== '') ? $doc['a_docs_displayname'] : $doc['a_docs_ver_filename'];
			$link = anchor('/docs/file/'.encode_id($doc['a_docs_id']).'/v',$name, 'class="ajax"');

			array_push($rows, array($link, byte_size($doc['a_docs_ver_filesize']), $doc['a_docs_stamp']));
		}
		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('aaData'=>$rows)));
	}

	/**
	 * Returns raw json values of directory contents
	 */
	function get_dir_contents_raw() {
		$sub_folders = $this->DocsM->get_sub_folders($this->url['id_plain']);
		if ($this->url['id_plain'] !== '1') {
			$parent = $this->DocsM->get_parent_id($this->url['id_plain']);
		}
		$docs = $this->DocsM->get_docs($this->url['id_plain']);
		$json = array('parent' => isset($parent) ? $parent : '',
			'sub_folder' => $sub_folders, 'docs' => $docs);
		$this->output->set_content_type('application/json')
			->set_output(json_encode($json));
	}

	// Called by view in tbuilder
	function load_html() {
		if ($this->url['subaction'] === 'folder-view') {

		} else {
			$folder_exists = $this->DocsM->does_folder_exists($this->url['id_plain']);
			if ( ! empty ($folder_exists)) {

			} else {
				// Does user has a root directory
				$root_dir = $this->DocsM->get_root_dir();
				if ( ! empty($root_dir)) {
					redirect('/docs/view/'.encode_id($root_dir['a_docs_id']).'/list-view');
				} else {
					// Create root folder
					$this->_create_folder(0, $this->UserM->info['cardid'], 'root');
					redirect('/docs/view/'.encode_id($id).'/list-view');
				}
			}
		}

		$data = array();
		$vars['url'] = $this->url;
		$vars['page'] = '/'.get_template().'/docs/docs_view_html';
		$data['html'] = $this->load->view('/'.get_template().'/docs/docs_view',$vars,TRUE);
		$data['isoutput'] = 1;
		$data['isdiv'] = 1;

		return($data);
	}

	function update_docs_title() {
		// /docs/update_docs_title/:docs_id/title
		if ($this->input->post('title')) {
			$i = $this->DocsM->update_docs_display_name($this->input->post('title'), $this->url['id_plain']);
			$this->output->set_content_type('application/json');
			($i)
			? $this->output->set_output(json_encode(array('success'=>1)))
			: $this->output->set_output(json_encode(array('success'=>0)));
		} else {
			log_message('debug', 'Missing title parameter');
		}

	}


	function pdfPreview() {
		require_once('resources/addon/docs/AdaptiveUI1.3.5/common.php');
		require_once('resources/addon/docs/AdaptiveUI1.3.5/pdf2json_php5.php');
		require_once('resources/addon/docs/AdaptiveUI1.3.5/pdf2swf_php5.php');
		require_once('resources/addon/docs/AdaptiveUI1.3.5/swfrender_php5.php');

		$doc 	= $_GET["doc"];
		$pdfdoc 	= $doc . ".pdf";
		$configManager 	= new Config();

		if(isset($_GET["page"])){$page = $_GET["page"];}else{$page = "";}
		if(isset($_GET["format"])){$format=$_GET["format"];}else{$format="swf";}
		if($configManager->getConfig('splitmode')){$swfdoc 	= $pdfdoc . "_" . $page . ".swf";}else{$swfdoc 	= $pdfdoc . ".swf";}

		$pngdoc 		= $pdfdoc . "_" . $page . ".png";
		$jsondoc 		= $pdfdoc . ".js";
		$messages 		= "";

		$swfFilePath 	= $configManager->getConfig('path.swf') . $swfdoc;
		$pdfFilePath 	= $configManager->getConfig('path.pdf') . $pdfdoc;
		$pngFilePath 	= $configManager->getConfig('path.swf') . $pngdoc;
		$jsonFilePath 	= $configManager->getConfig('path.swf') . $jsondoc;
		$validatedConfig = true;

		session_start();

		if(!is_dir($configManager->getConfig('path.swf'))){
			$messages = "[Cannot find SWF output directory, please check your configuration file]";
			$validatedConfig = false;
		}

		if(!is_dir($configManager->getConfig('path.pdf'))){
			$messages = "[Cannot find PDF output directory, please check your configuration file]";
			$validatedConfig = false;
		}

		if(!$validatedConfig){
			echo "[Cannot read directories set up in configuration file, please check your configuration.]";
		}else if(	!validPdfParams($pdfFilePath,$pdfdoc,$page) /*|| !validSwfParams($swfFilePath,$swfdoc,$page) */){
			echo "[Incorrect file specified, please check your path]";
		}else{
			if($format == "swf" || $format == "png"){

				// converting pdf files to swf format
				if(!file_exists($swfFilePath)){
					$pdfconv=new pdf2swf();
					$messages=$pdfconv->convert($pdfdoc,$page);
				}

				// rendering swf files to png images
				if($format == "png"){
					if(validSwfParams($swfFilePath,$swfdoc,$page)){
						if(!file_exists($pngFilePath)){
							$pngconv=new swfrender();
							$pngconv->renderPage($pdfdoc,$swfdoc,$page);
						}

						if($configManager->getConfig('allowcache')){
							setCacheHeaders();
						}

						if(!$configManager->getConfig('allowcache') || ($configManager->getConfig('allowcache') && endOrRespond())){
							header('Content-Type: image/png');
							echo file_get_contents($pngFilePath);
						}
					}else{
						if(strlen($messages)==0 || $messages == "[OK]")
							$messages = "[Incorrect file specified, please check your path]";
					}
				}

				// rendering pdf files to the browser, split pages if nessecary
				if($format == "pdf"){

				}

				// writing files to output
				if(file_exists($swfFilePath)){
					if($format == "swf"){

						if($configManager->getConfig('allowcache')){
							setCacheHeaders();
						}

						if(!$configManager->getConfig('allowcache') || ($configManager->getConfig('allowcache') && endOrRespond())){
							header('Content-type: application/x-shockwave-flash');
							header('Accept-Ranges: bytes');
							header('Content-Length: ' . filesize($swfFilePath));
							echo file_get_contents($swfFilePath);
						}
					}
				}else{
					if(strlen($messages)==0)
						$messages = "[Cannot find SWF file. Please check your PHP configuration]";
				}
			}

			// for exporting pdf to json format
			if($format == "json"){
				if(!file_exists($jsonFilePath)){
					$jsonconv = new pdf2json();
					$messages=$jsonconv->convert($pdfdoc,$jsondoc,$page);
				}

				if(file_exists($jsonFilePath)){
					if($configManager->getConfig('allowcache')){
							setCacheHeaders();
					}

					if(!$configManager->getConfig('allowcache') || ($configManager->getConfig('allowcache') && endOrRespond())){
						header('Content-Type: text/javascript');
						echo file_get_contents($jsonFilePath);
					}
				}else{
					if(strlen($messages)==0)
						$messages = "[Cannot find JSON file. Please check your PHP configuration]";
				}
			}

			// write any output messages
			if(strlen($messages)>0 && $messages != "[OK]"){
				echo "Error:" . substr($messages,1,strlen($messages)-2);
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
		if (S3::putBucket($this->_bucket, S3::ACL_PRIVATE, 'ap-southeast-1')) {
			return TRUE;
		}
		return FALSE;
	}

	/** Old functions for Flexpaper flash **/
	/*
	case 'application/pdf':

	$this->convert_to_swf($docs_details['a_docs_ver_filename']);
	$s3object = $this->_temp_dir.$docs_details['a_docs_ver_filename'].'.swf';
	break;

	function convert_to_swf($filename) {
		require_once('application/libraries/pdf2swf/common.php');
		require_once("application/libraries/pdf2swf/pdf2swf_php5.php");
		$page = '';
		$configManager = new Config();
		$swfFilePath = $configManager->getConfig('path.swf') . $filename  . $page. ".swf";
		$pdfFilePath = $configManager->getConfig('path.pdf') . $filename;

		if(	!validPdfParams($pdfFilePath,$filename,$page) )
			echo "[Incorrect file specified]";
		else{
			$pdfconv=new pdf2swf();
			$output=$pdfconv->convert($filename,$page);

			if(rtrim($output) === "[Converted]"){

			}else {
				echo $output; //error messages etc
			}
		}
	}

	function pdfPreview() {
		require_once('application/libraries/pdf2swf/common.php');
		require_once("application/libraries/pdf2swf/pdf2swf_php5.php");

			$doc=$_GET["doc"];
			$page = "";
			if(isset($_GET["page"]))
				$page = $_GET["page"];

			$pos = strpos($doc, "/");
			$configManager = new Config();
			$swfFilePath = $configManager->getConfig('path.swf') . $doc  . $page. ".swf";
			$pdfFilePath = $configManager->getConfig('path.pdf') . $doc;

			if(	!validPdfParams($pdfFilePath,$doc,$page) )
				echo "[Incorrect file specified]";
			else{
				$pdfconv=new pdf2swf();
				$output=$pdfconv->convert($doc,$page);
				if(rtrim($output) === "[Converted]"){

					if($configManager->getConfig('allowcache')){
						setCacheHeaders();
					}

					if(!$configManager->getConfig('allowcache') || ($configManager->getConfig('allowcache') && endOrRespond())){
						header('Content-type: application/x-shockwave-flash');
						header('Accept-Ranges: bytes');
						header('Content-Length: ' . filesize($swfFilePath));

						echo file_get_contents($swfFilePath);
					}
				}else
					echo $output; //error messages etc
			}
	}*/

	/*
	function permission_form() {

	}

	// Called when versioning is enabled.
	function replace_object() {

	}


	function delete_directory() {

	}

	function upload_form() {

	}*/
	/*
	function get_dirpath($id) {
		$_dirpath = array();
		$_dirpath_str = '';
		while ($i = $this->DocsM->get_dir_parent_id($id)) {
			$_dirpath[] = $i[0];
			$id = $i[0]['a_docs_parentid'];
		};
		if ( ! empty($_dirpath)) {
			$_dirpath = array_reverse($_dirpath);
			$_dirpath_html = '<ul class="apphead_title">';
			foreach ($_dirpath as $path) {
				if ($path['a_docs_displayname'] !== 'root') $_dirpath_str .= '/'. $path['a_docs_dir_name'];
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
	}*/

	/** Test functions **/
	function test() {
		print $this->_bucket;
		$i = S3::deleteBucket($this->_bucket);
		var_dump($i);
		die();
	}

	function print_tree($tree, $html) {
		$html .= '<ul>';
		foreach ($tree as $folder) {
			$html .= '<li>'.$folder['a_docs_displayname'].'</li>';
			if (isset($folder['child'])) {
				$html .= $this->print_tree($folder['child'], $html);
			}
		}
		$html .= '</ul>';
		return $html;
	}
}