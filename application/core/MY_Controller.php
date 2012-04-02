<?php

class MY_Controller extends CI_Controller {
	var $domain = '';

	var $url = array(
		'app' => '',
		'app_id' => '',

		'action' => '',
		'actiongp' => '',

		'id_plain' => 0,
		'id_encrypted' => 0,
		'id' => 0,	//original ID passed in by request

		'subaction' => '',
	);
	var $re_url = array(
		'app' => '',
		'action' => '',
		'id' => 0,
		'subaction' => '',
	);
	var $has_return = FALSE;

	var $data = array();
	var $layout = array(
		'name' => '',
		'formtype' => '',
		'listtype' => '',

		'type' => '',
		'logo' => '',
		'menu' => '',
		'footer' => '',
		'boxformat' => '',

		'format' => '',
		'breadcrumnb' => '',
		'content' => '',
		'icons' => '',

		'menu_array' => '',
		'appmenu' => '0',
		'appmenu_gp' => '',
	);

	var $system_messages = array();

	// Needed for logM
	var $log_data = array(
		'start_time' => 0,
		'insert_id' => 0,
		'log_type' => array(),
		'saveid' => 0,
	);

	var $model = '';

	var $is_ajax = FALSE;
	var $is_mobile_app = FALSE;

	var $eightforce_config = array();

	var $debug = array();

	//valid subactions
	var $subactions = array('a','v','e','d','l','s','as','es','ds');

	function __construct() {
		parent::__construct();

		if ($this->input->is_ajax_request()) $this->is_ajax = TRUE;

		$this->load->config('eightforce', TRUE);
		$this->eightforce_config = $this->config->item('eightforce');

		$this->setup_url();
		$this->setup_db();

		$this->check_mobile_app();

		$this->load->library('session');

		$this->load->model('UserM');
		$this->load->model('ACLM');
		$this->load->model('App_generalM');
		$this->load->model('LayoutM');
		$this->load->model('AppM');
		$this->load->model('LogM');
		$this->load->model('LicenseM');
		$this->load->model('RespM');

		$this->url['app_id'] = $this->AppM->get_id($this->url['app']);
		$this->url['actiongp'] = $this->AppM->get_group($this->url['app'], $this->url['action']);

		$this->UserM->setup();
		$this->setup_language();
		$this->LogM->start_log();
		$this->AppM->setup();
		$this->LayoutM->setup();
		$this->LicenseM->setup();

		if ($this->AppM->must_disable_plain_id()) $this->ACLM->check_id_encryption();
		$this->ACLM->check_app_access();


		//if (!$this->is_ajax) $this->output->enable_profiler(true);
	}

	//remap every URI call
	function _remap($action, $params = array()) {

		if (APP_ROLE == 'TSUB') {
			if ($this->LicenseM->has_restriction($this->url['app_id'], $this->url['actiongp'], 'access')) {
				$access = $this->LicenseM->get_restriction($this->url['app_id'], $this->url['actiongp'], 'access');
				if ($access == 0) die('Your license does not permit you to use this application.');
			}
		}

		//controller file always override settings in DB
		//if $this->url['action'] match a method in controller file, use it (including "index")
		//"index" method will override default set in database
		if (method_exists($this, $action)) {
			$this->debug['controller_mode'] = 'file';
			return call_user_func_array(array($this, $action), $params);
		}

		$this->debug['controller_mode'] = 'database';

		if ($this->url['action']=='index') {
			$this->AppM->load_default_actions();
			$this->url['action'] = $this->AppM->actions['core_apps_action_name'];
		}

		//if the action is not in DB, load a 404 view
		if (!$this->AppM->has_actions()) echo "404"; // erik, this function somehow doesn't work

		//run actions
		$this->run_action($params);

	}

	public function run_action($params) {
		$this->data = $this->app_load();
		$this->LayoutM->load_format();

		$this->output();
	}

