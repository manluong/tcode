<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Docs extends MY_Controller {
	private $_views_data, $_dir_path = array();
	private $_upload_status = FALSE;
	private $_temp_dir = '';

	function __construct() {
		parent::__construct();
		$this->load->library('S3');
		$this->load->model('DocsM');
		$this->_temp_dir = $_SERVER['DOCUMENT_ROOT'].'/tmp/';
		$this->_bucket = 's3subscribers';

		$this->_views_data['folder_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/folder-small-horizontal.png">';
		$this->_views_data['docs_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/document-small.png">';
		$this->_views_data['tick_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/tick-circle.png" id="tick-icon">';
		$this->_views_data['cross_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/cross-circle.png" id="cross-icon">';
		$this->_views_data['loader_icon'] = '<img src="/resources/template/'.get_template().'/images/ajax-loader.gif" style="display:none;" id="loader-icon">';
		$this->_views_data['error_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/exclamation-red.png" style="display:none;" id="error-icon">';
	}

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

	function create_folder() {
		if ($this->input->get('name')) {
			$values['displayname'] = $this->input->get('name');
			//dirpath finds the full directory patht to update
			$_dirpath = $this->DocsM->get_dirpath_dir($this->url['id_plain']);
			$values['dirpath'] = $_dirpath['a_docs_dir_dirpath'].$this->input->get('name');
			$values['parentid'] = $this->url['id_plain'];
			$insert_id = $this->DocsM->update_a_docs_dir_directory($values);
			$this->output->set_content_type('application/json');
			if ($insert_id) {
				return $this->output->set_output(json_encode(array('id'=>$insert_id,
					'name'=>$this->input->get('name'),
					'folder_icon'=>$this->_views_data['folder_icon'])));
			}
		}
		return $this->output->set_output(json_encode(array('error'=>'Error processing request'.$insert_id)));
	}

	// Removes files from server. No way to retrieve.
	function delete_docs() {
		$this->output->set_content_type('application/json');
		if ($this->input->get('id')) { //docs_id
			$_obj_details = $this->DocsM->get_docs_detail($this->input->get('id'));
			if (empty($_obj_details)) {
				return $this->output->set_header('HTTP/1.1 400');
			}
			$versions = $this->DocsM->get_all_versions($this->input->get('id'));
			foreach ($versions as $version) {
				if (S3::deleteObject($this->_bucket, $this->format_dirpath($_obj_details['a_docs_dir_dirpath'], $version['a_docs_ver_filename']))) {
					log_message('debug', 'Deleted '. $this->format_dirpath($_obj_details['a_docs_dir_dirpath'], $version['a_docs_ver_filename']));
				}
			}
			$this->DocsM->delete_docs($_obj_details['a_docs_id']);
		}
		$this->output->set_output(json_encode(array('message' => 'error'))); exit();
	}
	/*
	function delete_single() {
		$this->output->set_content_type('application/json');
		if ($this->input->get('id')) { //docs_id
			$_obj_details = $this->DocsM->get_docs_detail($this->input->get('id'));
			if ( ! empty($_this_object_details)) {
				$_obj_path = $this->DocsM->get_dirpath($_obj_details['a_docs_dir_docs_id']);
				$uri = $this->format_dirpath($_obj_path['a_docs_dir_dirpath'], $_obj_details['a_docs_ver_filename']);
				if (S3::deleteObject($this->_bucket,$uri)) {
					$this->DocsM->delete_docs($_this_object_details['a_docs_id']);
					$this->output->set_output(json_encode(array('message', 'File deleted'))); exit();
				}
			}
		}
		$this->output->set_output(json_encode(array('message' => 'error'))); exit();
	}*/

	function display_file_functions () {
		$this->_views_data['root_dir'] = $this->DocsM->get_root_dir();
		$this->_views_data['tree'] = $this->get_tree($this->_views_data['root_dir']['a_docs_id']);
		$data = array();
		$data['html'] = $this->load->view('/'.get_template().'/docs/docs_view_file_functions.php',$this->_views_data,TRUE);
		$data['isoutput'] = 1;
		$data['isdiv'] = 1;
		return $data;
	}

	function download_file() {
		$i = $this->DocsM->get_docs_ver_detail($this->input->get('id'));
		if ( ! empty($i)) {
			$uri = $this->format_dirpath($i['a_docs_dir_dirpath'], $i['a_docs_ver_filename']);
			$_filecontent = $this->get_object($this->_bucket, $uri);
			print_r($_filecontent);
			$this->output->set_content_type($i['a_docs_ver_mime']);
			$this->output->set_header('Content-Disposition: attachment; filename="'.$i['a_docs_ver_filename'].'"');
			return $this->output->set_output($_filecontent->body);
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

	function get_docs() {
		$docs = $this->DocsM->get_docs($this->url['id_plain']);
		if ( ! empty($docs)) {
			$this->output->set_content_type('application/json');
			return $this->output->set_output(json_encode($docs));
		} else {
			return $this->output->set_output(json_encode(array('message'=>'Your folder is empty.')));
		}
	}

	function get_object($bucket, $uri) {
		$object = S3::getObject($bucket, $uri, FALSE);
		return $object;
	}

	function get_object_url($bucket, $uri, $lifetime) {
		return S3::getAuthenticatedURL($bucket, $uri, $lifetime);
	}

	function get_tree($id) {
		$i = $this->DocsM->get_sub_folders($id);
		foreach($i as &$sub_folder) {
			$j = $this->get_tree($sub_folder['a_docs_id']);
			if ( ! empty($j)) $sub_folder['child'] = $j;
		}
		return $i;
	}

	function index() {
		redirect('/docs/view/0/list-view');
	}

	function move_file() {
		if ($this->input->get('folder_id') && $this->input->get('docs_id')) {
			$versions = $this->DocsM->get_all_versions($this->input->get('docs_id'));
			$new_dir = $this->DocsM->get_dirpath_dir($this->input->get('folder_id'));
			foreach ($versions as $version) {
				if (S3::copyObject($this->_bucket, $this->format_dirpath($version['a_docs_dir_dirpath'],$version['a_docs_ver_filename']), $this->_bucket, $this->format_dirpath($new_dir['a_docs_dir_dirpath'], $version['a_docs_ver_filename']), S3::ACL_PRIVATE)) {
					log_message('debug', 'Copied file '.$this->format_dirpath($version['a_docs_dir_dirpath'],$version['a_docs_ver_filename'].' to '.$this->format_dirpath($new_dir['a_docs_dir_dirpath'], $version['a_docs_ver_filename'])));
					if (S3::deleteObject($this->_bucket, $this->format_dirpath($version['a_docs_dir_dirpath'],$version['a_docs_ver_filename']))) {
						log_message('debug', 'Removed file '. $this->format_dirpath($version['a_docs_dir_dirpath'],$version['a_docs_ver_filename']));
					}
				}
				$this->DocsM->update_docs_location($this->input->get('folder_id'), $this->input->get('docs_id'));
			}
		} else {
			return $this->output->set_header('HTTP/1.1 500');
		}
	}

	function preview() {
		if ($this->uri->segment(5)) {
			$this->_views_data['docs_detail'] = $this->DocsM->get_docs_ver_detail($this->uri->segment(5,0));
		} else {
			$this->_views_data['docs_detail'] = $this->DocsM->get_docs_detail($this->url['id_plain']);
		}

		$this->_views_data['past_versions'] = $this->DocsM->get_all_versions($this->url['id_plain']);
		switch ($this->_views_data['docs_detail']['a_docs_ver_mime']) {
			case 'image/gif':
			case 'image/jpeg':
			case 'image/png':
				$this->_views_data['s3_object'] = $this->get_object_url('s3subscribers',$this->format_dirpath( $this->_views_data['docs_detail']['a_docs_dir_dirpath'],$this->_views_data['docs_detail']['a_docs_ver_filename']), '3600');
				$this->_views_data['s3_object'] = '<img src="'.$this->_views_data['s3_object'].'">';
				break;
			case 'application/pdf':
				$this->_views_data['s3_object'] = $this->get_object($this->_bucket, $this->format_dirpath($this->_views_data['docs_detail']['a_docs_dir_dirpath'],$this->_views_data['docs_detail']['a_docs_ver_filename']));
				$this->save_to_file($this->_views_data['s3_object']->body, $this->_views_data['docs_detail']['a_docs_ver_filename']);
				$this->convert_to_swf($this->_views_data['docs_detail']['a_docs_ver_filename']);
				$this->_view_data['s3_object'] = $this->_temp_dir.$this->_views_data['docs_detail']['a_docs_ver_filename'].'.swf';

				$data = array();
				$data['html'] = $this->load->view('/'.get_template().'/docs/docs_view_preview_pdf.php',$this->_views_data, TRUE);
				$data['isoutput'] = 1;
				$data['isdiv'] = 1;
				return $data;
				break;
		}

		$data = array();

		$data['html'] = $this->load->view('/'.get_template().'/docs/docs_view_preview.php',$this->_views_data, TRUE);
		$data['isoutput'] = 1;
		$data['isdiv'] = 1;
		return $data;
	}

	function check_version_settings() {
		// Check docs_dir and return, if not found default to docs_settings table
		$_docs_dir_ver = $this->DocsM->get_docs_dir_ver($this->url['id_plain']);
		if ( ! empty($_docs_dir_ver)) {
			return $_docs_dir_ver['a_docs_dir_nover'];
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
		$_ver_setting = $this->check_version_settings();
		$_filename = $this->check_filename($_FILES['file']['name']); // Get new filename if filename conflict
		if ($_ver_setting) {
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
				'a_docs_ver_filename' => $filename,
				'a_docs_ver_stamp'=> get_current_stamp(),
				'a_docs_ver_filesize' => $_FILES['file']['size'],
				'a_docs_ver_cardid' => $this->UserM->info['cardid'],
				'a_docs_ver_mime'=> $_FILES['file']['type'],
			);
			$this->rename_old_ver($dirpath, $this->url['id_plain']);
			$this->DocsM->insert_docs_ver($values);
			$this->output->set_output(array('message'=>'File uploaded'));
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
			log_message('debug', 'Copied file to '. $this->format_dirpath($dirpath['a_docs_dir_dirpath'], $values['a_docs_ver_filename']));
			if (S3::deleteObject($this->_bucket, $this->format_dirpath($dirpath['a_docs_dir_dirpath'],$ver_detail['a_docs_ver_filename']))) {
				log_message('debug', 'Removed old renamed file: '.$this->format_dirpath($dirpath['a_docs_dir_dirpath'],$ver_detail['a_docs_ver_filename']));
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
		} else {
			log_message('debug', 'Error uploading File: '. $filename);exit();
		}
		$this->remove_old_file($ver_id, $_dirpath);
		$this->DocsM->update_docs_ver($values);exit();
	}

	function remove_old_file ($ver_id, $dirpath) {
		$filename = $this->DocsM->get_file_name($ver_id);
		$uri = $this->format_dirpath($dirpath['a_docs_dir_dirpath'], $filename['a_docs_ver_filename']);
		if(S3::deleteObject($this->_bucket, $uri)) {
			log_message('debug', 'Removed old File:'.$uri);
		} else {
			log_message('debug', 'Error old File:'.$filename['a_docs_ver_filename']);
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
			$this->DocsM->insert_docs($values);exit();
		}
		$this->output->set_header('HTTP/1.1 500');
		log_message('debug',"Upload failed.\n ".'Content-type:'.$contentType."\n");exit();
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
				if (S3::putObject(S3::inputFile($_FILES['file']['tmp_name']), 's3subscribers',
					$this->format_dirpath($dirpath['a_docs_dir_dirpath'], $filename), S3::ACL_PRIVATE)) {
					$this->_upload_status = TRUE;
					log_message('debug', 'Upload: '.$this->format_dirpath($dirpath['a_docs_dir_dirpath'], $filename));
				} else {
					$this->_upload_status = FALSE;
				}
			}
		}
		// ----- end S3 code ---- /
		return;
	}

	function save_to_file(&$binary, $filename){
		$fp = fopen($this->_temp_dir.$filename, 'wb');
		$r = fwrite($fp, $binary);
		fclose($fp);
	}

	function view() {
		if ($this->url['subaction'] === 'folder-view') {

		} else {
			$folder_exists = $this->DocsM->does_folder_exists($this->url['id_plain']);
			if ( ! empty ($folder_exists)) {
				$this->_views_data['sub_folders'] = $this->DocsM->get_sub_folders($this->url['id_plain']);
				$i = $this->DocsM->get_parent_id($this->url['id_plain']);
				if ( ! empty($i)) $this->_views_data['parent'] = $i;
				$this->_views_data['docs'] = $this->DocsM->get_docs($this->url['id_plain']);
			} else {
				// Does user has a root directory
				$root_dir = $this->DocsM->get_root_dir();
				if ( ! empty($root_dir)) {
					redirect('/docs/view/'.$root_dir['a_docs_id'].'/list-view');
				} else {
					// Create root folder
					$values['parentid'] = 0;
					$values['displayname'] = 'root';
					$values['dirpath'] = '/';

					$id = $this->DocsM->update_a_docs_dir_directory($values);
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

	function update_docs_title() {
		if ($this->input->post('title') && $this->input->post('id')) {
			if ($this->DocsM->update_docs_display_name($this->input->post('title'), $this->input->post('id'))) {
				$this->output->set_content_type('application/json');
			echo json_encode(array('message' => 'Title changed'));exit();
			}
		}
		$this->output->set_status_header('400');exit();
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
	}

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
		$i = $this->DocsM->get_all_versions($this->input->get('id'));
		print_r($i);die();
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