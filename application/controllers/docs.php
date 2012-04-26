<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Docs extends MY_Controller {
	private $_views_data = array();
	private $_upload_status = FALSE;

	function __construct() {
		parent::__construct();
		$this->load->library('FileL');
		$this->load->library('CommentsL');

		$this->load->model('DocsM');

		$this->_views_data['folder_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/folder-small-horizontal.png">';
		$this->_views_data['docs_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/document-small.png">';
		$this->_views_data['tick_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/tick-circle.png" id="tick-icon">';
		$this->_views_data['cross_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/cross-circle.png" id="cross-icon">';
		$this->_views_data['loader_icon'] = '<img src="/resources/template/'.get_template().'/images/ajax-loader.gif" style="display:none;" id="loader-icon">';
		$this->_views_data['error_icon'] = '<img src="/resources/template/'.get_template().'/images/icons/16/exclamation-red.png" style="display:none;" id="error-icon">';
	}

	//TODO: might be deleted
	function get_object($uri) {
		$object = S3::getObject($this->eightforce_config['s3_bucket'], $uri, FALSE);
		return $object;
	}

	/*
	function index() {
		redirect('/docs/view/0/list-view');
	}*/

	function file() {
		$vars['url'] = $this->url;
		$vars['page'] = get_template().'/docs/docs_file_html';

		$this->data['content'] = $this->load->view(get_template().'/docs/docs_view',$vars,TRUE);

		$this->_do_output();
	}

	// Called by view in tbuilder
	function index() {
		/*
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
*/


		$vars['url'] = $this->url;
		$vars['page'] = get_template().'/docs/docs_view_html';
		$this->data['content'] = $this->load->view(get_template().'/docs/docs_view', $vars, TRUE);

		$this->_do_output();
	}

	function view() {
		$vars['url'] = $this->url;
		$vars['page'] = get_template().'/docs/docs_view_html';

		$this->data['content'] = $this->load->view(get_template().'/docs/docs_view', $vars, TRUE);

		$this->_do_output();
	}

	/**
	 * Returns raw json values of directory contents
	 */
	//TODO: does not seem to be in use, possible to delete
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




	function pdfPreview() {
		require_once('resources/addon/docs/AdaptiveUI1.3.5/common.php');
		require_once('resources/addon/docs/AdaptiveUI1.3.5/pdf2json_php5.php');
		require_once('resources/addon/docs/AdaptiveUI1.3.5/pdf2swf_php5.php');
		require_once('resources/addon/docs/AdaptiveUI1.3.5/swfrender_php5.php');

		$doc 	= $_GET["doc"];
		$pdfdoc 	= $doc;
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



	// updated by erik
	function ajax_create_folder() {
		$folder = $this->input->post('name');
		$parent_folder = $this->input->post('parent_folder');

		$result = $this->DocsM->create_dir($folder, $parent_folder);

		$this->RespM->set_details($result)
				->set_success(($result !== FALSE))
				->output_json();
	}

	function ajax_delete_file() {
		$result = $this->filel->delete($this->url['id_plain']);

		$this->RespM->set_success($result)
				->output_json();
	}

		/**
	 * Returns json details of document
	 */
	function ajax_get_file_details() {
		$docs_details = $this->DocsM->get_detail($this->url['id_plain']);

		if (empty($docs_details)) {
			$this->RespM->set_success(FALSE)
					->set_message('No details found')
					->output_json();
			return;
		}

		/*
		 * TODO: This block does not seem to server any purpose, delete after confirmation
		switch ($docs_details['mime']) {
			case 'image/gif':
			case 'image/jpeg':
			case 'image/png':
				$s3object = html_entity_decode($this->filel->get_url($docs_details['hash']));
				//$s3object = '<img src="'.$this->_views_data['s3_object'].'">';
				break;
			case 'application/pdf':
				$s3object = $this->get_object($this->eightforce_config['s3_bucket'], $this->format_dirpath($docs_details['a_docs_dir_dirpath'],$docs_details['a_docs_ver_filename']));
				$this->save_to_file($s3object->body, $docs_details['a_docs_id'].'.pdf');
				break;
		}
		*/

		$data = array(
			'docs_details' => $docs_details,
			'versions' => $this->DocsM->get_all_versions($docs_details['id'])
		);

		$this->RespM->set_success(TRUE)
				->set_details($data)
				->output_json();
	}

	function ajax_move_file() {
		$new_dir_id = $this->input->post('folder_id');
		$docs_id = $this->url['id_plain'];

		if ($new_dir_id === FALSE) {
			$this->RespM->set_message('Invalid Folder ID')
				->set_success(FALSE)
				->output_json();
		}

		$result = $this->DocsM->move_file($docs_id, $new_dir_id);

		$this->RespM->set_success($result)
			->output_json();
	}

	//upload a new file
	function ajax_upload() {
		$success = TRUE;
		$message = '';

		$dir_id = $this->url['id_plain'];
		$file_info = $this->filel->save('file', $dir_id);

		if ($file_info === FALSE) {
			$success = FALSE;
			$message = $this->filel->error_messages;
		}

		$this->RespM->set_success($success)
			->set_message($message)
			->set_details($file_info)
			->output_json();
	}

	function ajax_overwrite() {
		$docs_id = $this->url['id_plain'];
		$this->filel->save('file', 0, $docs_id);

		//TODO: success is defaulted to TRUE, must check file_info
		$this->RespM->set_success(TRUE)
			->set_details($file_info)
			->output_json();
	}

	function ajax_update_docs_display_name() {
		// /docs/update_docs_title/:docs_id/title
		$display_name = $this->input->post('title');
		$docs_id = $this->url['id_plain'];

		if ($display_name === FALSE) {
			$this->RespM->set_success(FALSE)
					->set_message('Please enter a document title')
					->output_json();
			return NULL;
		}

		if ($docs_id === FALSE) {
			$this->RespM->set_success(FALSE)
					->set_message('Invalid document ID')
					->output_json();
			return NULL;
		}


		$result = $this->DocsM->update_display_name($docs_id, $display_name);

		$this->RespM->set_success($result)
				->output_json();
	}

	function ajax_delete_version() {
		$hash = $this->input->post('hash');
		$result = $this->filel->delete($hash);

		$this->RespM->set_success($result)
				->output_json();
	}

	/**
	 * Returns json contents of directory for datatable.
	 */
	function ajax_get_dir_contents() {
		$rows = array();

		if ($this->url['id_plain'] !== '0') {
			$parent_id = $this->DocsM->get_dir_parent_id($this->url['id_plain']);
			$parent = anchor('/docs/view/'.encode_id($parent_id).'/list-view', 'prev', 'class="ajax"');
			array_push($rows, array($parent, '--', '--'));
		}

		$sub_folders = $this->DocsM->get_subdir($this->url['id_plain']);
		foreach ($sub_folders as $subfolder) {
			$name = anchor('/docs/view/'.encode_id($subfolder['id']).'/list-view', $subfolder['name'], 'class="ajax"');
			array_push($rows, array($name, '--', '--'));
		}

		$docs = $this->DocsM->get_dir_contents($this->url['id_plain']);
		foreach ($docs as $doc) {
			$link = anchor('/docs/file/'.encode_id($doc['id']).'/v', $doc['display_name'], 'class="ajax"');
			array_push($rows, array($link, byte_size($doc['file_size']), $doc['created_stamp']));
		}

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('aaData'=>$rows)));
	}

	function ajax_dir_tree() {
		$tree = $this->DocsM->get_subdir(0, TRUE); //gets tree from root dir

		$this->output->set_content_type('application/json')
			->set_output(json_encode($tree));
	}

	function download_file() {
		$file = $this->filel->read($this->url['id_plain']);

		if ($file === FALSE) return NULL;

		$this->output->set_content_type($file['mime']);
		$this->output->set_header('Content-Disposition: attachment; filename="'.$file['filename'].'"');
		$this->output->set_output($file['content']);
	}



}