	private function setup_url() {
		$this->url['app'] = $this->router->fetch_class();

		//if method is not pass in, action is set to "index" by CI
		$this->url['action'] = $this->router->fetch_method();

		$this->url['subaction'] = $this->uri->segment(4, '');
		if (!in_array($this->url['subaction'], $this->subactions)) $this->url['subaction'] = 'v';

		$this->url['id'] = $id = $this->uri->segment(3, 0);

		if (id_is_encrypted($id)) {
			$this->url['id_plain'] = decode_id($id);
			$this->url['id_encrypted'] = $id;
		} else {
			$this->url['id_plain'] = $id;
			$this->url['id_encrypted'] = encode_id($id);
		}

		$this->re_url['app'] = $this->input->get_post('re_app', TRUE);
		$this->re_url['action'] = $this->input->get_post('re_action', TRUE);
		$this->re_url['subaction'] = $this->input->get_post('re_subaction', TRUE);
		$this->re_url['id'] = $this->input->get_post('re_id', TRUE);

		if ($this->re_url['app'] !== FALSE && $this->re_url['action'] !== FALSE) $this->has_return = TRUE;

		if ($this->input->is_cli_request()) {
			$this->domain = 'my';
		} else {
			if (ENVIRONMENT != 'development') {
				$domain = explode('.', $_SERVER['SERVER_NAME']);
				//if ($domain[1]!=='8force' || $domain[2]!=='net') die('There is a problem with the domain name.');
				$this->domain = $domain[0];
			}
		}

		$this->debug['environment'] = ENVIRONMENT;

		//TBOSS - For internal use
		//TSUB - Tenant software
		if ($this->domain === 'my') {
			define('APP_ROLE', 'TBOSS');
			$this->debug['app_role'] = 'TBOSS';
		} else {
			define('APP_ROLE', 'TSUB');
			$this->debug['app_role'] = 'TSUB';
		}
	}

	private function setup_db() {
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

		$this->debug['database'] = $config['database'];
		$this->debug['database_username'] = $config['username'];

		$this->load->database($config);
	}



	private function setup_language() {
		$this->load->model('LangM');
		$this->lang->initialise($this->LangM->active);
		$this->lang->load($this->LangM->get_array('core', $this->lang->lang_use));
		$this->lang->load($this->LangM->get_array($this->url['app'], $this->lang->lang_use));
	}

	private function check_mobile_app() {
		if ($this->input->is_cli_request()) return NULL;

		//detect mobile app or not
		$agent = trim($_SERVER['HTTP_USER_AGENT']);
		$mobile_app_user_agents = array('8force-ios', '8force-and');
		foreach($mobile_app_user_agents AS $maua) {
			if (strpos($agent, $maua) !== FALSE) $this->is_mobile_app = TRUE;
		}

		//change session timeout based on whether the requesting party is a mobile app or not
		if ($this->is_mobile_app) {
			$this->session->sess_expiration = 60*60*24*365*2;
			$this->session->sess_expire_on_close = FALSE;
		}
	}


