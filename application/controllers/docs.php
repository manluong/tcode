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

	function permission_form() {

	}

	function create_folder() {
		if ($this->input->get('name')) {
			$values['displayname'] = $this->input->get('name');
			//dirpath finds the full directory patht to update
			$_dirpath = $this->DocsM->get_dirpath($this->url['id_plain']);
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

	function delete_directory() {

	}

	function upload_form() {

	}

	function put_object() {
		$targetDir = '/Applications/XAMPP/htdocs/tcode/tmp/';
		$dirpath = $this->DocsM->get_dirpath($this->url['id_plain']);
		if ($dirpath['a_docs_dir_dirpath'] === '/') $dirpath['a_docs_dir_dirpath'] = ''; //if its root remove /

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
				// Open temp file
				log_message('debug', 'Upload: '.$dirpath['a_docs_dir_dirpath'].'/'.$_FILES['file']['name']);
				if (S3::putObject(S3::inputFile($_FILES['file']['tmp_name']), 's3subscribers', $dirpath['a_docs_dir_dirpath'].'/'.$_FILES['file']['name'], S3::ACL_PRIVATE)) {
					$this->_upload_status = TRUE;
				} else {
					$this->_upload_status = FALSE;
				}
			}
		}
		// ----- end S3 code ---- /
		//$this->_upload_status
		if ($this->_upload_status) {
			$values['parentid'] = $this->url['id_plain'];
			$values['filename'] = $_FILES['file']['name'];
			$values['uploadvia'] = 'web';
			$values['filesize'] = $_FILES['file']['size'];
			$values['mime'] = $_FILES['file']['type'];
			log_message('debug','File type: '.$_FILES['file']['type']);
			$this->DocsM->update_docs($values);
		} else {
			log_message('debug',"Upload failed.\n ".'Content-type:'.$contentType."\n");
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
		$this->output->set_content_type('application/json');
		if ($this->input->get('id')) {
			$_this_object_details = $this->DocsM->get_docs_detail($this->input->get('id'));
			if ( ! empty($_this_object_details)) {
				$_this_object_path = $this->DocsM->get_dirpath($_this_object_details['a_docs_dir_docs_id']);
				if ($_this_object_path['a_docs_dir_dirpath'] === '/') $_this_object_path['a_docs_dir_dirpath'] = '';
				$uri = $_this_object_path['a_docs_dir_dirpath'].'/'.$_this_object_details['a_docs_ver_filename'];
				$_bucket = 's3subscribers';
				if (S3::deleteObject('s3subscribers',$uri)) {
					$this->DocsM->delete_docs($_this_object_details['a_docs_id']);
					return $this->output->set_output(json_encode(array('message', 'File deleted')));
				}
			}
		}
		return $this->output->set_output(json_encode(array('message' => 'error')));
	}

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
		switch ($this->_views_data['docs_detail']['a_docs_ver_mime']) {
			case 'image/gif':
			case 'image/jpeg':
			case 'image/png':
				$this->_views_data['s3_object'] = $this->get_object_url('s3subscribers', $this->_views_data['docs_detail']['a_docs_dir_dirpath'].$this->_views_data['docs_detail']['a_docs_ver_filename'], '3600');
				$this->_views_data['s3_object'] = '<img src="'.$this->_views_data['s3_object'].'">';
				break;
			case 'application/pdf':
				$this->_views_data['s3_object'] = $this->get_object('s3subscribers', $this->_views_data['docs_detail']['a_docs_dir_dirpath'].$this->_views_data['docs_detail']['a_docs_ver_filename']);
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

	function save_to_file(&$binary, $filename){
		$fp = fopen($this->_temp_dir.$filename, 'wb');
		$r = fwrite($fp, $binary);
		fclose($fp);
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

	function display_file_functions () {
		$data = array();
		$data['html'] = $this->load->view('/'.get_template().'/docs/docs_view_file_functions.php','',TRUE);
		$data['isoutput'] = 1;
		$data['isdiv'] = 1;
		return $data;
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

	function test() {
		$i = $this->DocsM->get_docs_detail($this->url['id_plain']);

	}

	function testView() {
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
}