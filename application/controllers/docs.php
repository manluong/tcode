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
		log_message('debug','Content-type:'.$contentType."\n");
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
		/*
		$i = $this->get_dirpath(1);
		print_r($i);
		$img =  S3::getAuthenticatedURL('s3subscribers', 'gravatar-14022.png', '3600');
		echo '<img src="'.$img.'">';
		*/
		/*
		$p = '/Applications/XAMPP/xamppfiles/htdocs/tcode/tmp/unisim.pdf';
		exec('pdf2swf '.$this->_temp_dir.$filename.' -o '.$this->_temp_dir.$filename.'.swf -T 9 -f', $arr, $r);
		var_dump($arr);
		var_dump($r);*/
		require_once('application/libraries/pdf2swf/common.php');
		require_once('application/libraries/pdf2swf/config.php');
		$configManager = new Config();
		// Setting current document from parameter or defaulting to 'Paper.pdf'

		$doc = "Paper.pdf";
		if(isset($_GET["doc"]))
		$doc = $_GET["doc"];

		$pdfFilePath = $configManager->getConfig('path.pdf') . $doc;
		$swfFilePath = $configManager->getConfig('path.swf');

		$html = '<div style="position:absolute;left:10px;top:60px;">
	        <p id="viewerPlaceHolder" style="width:660px;height:553px;display:block">Document loading..</p>';
		if(validPdfParams($pdfFilePath,$doc,null) && is_dir($swfFilePath) ){
			$html .= '<script type="text/javascript">'
				 . 'var doc = "'.$doc.'";'
				 ."var fp = new FlexPaperViewer(
						 '/resources/template/default_web/lib/flexpaperViewer/FlexPaperViewer',
						 'viewerPlaceHolder', { config : {
						 SwfFile : escape('/docs/testView?doc='+doc),
						 Scale : 0.6,
						 ZoomTransition : 'easeOut',
						 ZoomTime : 0.5,
						 ZoomInterval : 0.2,
						 FitPageOnLoad : true,
						 FitWidthOnLoad : false,
						 FullScreenAsMaxWindow : false,
						 ProgressiveLoading : false,
						 MinZoomSize : 0.2,
						 MaxZoomSize : 5,
						 SearchMatchAll : false,
						 InitViewMode : 'Portrait',

						 ViewModeToolsVisible : true,
						 ZoomToolsVisible : true,
						 NavToolsVisible : true,
						 CursorToolsVisible : true,
						 SearchToolsVisible : true,

  						 localeChain: 'en_US'
						 }});"
				."function onDocumentLoadedError(errMessage){
					$('#viewerPlaceHolder').html(\"Error displaying document. Make sure the conversion tool is installed and that correct user permissions are applied to the SWF Path directory".$configManager->getDocUrl()."\");}</script>";
		} else {
			$html = "<script type=\"text/javascript\">
				$('#viewerPlaceHolder').html('Cannot read pdf file path, please check your configuration (in php/lib/config/)');
			</script>";
		}
		$html .="</div>";
		$data['html'] = $html;
		$data['outputdiv'] = 1;
		$data['isdiv'] = TRUE;
		$data['div']['title'] = 'Documents';
		$data['div']['element_name'] = 'loginwin';
		$data['div']['element_id'] = 'divlogin';
		$this->data[] = $data;
		$this->LayoutM->load_format();
		$this->output();
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