	private function app_load(){

		/*
		 * getthisid model
		 *
		 * use plain id only check
		 *
		 */


		//looping the elements, and switch the element_type

		$all_action_elements = $this->AppM->get_action_element($this->url['app'], $this->url['action']);

		if (!$all_action_elements) return array();

		$output = array();
		$count_output = 0;
		$mfunction = array();

		foreach ($all_action_elements as $this_element) {
			// if the previous mFunction is passed, continue
			// if $mfunction['stop'] is given, do not perform the next element
			if (isset($mfunction['stop'])) continue;

			//confirm we have the right $this_element to work with
			//if it's a ajax, load the target action and it's element values
			$this_element = $this->AppM->get_this_element($this_element, $mfunction, $this->AppM->actions);

			// if it's NOT ajax, pass to model to process, else pass the value to format [ajax] ajax
			if (!$this_element['ajax'] || $this->url['subaction'] == 'ss') {

				switch ($this_element['type']) {
					case 'dgroup':
						$this->load->model('element/Element_dgroup');
						$this->load->model('element/Element_button');
						$output[$count_output] = $this->Element_dgroup->core_element_dgroup($this_element);
						break;

					case 'statpanel':
						$this->load->model('element/Element_statpanel');
						$output[$count_output] = $this->Element_statpanel->get_statpanel($this_element['name']);
						break;

					case 'menu':
						$this->load->model('AppmenuM');
						$get_appmenu = $this->AppmenuM->get_appmenu($this_element['name']);
						$output[$count_output]['html'] = $this->load->view('/'.get_template().'/app_menu', $get_appmenu, true);
						$output[$count_output]['isoutput'] = 1;
						$output[$count_output]['isdiv'] = 1;
						break;

					case 'mfunction':
						//$mfunction = array();
						//include_once DOCUMENT_ROOT.'/includes/core/element/element_mfunction.inc';
						//$mfunction = f_element_mfunction($this_element_name,$an,$app,$aved);
						$output[$count_output]['isoutput'] = 0;
						$output[$count_output]['isdiv'] = 0;
						break;

					case 'fdata':
						include_once DOCUMENT_ROOT.'/includes/core/element/element_dgroup.inc';
						include_once DOCUMENT_ROOT.'/includes/core/element/element_dgroup_structure.inc';
						include_once DOCUMENT_ROOT.'/includes/core/element/element_dgroup_aved_list.inc';
						include_once DOCUMENT_ROOT.'/includes/core/element/element_fdata.inc';
						$output[$count_output]['data'] = element_fdata($this_element_name);
						$output[$count_output]['isoutput'] = 1;
						$output[$count_output]['isdiv'] = 0;
						break;

					case 'method':
						if (method_exists($this, $this_element['name'])) $output[$count_output] = call_user_func_array(array($this, $this_element['name']), $params=array());
						break;

					case 'comment' :
						$this->load->library('CommentsL');
						$output[$count_output]['html'] = $this->commentsl->get_page_html();
						$output[$count_output]['isoutput'] = 1;
						$output[$count_output]['isdiv'] = 1;
						break;

					case 'tag' :
						$this->load->library('TagsL');
						$output[$count_output]['html'] = $this->tagsl->get_html();
						$output[$count_output]['isoutput'] = 1;
						$output[$count_output]['isdiv'] = 1;
						break;
				}

			} else {
				//for AJAX element

				//if the thisid field is set in this element row, some other id will be used
				//other id is loaded by getthisid, this is yet to be ported to CI
				$this_element_thisid_en = ($this_element['thisid'])
					? $getthisid['en'][$this_element_thisid]
					: $this->url['id_encrypted'];

				//format a AJAX to load on full page load
				//in OUTPUT function, if this ['ajax'] exist, it will format a AJAX call
				$output[$count_output]['isoutput'] = 1;
				$output[$count_output]['isdiv'] = 1;
				$output[$count_output]['ajax']['app'] = $this->url['app'];
				$output[$count_output]['ajax']['an'] = $this_element['target_an'];
				$output[$count_output]['ajax']['subaction'] = $this_element['subaction'];
				$output[$count_output]['ajax']['elementid'] = $this_element['element_id'];
				$output[$count_output]['ajax']['id'] = $this_element_thisid_en;
			}

			//if ['outputdiv'] or ['output'] is set, an array will be form for this element
			//

			//if ['output'] is set

			//if ['isoutput'] = 1, this element is going to output some HTML data
			//if ['isoutput'] = 0, this element will just be processed without any HTML output, $count_output is not added 1
			if ($output[$count_output]['isoutput']) {

				$output[$count_output]['element_name'] = $this_element['name'];
				$output[$count_output]['element_id'] = $this_element['element_id'];
				$output[$count_output]['element_type'] = $this_element['type'];

				//format a DIV section
				//if ['isdiv'] is set, <div id="['element_id']"></div> will be inserted when the layout is FULL PAGE (layout_type = 1)
				if (isset($output[$count_output]['isdiv'])) {
					$output[$count_output]['div']['title'] = ($this_element['langname'])
						? $this->lang->line('coreename_'.$this_element['langname'])
						: $output[$count_output]['div']['title'] = $this->lang->line('coreename_'.$this_element['type'].'_'.$this_element['name']);

					$output[$count_output]['div']['element_name'] = $this_element['name'];
					$output[$count_output]['div']['element_id'] = $this_element['element_id'];
					$output[$count_output]['div']['tab'] = $this_element['tab'];
					$output[$count_output]['div']['divwh'] = $this_element['divwh'];
					$output[$count_output]['div']['colnum'] = $this_element['colnum'];
					$output[$count_output]['div']['divstyle'] = $this_element['divstyle'];
				}

				$count_output++;
			}

		}

		return $output;
	}














