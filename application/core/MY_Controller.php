<?php

class MY_Controller extends CI_Controller {
	var $url = array(
		'app' => '',
		'action' => '',
		'id_plain' => 0,
		'id_encrypted' => 0,
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
	var $layout = array();


	function __construct() {
		parent::__construct();

		$this->load->model('User');
		$this->load->model('ACL');
		$this->load->model('App_general');
		$this->load->model('Html');
		$this->load->model('App');

		$this->setup_url();
		$this->User->setup();
		$this->setup_language();
		$this->App->setup();

		if ($this->App->must_disable_plain_id()) $this->ACL->check_id_encryption();
		$this->ACL->check_app_access();

		//$this->output->enable_profiler(true);
	}

	//remap every URI call
	public function _remap($action, $params = array()){
		//if the function exist in the Controller, use it
		if (method_exists($this, $action)) return call_user_func_array(array($this, $action), $params=array());

		//else, run default action
		$this->default_action($params);
	}

	public function default_action($params) {
		//if no matching APP AN is found in the DB, call to default index in the Controller file
		if (!$this->App->has_actions()) return call_user_func_array(array($this, 'index'), $params);

		$this->data = $this->app_load();
		$this->layout = $this->Html->html_template($this->App->actions());

		$this->output();
	}







	private function setup_url() {
		$this->url['app'] = $this->router->fetch_class();
		$this->url['action'] = $this->router->fetch_method();
		$this->url['subaction'] = $this->uri->segment(4, '');

		$id = $this->uri->segment(3, 0);

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
	}

	private function setup_language() {
		$this->load->model('Langmodel');
		$this->lang->initialise($this->Langmodel->initialise());
		$this->lang->loadarray($this->Langmodel->loadarray("core", $this->lang->lang_use));
		$this->lang->loadarray($this->Langmodel->loadarray($this->url['app'], $this->lang->lang_use));
	}









	private function app_load(){

		$apps_action = $this->App->actions;
		/*
		 * thisid
		 */
		$thisid = $this->url['id_plain'];

		/*
		 * access right

		if (!$this->User->info && !$apps_action['core_apps_action_public']) {

		    header( 'Location: '.base_url().'access/login/?re_app='.$app.'&re_an='.$an.'&re_aved='.$aved.'&re_thisid='.$re_thisid);
		    exit;

		} elseif (isset($this->User->id['accessgp']) && $this->User->id['accessgp'] != 1 && !$apps_action['core_apps_action_public']) {

		    $app_access_rights_table = $this->Access_model->core_access_rights_table($app,$an,$aved,$this->User->id,$apps_action);

		    if ($app_access_rights_table['allow'] == 3) {
		    //requestion aved is not allowed/set in AN
		    meg(999,"AN Permission Not Allow. - ".$aved);
		    }elseif ($app_access_rights_table['allow'] == 2) {
		    //the access is denied by an entry in the access_rights table
		    meg(999,"Access Rights Permission Not Allow. - ".$app_access_rights_table['typeid']);
		    }elseif($app_access_rights_table['allow'] != 1){
		    //not permission is set to allow access, minimum set a Allow all rule for a App for each master group (except Admin)
		    meg(999,"Access Rights Permission Not Allow. - No Permission");
		    }

		}
		*/
		//getthisid






		//looping the elements, and switch the element_type

		$all_action_elements = $this->Apps->get_action_element($this->url['app'], $this->url['action']);
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
			$this_element = $this->Apps->get_this_element($this_element, $mfunction, $apps_action);

			// if it's NOT ajax, pass to model to process, else pass the value to format [ajax] ajax
			if (!$this_element['ajax'] || $this->url['subaction'] == 'ss') {

				switch ($this_element['type']) {
					case 'dgroup':
						$this->load->model('element/Element_dgroup');
						$this->load->model('element/Element_button');

						$core_element_dgroup = $this->Element_dgroup->core_element_dgroup($this_element);
						$output[$count_output]['isoutput'] = 1;
						$output[$count_output]['isdiv'] = 0;
						$output[$count_output]['data'] = $core_element_dgroup['data'];
						$output[$count_output]['element_button'] = $core_element_dgroup['element_button'];

						break;

					case 'statpanel':
						$this->load->model('element/Element_statpanel');
						$output[$count_output] = $this->Element_statpanel->get_statpanel();

						break;

					case 'menu':
						$output[$count_output]['data'] = core_appmenu($app,1,$this_element_name,0);
						$output[$count_output]['isoutput'] = 1;
						$output[$count_output]['isdiv'] = 1;
						//html_show_array($output[$count_output]);exit;
						break;

					case 'xfunction':
						include_once DOCUMENT_ROOT.'/includes/core/element/element_button.inc';
						include_once DOCUMENT_ROOT.'/includes/core/element/element_xfunction.inc';
						$output[$count_output] = f_element_xfunction($this_element_name, $an, $app, $aved, $thisid, $thisid_en,
							$this_element_aved, $this_element_id, $this_element_add, $this_element_view, $this_element_edit,
							$this_element_del, $this_element_list, $this_element_search);

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
				}

			} else {
				//for AJAX element

				//if the thisid field is set in this element row, some other id will be used
				//other id is loaded by getthisid, this is yet to be ported to CI
				$this_element_thisid_en = ($this_element['thisid'])
					? $getthisid['en'][$this_element_thisid]
					: $this->url['id_plain'];

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
	private function output(){

		//
		// output_foreach
		//
		//return with final output, formated by template inc files
		//
		//foreach (array_keys($this->output) as $this_key){
		//	if (!isset($output[$this_key]['ajax'])) $output[$this_key] = $this->output_foreach($output[$this_key],$layout['folderinc'],$layout['name']);
		//}


		//temp fix for new json output for dgroup
		if ($this->layout['type'] == 2) $this->layout['type'] = 4;

		switch ($this->layout['type']) {
			// 0=no output (no swithc case form this)
			// 1=html
			// 2=plain html
			// 3=xml
			// 4=json
			// /////5=json auto complete
			// /////6=html one value

			case '1':
				//load the template page.inc file to output the full page with template layout
				//include_once DOCUMENT_ROOT.'/'.$layout['folderinc'].'/page.inc';
				//output_fullpage($output,$layout);
				$this->output_fullpage();

				break;

			case '2':
				header('Content-Type:text/html');
				//just print content in the array name "html"
				if ($this->data){
					foreach ($this->data as $this_output) {
						if (isset($this_output['html'])) echo $this_output['html'];
					}
				}

				break;

			case '3':
				header('Content-Type:text/xml');
				// this part is changed but yet to test
				// if there is an array name "xml", just print, content alreay in xml format
				// else if there is array name "data", convert to xml format
				//echo $h_xml; << old value name
				if ($this->data){
					foreach ($this->data as $this_output) {
						if ($this_output['xml']){
							echo $this_output['xml'];
						} elseif ($this_output['data']) {
							//php array to xml format
						}
					}

				}

				break;

			case '4':
				header('Content-type: text/json');
				header('Content-type: application/json');
				// if there is an array name "json", just print, content alreay in json format
				// else if there is array name "data", convert to json format
				if ($this->data) {
					foreach ($this->data as $this_output) {
						if (isset($this_output['json'])) {
							echo $this_output['json'];
						} elseif ($this_output['data']) {
							echo json_encode($this_output['data']);
						}
					}
				}
				//example header
				//header('Cache-Control: no-cache, must-revalidate');
				//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
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

			'menumain' => '',
			'top' => '',

			'content' => '',

			'js_bodyend' => ''
		);

		//load addon
		if (isset($layout['addons'])) $addons = $this->Html->Html_addons($layout['addons']);

	    $pagedata['css'] .= preg_replace("/href=\"/", "href=\"".base_url(), $addons['css']);
	    $pagedata['js'] .= preg_replace("/src=\"/", "src=\"".base_url(), $addons['js']);
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
		if (isset($layout['logo']) || isset($layout['menu'])){
	        //$layout_page = file_get_contents($layout['pagefile']);

			if ($layout['menu']) {
				//$this->load->view('web/mainmenu');
				//include_once DOCUMENT_ROOT.'/'.$layout['folderinc'].'/menumain.inc';
				//$layout_page = preg_replace("/#menu#/", menu_main(), $layout_page);
				$pagedata['menumain'] = '';
			} else {
				//$layout_page = preg_replace("/#menu#/", "", $layout_page);
				$pagedata['menumain'] = '';
			}

			if ($layout['logo']) {
				//include_once DOCUMENT_ROOT.'/'.$layout['folderinc'].'/topbar.inc';
				//$layout_page = preg_replace("/#topbar#/", topbar(), $layout_page);
				//$history = core_log_loadhistory();
				//$lang['session']['companyname']
				//$lang['core']['welcome'].' '.$id['name']
				$pagetopdata['companyname'] = 'Telcoson';
				$pagetopdata['welcome'] = 'Welcome to CI';
				$pagedata['top'] = $this->load->view('web/top.php', $pagetopdata, true);
			}

			//no action for $layout_format_footer

	    } else {
	        //$layout_page = file_get_contents($layout['pageplain']);
	    }

		//load the content in $output to $h_html
		//makeup the DIV html
		//put in the $output[]['html'] into the DIV if any
		$output_content = $this->output_content();
		//$h_html = $output_page['html'];
		//$h_js_onload = $output_page['jsonload'];

	    //load appmenu + breadcrumb
	    if (isset($layout['appmenu']) || isset($layout['breadcrumb'])){
			//include_once DOCUMENT_ROOT.'/'.$layout['folderinc'].'/appmenu.inc';
			//$appmenu = core_appmenu($app,$layout['appmenu'],$layout['appmenu_gp'],$layout['breadcrumb']);
			//$appmenu = f_layout_appmenu($appmenu,$layout['appmenu'],$layout['breadcrumb'],1);
			$pagedata['menuapp'] = '';
	    } else {
	    	$pagedata['menuapp'] = '';
	    }

	    //format $h_js_onload
		if (isset($output_content['jsonload'])) {
			$output_content['jsonload'] = '
				<script type="text/javascript">
					window.onload = start;
					function start() {
						'.$output_content['jsonload'].'
					}
				</script>
			';
		}

		$pagedata['title'] = 'T Business';
		$pagedata['head'] = $pagedata['css'].$pagedata['js'].$output_content['jsonload'];
		//$pagedata['head'] = $pagedata['css'].$pagedata['js'];
		$pagedata['content'] = $output_content['html'].$pagedata['js_bodyend'];
		$this->load->view('web/page_full', $pagedata);

		//$layout_page = preg_replace("/#title#/", "T Business", $layout_page);
		//$layout_page = preg_replace("/#head#/", $h_css.''.$h_head.''.$h_js.''.$h_js_onload, $layout_page);
		//$layout_page = preg_replace("/#menuapp#/", $appmenu, $layout_page);
		//$layout_page = preg_replace("/#content#/", megshow().''.$h_html.''.$h_js_bodyend, $layout_page);

		//header("Content-Type:text/html");
		//echo $layout_page;
	}


	private function output_content() {
		$result = array();
		$content_div = array();

		//print_r($output);
		if (isset($this->layout['boxformat'])) $boxformat_array = explode(',', $this->layout['boxformat']);

		$boxformat_count = 0;
		$this_boxformat = 0;
		$count_output = 0;

		$result['jsonload'] = '';
		$result['html'] = '';

		$view_data = array();
		foreach ($this->data as $data) {
			$view_data['data'] = $data;

			if ($data['isdiv']) {

				if (isset($data['div']['divwh'])) {
					$this_boxformat = $data['div']['divwh'];
					$boxformat_count++;
				} elseif (isset($boxformat_array[$boxformat_count])) {
					$this_boxformat = $boxformat_array[$boxformat_count];
					$boxformat_count++;
				} else {
					$this_boxformat = 1;
				}

				//print_r($data);

				$active_style = ($data['div']['divstyle'] != '')
					? $data['div']['divstyle']
					: 'default';
				$view_data['active_style'] = $active_style;

				if (!isset($view_data['divstyle'][$active_style])) $view_data['divstyle'][$active_style] = $this->get_divstyle($active_style);

				if (!isset($data['colnum'])) $data['colnum'] = 1;
				if (!isset($content_div[$data['colnum']])) $content_div[$data['colnum']] = '';

				//$this_apps_html['colnum']
				//$this_apps_html['actlayout']
				//echo $this_boxformat;

				switch ($view_data['divstyle'][$active_style]['style']) {
					case 'tgrid':
						switch($this_boxformat){
							case '1': $view_data['gridnum'] = 'grid_6'; break;
							case '2': $view_data['gridnum'] = 'grid_4'; break;
							case '3': $view_data['gridnum'] = 'grid_3'; break;
							case '6': $view_data['gridnum'] = 'grid_1'; break;
							default: $view_data['gridnum'] = 'grid_6'; break;
						}

						if ($data['div']['tab'] == 1) {
							//$view_data['build_tab'] = $this->output_page_format_tgrid_tab($view_data['build_tab']);

							//check if next div is also a tab, close the tab html if next div is not a tab
							//add in logic to check if next div is a outputdiv, if it's not, skip to check the next one.
							$chktab_count = $count_output+1;
							$chktab_cont = 1;

							while ($chktab_cont == 1){
								if (isset($this->data[$chktab_count]['isdiv'])){
									//next div that is for output
									if (!$this->data[$chktab_count]['div']['tab']) {
										//is not a tab, so warp up the tab
										$content_div[$data['colnum']] .= $this->load->view('default/web/component_grid_tab_wrap', $view_data, TRUE);
										//$build_tab = array("li" => array(), "section" => array());
									}
									$chktab_cont = 0;
								} elseif (!isset($this->data[$chktab_count])) {
									//end of $output array
									//warp up the tab
									$chktab_cont = 0;
									$content_div[$data['colnum']] .= $this->load->view('default/web/component_grid_tab_wrap', $view_data, TRUE);
								}
							}
						} else {
							$content_div[$data['colnum']] .= $this->load->view('default/web/component_grid', $view_data, TRUE);
						}
						break;

					case 'simple':
						$content_div[$data['colnum']] .= $this->load->view('default/web/component_simple', $view_data, TRUE);
						break;
				}

			}

			if (isset($data['ajax']) && $data['ajax']) {
				$result['jsonload'] .= 'apps_action_ajax("';
				$result['jsonload'] .= $data['ajax']['app'].'","';
				$result['jsonload'] .= $data['ajax']['an'].'","';
				$result['jsonload'] .= $data['ajax']['subaction'].'","';
				$result['jsonload'] .= $data['ajax']['elementid'].'","';
				$result['jsonload'] .= $data['ajax']['id'].'");';
			}

			$count_output++;
		}



		/////////////////////////////////////////////////////////
		//load content
		/////////////////////////////////////////////////////////
		$sql3 = (!$this->layout['content'])
			? "SELECT * FROM core_layout_content WHERE core_layout_content_name  = 'full'"
			: "SELECT * FROM core_layout_content WHERE core_layout_content_name  = '".$this->layout['content']."'";

		$result3 = $this->db->query($sql3);
		$result3 = $result3->row_array(0);

		if (!$result3) meg(999,'No Content Format Specified Found.');

		$layout_cf_sort = explode(',', $result3['core_layout_content_sort']);
		$layout_cf[1]['start'] = $result3['core_layout_content_1_start'];
		$layout_cf[1]['end'] = $result3['core_layout_content_1_end'];
		$layout_cf[2]['start'] = $result3['core_layout_content_2_start'];
		$layout_cf[2]['end'] = $result3['core_layout_content_2_end'];
		$layout_cf[3]['start'] = $result3['core_layout_content_3_start'];
		$layout_cf[3]['end'] = $result3['core_layout_content_3_end'];
		$layout_cf[4]['start'] = $result3['core_layout_content_4_start'];
		$layout_cf[4]['end'] = $result3['core_layout_content_4_end'];
		$layout_boxformat = $result3['core_layout_content_defboxcol'];

		//insert the content_div into the core_layout_content as h_html

		$div_count = 0;

		foreach ($layout_cf_sort as $this_cf) {
			$result['html'] .= $layout_cf[$this_cf]['start'];
			$result['html'] .= $content_div[$this_cf];
			$result['html'] .= $layout_cf[$this_cf]['end'];
		}

		return $result;
	}

	//TODO: Move this to layout model
	private function get_divstyle($action_layout_name) {
		$rs = $this->db->select()
				->where('core_apps_action_layout_name', $action_layout_name)
				->get('core_apps_action_layout', 1);
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


















}