	//beginning of output
	function output(){

		//
		// output_foreach
		//
		//return with final output, formated by template inc files
		//
		//foreach (array_keys($this->output) as $this_key){
		//	if (!isset($output[$this_key]['ajax'])) $output[$this_key] = $this->output_foreach($output[$this_key],$layout['folderinc'],$layout['name']);
		//}

		//temp fix for new json output for dgroup
		if ($this->layout['type'] == 'html') $this->layout['type'] = 'json';

		switch ($this->layout['type']) {
			// 0=no output (no swithc case form this)
			// 1=html
			// 2=plain html
			// 3=xml
			// 4=json
			// /////5=json auto complete
			// /////6=html one value

			case 'html':
				header('Content-Type:text/html');
				//just print content in the array name "html"
				if ($this->data){
					foreach ($this->data as $output) {
						if (isset($output['html'])) echo $output['html'];
					}
				}

				break;

			case 'xml':
				header('Content-Type:text/xml');
				// this part is changed but yet to test
				// if there is an array name "xml", just print, content alreay in xml format
				// else if there is array name "data", convert to xml format
				//echo $h_xml; << old value name
				if ($this->data){
					foreach ($this->data as $output) {
						if ($output['xml']){
							echo $output['xml'];
						} elseif ($output['data']) {
							//php array to xml format
						}
					}

				}

				break;

			case 'json':
				header('Content-type: text/json');
				header('Content-type: application/json');
				// if there is an array name "json", just print, content alreay in json format
				// else if there is array name "data", convert to json format
				if ($this->data) {
					foreach ($this->data as $output) {
						if (isset($output['json'])) {
							echo $output['json'];
						} elseif (isset($output['data'])) {
							echo json_encode($output['data']);
						}
					}
				}
				//example header
				//header('Cache-Control: no-cache, must-revalidate');
				//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
				break;

			case 'full':
			default:
				//load the template page.inc file to output the full page with template layout
				//include_once DOCUMENT_ROOT.'/'.$layout['folderinc'].'/page.inc';
				//output_fullpage($output,$layout);
				$this->output_fullpage();

				break;
		}
	}




	private function output_fullpage(){
		$layout = $this->layout;

		$pagedata = array(
			'title' => '',
			'head' => '',
			'css' => '',
			'js' => '',

			'sidebar' => '',

			'breadcrumb' => '',
			'jsonload' => '',
			'content' => '',
			'app_menu' => '',

			'js_bodyend' => ''
		);

		//load addon
		if (isset($layout['addons'])) $addons = $this->LayoutM->Html_addons($layout['addons']);

		$pagedata['css'] .= $addons['css'];
		$pagedata['js'] .= $addons['js'];
	    $pagedata['js_bodyend'] .= $addons['js_bodyend'];


		/*
		 * $layout_page
		 *
		 * format the page
		 *
		 */

		//load either the pagefile or pageplain
		//load MAINMENU
		//load TOPBAR
		if ($this->UserM->is_logged_in()) {
			if (isset($layout['logo']) || isset($layout['menu'])){
				//$layout_page = file_get_contents($layout['pagefile']);

				$sidebar['app_list'] = ($layout['menu'])
					? $layout['menu_array']
					: array();

				if ($layout['logo']) {
					//include_once DOCUMENT_ROOT.'/'.$layout['folderinc'].'/topbar.inc';
					//$layout_page = preg_replace("/#topbar#/", topbar(), $layout_page);
					//$history = core_log_loadhistory();
					//$lang['session']['companyname']
					//$lang['core']['welcome'].' '.$id['name']
					$sidebar['history'] = $this->LogM->get_history();
					if (isset($this->log_data['log_type']) && ! empty($this->log_data['log_type'])) {
						$sidebar['can_follow'] = $this->log_data['log_type']['can_follow'];
						$sidebar['can_favorite'] = $this->log_data['log_type']['can_favorite'];
					}

					$sidebar['company_logo'] = 'logo.png';
					$sidebar['company_name'] = 'Telcoson';
					$sidebar['show_user_status'] = TRUE;
					$sidebar['user_name'] = $this->UserM->get_name();

					$pagedata['sidebar'] = $this->load->view('/'.get_template().'/sidebar', $sidebar, TRUE);
				}

				//no action for $layout_format_footer

			} else {
				//$layout_page = file_get_contents($layout['pageplain']);
			}

			//load appmenu + breadcrumb
			if (isset($layout['appmenu']) || isset($layout['breadcrumb'])) {
				//$appmenu = core_appmenu($app,$layout['appmenu'],$layout['appmenu_gp'],$layout['breadcrumb']);
				//$appmenu = f_layout_appmenu($appmenu,$layout['appmenu'],$layout['breadcrumb'],1);

				$this->load->model('AppmenuM');
				$app_menu = $this->AppmenuM->get_appmenu();
				$pagedata['breadcrumb'] = $this->load->view('/'.get_template().'/breadcrumb', $app_menu, TRUE);
				$pagedata['app_menu'] = $this->load->view('/'.get_template().'/app_menu', $app_menu, TRUE);
			}
		}

		//load the content in $output to $h_html
		//makeup the DIV html
		//put in the $output[]['html'] into the DIV if any
		$output_content = $this->output_content();
		//$h_html = $output_page['html'];
		//$h_js_onload = $output_page['jsonload'];

	    //format $h_js_onload
		if (isset($output_content['jsonload'])) {
			$output_content['jsonload'] = '
				<script type="text/javascript">
					$(document).ready(function() {
						'.$output_content['jsonload'].'
					});
				</script>
			';
		}

		$pagedata['title'] = 'T Business';
		$pagedata['head'] = $pagedata['css'].$pagedata['js'];
		$pagedata['jsonload'] = $output_content['jsonload'];
		$pagedata['content'] = $output_content['html'].$pagedata['js_bodyend'];

		if ($this->is_ajax) {
			$this->load->view('/'.get_template().'/page_ajax', $pagedata);
		} else {
			$pagedata['debug'] = $this->debug;
			$this->load->view('/'.get_template().'/page_full', $pagedata);
		}

		//$layout_page = preg_replace("/#head#/", $h_css.''.$h_head.''.$h_js.''.$h_js_onload, $layout_page);
		//$layout_page = preg_replace("/#content#/", megshow().''.$h_html.''.$h_js_bodyend, $layout_page);
	}


	private function output_content() {
		$result = array();
		$content_div = array(
			'col_1' => '',
			'col_2' => '',
			'col_3' => ''
		);

		//print_r($output);
		if (isset($this->layout['boxformat'])) $boxformat_array = explode(',', $this->layout['boxformat']);

		$boxformat_count = 0;
		$this_boxformat = 0;
		$count_output = 0;
		$count_tab = 0;
		$tabdata = array();

		$result['jsonload'] = '';
		$result['html'] = '';

		$view_data = array();
		foreach ($this->data as $data) {
			$view_data['data'] = $data;

			if (!isset($data['isdiv']) || $data['isdiv']===TRUE) {

				if (isset($data['div']['divwh'])) {
					$this_boxformat = $data['div']['divwh'];
					$boxformat_count++;
				} elseif (isset($boxformat_array[$boxformat_count])) {
					$this_boxformat = $boxformat_array[$boxformat_count];
					$boxformat_count++;
				} else {
					$this_boxformat = 1;
				}

				$active_style = (isset($data['div']['divstyle']) && $data['div']['divstyle'] != '')
					? $data['div']['divstyle']
					: 'default';
				$view_data['active_style'] = $active_style;

				if (!isset($view_data['divstyle'][$active_style])) $view_data['divstyle'][$active_style] = $this->get_divstyle($active_style);

				if (!isset($data['div']['colnum'])) {
					$data['div']['colnum'] = 1;
				} elseif ($data['div']['colnum'] == 0) {
					$data['div']['colnum'] = 1;
				}
				if (!isset($content_div['col_'.$data['div']['colnum']])) $content_div['col_'.$data['div']['colnum']] = '';

				//$this_apps_html['colnum']
				//$this_apps_html['actlayout']
				//echo $this_boxformat;

				switch ($view_data['divstyle'][$active_style]['style']) {
					case 'tgrid':
						switch($this_boxformat){
							case '1': $view_data['gridnum'] = '12'; break;
							case '2': $view_data['gridnum'] = '5'; break;
							case '3': $view_data['gridnum'] = '3'; break;
							case '6': $view_data['gridnum'] = '2'; break;
							default: $view_data['gridnum'] = '12'; break;
						}

						if (isset($data['div']['tab']) && $data['div']['tab'] == 1) {
							//$view_data['build_tab'] = $this->output_page_format_tgrid_tab($view_data['build_tab']);

							//check if next div is also a tab, close the tab html if next div is not a tab
							//add in logic to check if next div is a outputdiv, if it's not, skip to check the next one.
							//$chktab_count = $count_output+1;
							//$chktab_cont = 1;

							//while ($chktab_cont == 1){

							if (!isset($tabdata['gridnum'])) $tabdata['gridnum'] = $view_data['gridnum'];

							$tabdata['data'][$count_output]['div']['element_id'] = $view_data['data']['div']['element_id'];
							$tabdata['data'][$count_output]['div']['title'] = $view_data['data']['div']['title'];
							if (isset($view_data['data']['html'])) {
								$tabdata['data'][$count_output]['html'] = $view_data['data']['html'];
							} else {
								$tabdata['data'][$count_output]['html'] = "";
							}

							if (!isset($this->data[$count_output+1]) || isset($this->data[$count_output+1]) && $this->data[$count_output+1]['div']['tab'] == 0){
								//next element is not a tab, so warp up the tab
								$content_div['col_'.$data['div']['colnum']] .= $this->load->view('/'.get_template().'/element/component_grid_tab', $tabdata, TRUE);
								$tabdata = array();
							}

							//}
						} else {
							$content_div['col_'.$data['div']['colnum']] .= $this->load->view('/'.get_template().'/element/component_grid', $view_data, TRUE);
						}
						break;

					case 'simple':
						$content_div['col_'.$data['div']['colnum']] .= $this->load->view('/'.get_template().'/element/component_simple', $view_data, TRUE);
						break;
				}

			}

			/*
			if (isset($data['ajax']) && $data['ajax']) {
				$result['jsonload'] .= 'apps_action_ajax("';
				$result['jsonload'] .= $data['ajax']['app'].'","';
				$result['jsonload'] .= $data['ajax']['an'].'","';
				$result['jsonload'] .= $data['ajax']['subaction'].'","';
				$result['jsonload'] .= $data['ajax']['elementid'].'","';
				$result['jsonload'] .= $data['ajax']['id'].'");';
			}
			*/

			if (isset($data['ajax']) && $data['ajax']) {
				$result['jsonload'] .= 'ajax_content("';
				$result['jsonload'] .= $data['ajax']['app'].'/';
				$result['jsonload'] .= $data['ajax']['an'].'/';
				$result['jsonload'] .= $data['ajax']['id'].'/';
				$result['jsonload'] .= $data['ajax']['subaction'].'","';
				$result['jsonload'] .= $data['ajax']['elementid'].'");';
			}

			$count_output++;
		}

		/////////////////////////////////////////////////////////
		//load content layout
		/////////////////////////////////////////////////////////
		$template = '/'.get_template().'/element/content_layout_';
		$template .= (isset($this->layout['content']) && $this->layout['content']!='')
			? $this->layout['content']
			: 'full';

		$result['html'] = $this->load->view($template, $content_div, TRUE);

		return $result;
	}

	//TODO: Move this to layout model
	private function get_divstyle($action_layout_name) {
		$rs = $this->db->select()
				->where('core_apps_action_layout_name', $action_layout_name)
				->get('global_setting.core_apps_action_layout', 1);
		$result = $rs->row_array();

		$this_array = array();
	    $this_array['style'] = $result['core_apps_action_layout_style'];
	    $this_array['title'] = $result['core_apps_action_layout_title'];
	    $this_array['drag'] = $result['core_apps_action_layout_drag'];
	    $this_array['collaps'] = $result['core_apps_action_layout_collaps'];
	    $this_array['boxless'] = $result['core_apps_action_layout_boxless'];
	    $this_array['css'] = $result['core_apps_action_layout_css'];
	    $this_array['formtype'] = $result['core_apps_action_layout_formtype'];
	    $this_array['listtype'] = $result['core_apps_action_layout_listtype'];
	    $this_array['viewtype'] = $result['core_apps_action_layout_viewtype'];
	    $this_array['formcss'] = $result['core_apps_action_layout_formcss'];
	    $this_array['tablecss'] = $result['core_apps_action_layout_tablecss'];
	    $this_array['viewcss'] = $result['core_apps_action_layout_viewcss'];

		return $this_array;
	}













	//basic API functions for all controllers
	function api_get() {
		$data = $this->model->get($this->url['id_plain']);

		$resp = array(
			'success' => TRUE,
			'data' => $data
		);

		echo json_encode($resp);
	}


	function api_list() {
		$data = $this->model->get_list();

		$resp = array(
			'success' => TRUE,
			'data' => $data
		);

		echo json_encode($resp);
	}



}