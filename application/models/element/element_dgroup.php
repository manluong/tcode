<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Element_dgroup extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function element_fdata_from_dgroup($dgroup_name,$fdata_name,$thisid,$thisapp=0,$thisaved=0){

	    if (!$thisapp) $thisapp = $this->url['app'];
	    if (!$thisaved) $thisaved = "v";

	    $dgroup_structure = $this->core_element_dgroup_structure($thisapp,$thisaved,$dgroup_name,$thisid,0);
		$dgroup_value = $this->core_element_dgroup_value($dgroup_structure);
	    $f_element_fdata = $this->element_fdata($fdata_name,$dgroup_value);
	    //print_r($f_element_fdata);

	return($f_element_fdata);
	}

	function element_fdata($this_element_name,$dataarray=""){

	$sql1 = "SELECT * FROM core_e_fdata WHERE core_e_fdata_name = '$this_element_name' LIMIT 1";
	$result1 = $this->db->query($sql1);
	$result1 = $result1->row_array(1);

	if ($result1){

	    if ($result1['core_e_fdata_type'] == "jsonac"){

	        $jsonac_value = explode(",", $result1['core_e_fdata_jsonac_value']);
	        $jsonac_label = explode(",", $result1['core_e_fdata_jsonac_label']);
	        $jsonac_seprator = explode("^,", $result1['core_e_fdata_jsonac_seprator']);

			$core_element_dgroup = $this->core_element_dgroup($result1['core_e_fdata_dgroupname'],"",$app,"sq","","","sq","",0,0,0,0,1,1,"",1);



	        $this_array = array();
	        //print_r($h_apps_html[0]['data']['listfield']);
	        //print_r($jsonac_value);
	        //print_r($jsonac_label);
	        //
	        //$json_data = $h_apps_html[0]['data']['list'];
			//$json_data = ;
			//print_r($json_data);
	        if ($core_element_dgroup['data']['table']){

	             $json_count = 0;
	             foreach ($core_element_dgroup['data']['table'] as $this_row) {

	                $count=0;
	                $vaule="";
	                $label="";
	                foreach (array_keys($core_element_dgroup['data']['listarray']) as $this_field) {
	                    foreach ($jsonac_value as $this_value) {
	                    if ($this_field == $this_value) $vaule .= $this_row[$count];

	                    }
	                    $count++;
	                }

	                $count=0;
	                $count2=0;
	                foreach (array_keys($core_element_dgroup['data']['listarray']) as $this_field) {
	                    foreach ($jsonac_label as $this_label) {

	                    if ($this_field == $this_label) {
	                        if ($count2 == 0) { $label = $jsonac_seprator[$count2]; $count2++; }
	                        $label .= $this_row[$count].$jsonac_seprator[$count2];
	                        $count2++;
	                    }

	                    }
	                    $count++;
	                }

	          		array_push($this_array, array(
	          			"value" => $vaule,
	          			"label" => $label
	          		));

	                $json_count++;
	             }

	        }

	        $result = $this_array;

	    }elseif ($result1['core_e_fdata_type'] == "arrayone"){

	        $arrayone_value = explode(",", $result1['core_e_fdata_arrayone_field']);
	        $arrayone_seprator = explode("^,", $result1['core_e_fdata_arrayone_seprator']);
	        $arrayone_langpre = $result1['core_e_fdata_arrayone_langpre'];
	        $arrayone_langsub = $result1['core_e_fdata_arrayone_langsub'];

	        //foreach (array_keys($dataarray) as $this_field){
	        $count=0;
	        foreach ($arrayone_value as $this_value) {
	            if ($count == 0) { $result = $arrayone_seprator[$count]; $count++; }
	            if (!isset($dataarray[$this_value])) $dataarray[$this_value] = "";
	            $result .= $dataarray[$this_value].$arrayone_seprator[$count];
	            $count++;
	        }
	        //}


	    }



	}else{
	  echo "No Fdata Name";
	}

	return($result);
	}

	function core_element_dgroup($this_element, $is_fdata=0){

		$app = $this->url['app'];
		$an = $this->url['action'];

		$this_element_name = $this_element['name'];
		$this_element_aved = $this_element['subaction'];
		$this_element_id = $this_element['element_id'];
		$this_element_add = $this_element['add'];
		$this_element_view = $this_element['view'];
		$this_element_edit = $this_element['edit'];
		$this_element_del = $this_element['del'];
		$this_element_list = $this_element['list'];
		$this_element_search = $this_element['search'];
		$this_element_dgroupextend = $this_element['dgroupextend'];

	    //
	    //get the structure
	    //

	    //gethisid function
	    //disabled for integration to CI
	    /*
	    if ($this_element_thisid) {
	    $this_dgroupid[0] = $getthisid[$this_element_thisid];
	    }else{
	    $this_dgroupid = $thisid;
	    }
		*/
		$this_dgroupid = $this->url['id_plain'];

	    $dgroup_structure = $this->core_element_dgroup_structure($app,$this_element_aved,$this_element_name,$this_dgroupid,$this_element_dgroupextend);


	    //
	    //get the structure for e_xtra
	    //
	    if (isset($dgroup_structure['e_xtra'])){

			$this->load->model('element/Element_dgroup_xtra');
	        $xtra_structure = $this->Element_dgroup_xtra->core_element_xtra_structure($dgroup_structure);

	        if ($xtra_structure){
	            $button_e_xtra = 1;
	            $dgroup_structure = $xtra_structure;
	        }

	    } else {
			$button_e_xtra = 0;
		}

	    /////////////////////////////////
	    //get the value
	    //es is needed for logging purpose for current record
	    $dgroup_value = array();
	    if ($this_element_aved == "v" || $this_element_aved == "e" || $this_element_aved == "d" || $this_element_aved == "es") {
	    if ($dgroup_structure['sql']) $dgroup_value = $this->core_element_dgroup_value($dgroup_structure);
	    }


	    if (isset($dgroup_value) && $dgroup_value) {
	    	$dgourp_value_notfound = 0;
		} else {
			$dgourp_value_notfound = 1;
		}

	    //display error from dgroup_structure
	    if (isset($dgroup_structure['error'])){

	        meg(999,"apps_action_dgroup.inc: ".$dgroup_structure['error']);

	    }else{
	    //continue if no error

	       // if ($layout['format'] == "data") include_once $DOCUMENT_ROOT.'/includes/layout/layout_data.inc';

	        if (!$is_fdata) $element_button = $this->Element_button->core_element_button($app,$an,$this_element_aved,$this_element_id,$this_element_add,$this_element_view,$this_element_edit,$this_element_del,$this_element_list,$this_element_search,$dgourp_value_notfound,$dgroup_structure['basetype'],$dgroup_structure['formadd_allowforceid'],$button_e_xtra);


	        $data['dgrouptype'] = $dgroup_structure['avedtype'];

	        switch($dgroup_structure['avedtype']){

				/*
				 * FORM
				 */
	            case "form":
	            //$h_apps_html[$count_element] = f_layout_form($app,$this_element_name,$this_element_aved,$thisid,$dgroup_structure,$dgroup_value,$this_element_id,$element_button,$this_element_target_an,$thisid_en);
	            //print_r($h_apps_html[$count_element]);

				if ($this_element_aved == "v" && !$dgroup_value) {
				//} elseif ($aved == "v" && ($layout['format'] == "div")) {
				$result['element_button'] = $element_button;
				$result['data']['dgrouptype'] = "empty";

				}elseif (($this_element_aved == "v" && $dgroup_value) || $this_element_aved == "d") {
				//view
				$this->load->Model('element/Element_dgroup_form');
				$result['data'] = $this->Element_dgroup_form->dgroup_view($dgroup_structure,$dgroup_value,$element_button,$this_element_aved);
				$result['element_button'] = $element_button;

				}elseif ($this_element_aved == "a" || $this_element_aved == "e" || $this_element_aved == "ed" || $this_element_aved == "ad" || $this_element_aved == "s" || $this_element_aved== "sd" || $this_element_aved == "f" || $this_element_aved == "fd") {
				//form
				$this->load->Model('element/Element_dgroup_form');
				$result['data'] = $this->Element_dgroup_form->dgroup_form($dgroup_structure,$dgroup_value,$element_button,$this_element_aved);
				$result['element_button'] = $element_button;

				}elseif ($this_element_aved == "as" || $this_element_aved == "es" || $this_element_aved == "ds" || $this_element_aved == "ss") {
				//save
				$this->load->Model('element/Element_dgroup_save');
				$result['data'] = $this->Element_dgroup_save->dgroup_save($dgroup_structure,$dgroup_value,$this_element_aved);
				$result['element_button'] = $element_button;

				}

				; break;

				/*
				 * LIST
				 */
	            case "list":
					$this->load->Model('element/Element_dgroup_list');
					$result = $this->Element_dgroup_list->dgroup_list($dgroup_structure,$dgroup_value,$element_button);

	            ; break;

				/*
				 * SEARCH
				 */
	            case "search":
	            if ($this_element_aved == "s" || $this_element_aved == "sd"){
	            	$this->load->Model('element/Element_dgroup_form');
	            	$data['form'] = $this->Element_dgroup_form->dgroup_form($dgroup_structure,$dgroup_value,$element_button);

	            }elseif ($this_element_aved == "sq"){
	            	$this->load->Model('element/Element_dgroup_list');
		            $result = $this->Element_dgroup_list->dgroup_list($dgroup_structure,$dgroup_value,$element_button,"",$is_fdata);


	            }elseif ($this_element_aved == "ss"){
	            	$this->load->Model('element/Element_dgroup_list');
	            	$searcharray = $this->Element_dgroup_list->dgroup_searcharray($dgroup_structure);
					$result = $this->Element_dgroup_list->dgroup_list($dgroup_structure,$dgroup_value,$element_button,$searcharray);

	            }elseif ($this_element_aved == "so"){

	            }

	            ; break;


	        }

	    }

		//switch between platform
		$platform = "web";

		switch ($platform){
			case "web":
			$this->load->model('element/web/View_dgroup');
			$this->load->model('element/web/View_button');
			break;

		}

	    $output_add = $this->View_dgroup->output_dgroup($result,$this_element);
		$result = array_merge($result,$output_add);
		$result['isoutput'] = 1;
		$result['isdiv'] = 0;
		//$result['data'] = $data;
		//$result['element_button'] = $element_button;

	return($result);
	}




function core_element_dgroup_structure($app,$aved,$dgroupname,$thisid,$this_element_dgroupextend){

//this function will get the structure of the from
//tables used,tables index and the fileds used with a JOIN SQL which include the fields details from core_db_fields

    $sql1 = "SELECT * FROM core_e_dgroup WHERE core_e_dgroup_name = '$dgroupname' AND core_e_dgroup_app = '$app'";
	$result1 = $this->db->query($sql1);
	$result1 = $result1->row_array(0);

    if (!$result1) meg(0,"MISSING DGROUP [core_element_dgroup]: ".$app."/".$dgroupname);


    $structure['icon'] = $result1['core_e_dgroup_icon'];
    $structure['thisidreq'] = $result1['core_e_dgroup_thisidreq'];
    $structure['basetype'] = $result1['core_e_dgroup_basetype'];
    $structure['listpg'] = $result1['core_e_dgroup_listpg'];
    $structure['listnotitle'] = $result1['core_e_dgroup_listnotitle'];
    $structure['formadd_allowforceid'] = $result1['core_e_dgroup_formadd_allowforceid'];
    $structure['listparentstyle'] = $result1['core_e_dgroup_parentstyle'];
    $structure['listparentnamefield'] = $result1['core_e_dgroup_parentnamefield'];
    $structure['listparenttop_lang'] = $this->lang->line($app."listparenttop_".$result1['core_e_dgroup_parenttop_lang']);
    $structure['fvnolabel'] = $result1['core_e_dgroup_fvnolabel'];

    $structure['formtype'] = $result1['core_e_dgroup_formtype'];
    $structure['listtype'] = $result1['core_e_dgroup_listtype'];
    $structure['searchtype'] = $result1['core_e_dgroup_searchtype'];

	$structure['fieldlist'] = "";
	$structure['fieldlist_short'] = "";
	$structure['list']['formtype'] = "";
	$structure['list']['listwidth'] = "";
	$structure['list']['listcoltype'] = "";
	$structure['list']['listsorttype'] = "";
	$structure['list']['listtxtsearch']  = "";
	$structure['list']['listname'] = "";
	$structure['list']['langc'] = "";
    //if (!$result1['core_e_dgroup_formtype']) $structure['formtype'] = $layout['formtype'];
    //if (!$result1['core_e_dgroup_listtype']) $structure['listtype'] = $layout['listtype'];

    /////////////////////////////////////////////////////////////////////////////
    //if got dgroupextend, get it. Do this here because $app value will change

    if ($this_element_dgroupextend){
        $sql5 = "SELECT * FROM core_e_dgroup_extend WHERE core_e_dgroup_extend_app = '$app' AND core_e_dgroup_extend_name = '$this_element_dgroupextend'";
		$result5 = $this->db->query($sql5);
		$result5 = $result5->row_array(0);

        if (!$result5) {
            meg(0,"MISSING DGROUP EXTEND [core_element_dgroup]: ".$app."/".$dgroupname."/".$this_element_dgroupextend);
        } else {
            $structure['extend_matchid'] = $result5['core_e_dgroup_extend_matchid'];
            $structure['extend_matchfield'] = $result5['core_e_dgroup_extend_matchfield'];
            $structure['extend_where'] = $result5['core_e_dgroup_extend_where'];
            $extend_add = $result5['core_e_dgroup_extend_add'];
        }
    }

	/*
    if(isset($structure['extend_matchid'])){
        if ($structure['extend_matchid'] == "uid"){
        $extend_wheresql = $structure['extend_matchfield']." = ".$uid;
        } elseif ($structure['extend_matchid'] == $xidtype){
        $extend_wheresql = $structure['extend_matchfield']." = ".$xid;
        }
    }
    */

    //REPLACE XXuid, XXxid (cid,staffid,vendorid)
    if (isset($structure['extend_where']) && isset($extend_wheresql)) {
        $structure['extend_where'] = $structure['extend_where'].", ".$extend_wheresql;
    }

    $this_extendcount = 0;
    if (isset($extend_add)){
	    $extend_add = explode(";", $extend_add);
	    foreach ($extend_add as $this_extend_add){
	    $this_extend_value = explode(",", $this_extend_add);
	    $structure['extend_add'][$this_extendcount]['table'] = $this_extend_value[0];
	    $structure['extend_add'][$this_extendcount]['field'] = $this_extend_value[1];
	    $structure['extend_add'][$this_extendcount]['value'] = $this_extend_value[2];
	    $this_extendcount++;
	    }
	}
    //define the type of this structure by using the aved type
    //form, list or search
    //will format the structure different
    //will format the sql different of what field to use
    switch($aved){
        case "a": $fielduse = 'core_e_dgroup_table_field_a'; $avedtype = "form"; break;
        case "as": $fielduse = 'core_e_dgroup_table_field_a'; $avedtype = "form"; break;
        case "ad": $fielduse = 'core_e_dgroup_table_field_a'; $avedtype = "form"; break;
        case "v": $fielduse = 'core_e_dgroup_table_field_v'; $avedtype = "form"; break;
        case "e": $fielduse = 'core_e_dgroup_table_field_e'; $avedtype = "form"; break;
        case "es": $fielduse = 'core_e_dgroup_table_field_e'; $avedtype = "form"; break;
        case "ed": $fielduse = 'core_e_dgroup_table_field_e'; $avedtype = "form"; break;
        case "d": $fielduse = 'core_e_dgroup_table_field_d'; $avedtype = "form"; break;
        case "ds": $fielduse = 'core_e_dgroup_table_field_d'; $avedtype = "form"; break;
        case "l": $fielduse = 'core_e_dgroup_table_field_l'; $avedtype = "list"; break;
        case "ld": $fielduse = 'core_e_dgroup_table_field_l'; $avedtype = "list"; break;
        case "s": $fielduse = 'core_e_dgroup_table_field_s'; $avedtype = "search"; break;
        case "sd": $fielduse = 'core_e_dgroup_table_field_s'; $avedtype = "search"; break;
        case "ss": $fielduse = 'core_e_dgroup_table_field_s'; $avedtype = "search"; break;
        case "so": $fielduse = 'core_e_dgroup_table_field_s'; $avedtype = "search"; break;
        case "sq": $fielduse = 'core_e_dgroup_table_field_sq'; $avedtype = "search"; break;
        case "f": $fielduse = 'core_e_dgroup_table_field_f'; $avedtype = "form"; break;
        case "fd": $fielduse = 'core_e_dgroup_table_field_f'; $avedtype = "form"; break;
        case "fs": $fielduse = 'core_e_dgroup_table_field_f'; $avedtype = "form"; break;
    }

    $structure['avedtype'] = $avedtype;

    $thisapp = $app;

    if ($result1){

        $sql2 = "SELECT * FROM core_e_dgroup_table WHERE core_e_dgroup_table_dgroupname = '$dgroupname' ORDER BY core_e_dgroup_table_sort ASC,core_e_dgroup_table_parent DESC";
		$result2 = $this->db->query($sql2);
		$result2 = $result2->result_array();
        //echo $sql2;

            if ($result2){
                $this_count2 = 0;
                $count_fielduse = 0;
                $multilang_count = 0;
                $fieldsort_count = 100;

                ////////////////////////////////////////////////////////////////////////////
                //
                // Open foreach table
                //
                foreach ($result2 as $field2) {
                    $structure['table'][$this_count2]['table'] = $field2['core_e_dgroup_table_table'];
                    $structure['table'][$this_count2]['isparent'] = $field2['core_e_dgroup_table_parent'];

                    //echo $field2['core_e_dgroup_table_app'];

                    if ($field2['core_e_dgroup_table_app']) {
                    $this->lang->loadarray($this->LangM->loadarray($field2['core_e_dgroup_table_app'], $this->lang->lang_use));
                    $app = $field2['core_e_dgroup_table_app'];
                    } else {
                    $app = $thisapp;
                    }

                    //echo $app;

                    //$sql3 = "SELECT core_form_fields_field FROM core_form_fields WHERE core_form_fields_form = '$dgroupname' AND core_form_fields_tablename = '".$structure['table'][$this_count2]['table']."' AND core_form_fields_use = '1'";
                    //$result3 = $db->fetchAll($sql3, 2);
                    //echo $avedtype;

                    switch ($avedtype){
                      case "form": $sortwhich = "core_e_dgroup_table_field_sortform"; break;
                      case "search": $sortwhich = "core_e_dgroup_table_field_sortsearch"; break;
                      case "list": $sortwhich = "core_e_dgroup_table_field_sortlist"; break;
                    }

					$sql = "SELECT * FROM core_e_dgroup_table_field LEFT JOIN core_db_fields ON";
					$sql .= " core_e_dgroup_table_field.core_e_dgroup_table_field_table = core_db_fields.core_db_fields_tablename";
					$sql .= " WHERE core_e_dgroup_table_field_dgroupname = '".$dgroupname."'";
					$sql .= " AND core_e_dgroup_table_field_table = '".$structure['table'][$this_count2]['table']."'";
					$sql .= " AND core_e_dgroup_table_field.core_e_dgroup_table_field_field = core_db_fields.core_db_fields_name ORDER BY $sortwhich";

					$result3 = $this->db->query($sql);
					$result3 = $result3->result_array();
                    $this_count3 = 0;
                    $count_listsel = 0;
                    $count_sq = 0; //search quick, use in aved=sq, create an array to hold the field used in sq

                    ////////////////////////////////////////////////////////////////////////////
                    //
                    // Open foreach field
                    //
                    foreach ($result3 as $field3) {
                    //$structure['table'][$this_count2]['fields'][$this_count3] = $field3;

                    //for each field, identify the input type
                    //put the neccessary field into to array result
                    //get the field name lang into array result
                    //for sel,chk,radio get the list of option into array result

                    /*
                    array("",""),
                    array("1","input"),
                    array("2","select"),
                    array("3","checkbox"),
                    array("4","textarea"),
                    array("5","radio"),
                    array("6","date"),
                    array("7","password")
                    */
                    if ($field3['core_e_dgroup_table_field_thisidform'] == 1) {
                    $structure['thisidform'] = $field3['core_e_dgroup_table_field_table'].".".$field3['core_e_dgroup_table_field_field'];
                    $structure['table'][$this_count2]['index'] = $field3['core_e_dgroup_table_field_field'];
                    $structure['list']['listkey'] = $field3['core_e_dgroup_table_field_field'];
                    }

                    if ($field3['core_e_dgroup_table_field_thisidlist']) {
                    $structure['thisidlist'][$field3['core_e_dgroup_table_field_thisidlist']] = $field3['core_e_dgroup_table_field_table'].".".$field3['core_e_dgroup_table_field_field'];
                    }

                    if ($field3['core_e_dgroup_table_field_linkchild']){
                    $structure['table'][$this_count2]['linkchild'][$field3['core_e_dgroup_table_field_linkchild']] = $field3['core_e_dgroup_table_field_table'].".".$field3['core_e_dgroup_table_field_field'];
                    }

                    if ($field3['core_e_dgroup_table_field_linkparent']){
                    $structure['table'][$this_count2]['linkparent'][$field3['core_e_dgroup_table_field_linkparent']] = $field3['core_e_dgroup_table_field_table'].".".$field3['core_e_dgroup_table_field_field'];
                    }

                    ////////////////////////////////////////////////////////////////////////////
                    //
                    // OPEN if use field
                    //
                    if ($field3[$fielduse]) {

                    $count_fielduse++;

                    if ($field3['core_e_dgroup_table_field_langc']) {

                         if ($field3['core_e_dgroup_table_field_langc'] == 1 || $field3['core_e_dgroup_table_field_langc'] == 2){
                         $structure['multilang'][$multilang_count]['table'] = $field3['core_e_dgroup_table_field_table'];
                         $structure['multilang'][$multilang_count]['tablenum'] = $this_count2;
                         $structure['multilang'][$multilang_count]['field'] = $field3['core_e_dgroup_table_field_field'];
                         $structure['multilang'][$multilang_count]['fieldnum'] = $this_count3;
                         $structure['multilang'][$multilang_count]['show'] = $field3['core_e_dgroup_table_field_langc'];
                         if ($field3[$sortwhich] == 0) { $field3[$sortwhich] = $fieldsort_count; $fieldsort_count++; }
                         $structure['multilang'][$multilang_count]['sort'] = $field3[$sortwhich];

                         $structure['table'][$this_count2]['fields'][$this_count3]['multilang'] = $field3['core_e_dgroup_table_field_langc'];
                         }elseif ($field3['core_e_dgroup_table_field_langc'] == 3){

                         $structure['e_xtra']['field'] = $field3['core_e_dgroup_table_field_field'];

                         $structure['table'][$this_count2]['fields'][$this_count3]['multilang'] = $field3['core_e_dgroup_table_field_langc'];
                         $count_fielduse--;
                         }

                    } else {

                     if ($field3[$sortwhich] == 0) { $field3[$sortwhich] = $fieldsort_count; $fieldsort_count++; }
                     $structure['fieldsort'][$field3['core_e_dgroup_table_field_field']]['sort'] = $field3[$sortwhich];
                     $structure['fieldsort'][$field3['core_e_dgroup_table_field_field']]['table'] = $field3['core_e_dgroup_table_field_table'];
                     $structure['fieldsort'][$field3['core_e_dgroup_table_field_field']]['tablenum'] = $this_count2;
                     $structure['fieldsort'][$field3['core_e_dgroup_table_field_field']]['fieldnum'] = $this_count3;

                    }

                     $structure['fieldlist'] .= $field3['core_e_dgroup_table_field_table'].".".$field3['core_e_dgroup_table_field_field'].",";
                     $structure['fieldlist_short'] .= $field3['core_e_dgroup_table_field_field'].",";

                     $structure['table'][$this_count2]['fields'][$this_count3]['sort'] = $field3[$sortwhich];

                     $structure['table'][$this_count2]['fields'][$this_count3]['core_db_fields_name'] = $field3['core_db_fields_name'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['db_type'] = $field3['core_db_fields_db_type'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['db_length'] = $field3['core_db_fields_db_length'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['db_defaultvalue'] = $field3['core_db_fields_db_defaultvalue'];

					$structure['table'][$this_count2]['fields'][$this_count3]['db_null'] = $field3['core_db_fields_db_null'];


                     //if ($avedtype == "form" || $avedtype == "list" || $avedtype == "search"){
                     /////////////////////////////////////////////////////////////////////////////
                     //FORM or SEARCH or LIST

                     //if no form type selected, default is input(text input)
                     if (($field3['core_db_fields_db_type'] == "date" || $field3['core_db_fields_db_type'] == "datetime") && !$field3['core_db_fields_form_fieldtype']){
                        $field3['core_db_fields_form_fieldtype']=6;
                     } elseif (!$field3['core_db_fields_form_fieldtype']) {
                        $field3['core_db_fields_form_fieldtype']=1;
                     }

                     $structure['table'][$this_count2]['fields'][$this_count3]['form_fieldtype'] = $field3['core_db_fields_form_fieldtype'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['form_required'] = $field3['core_db_fields_form_required'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['form_validate'] = $field3['core_db_fields_form_validate'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['form_validmin'] = $field3['core_db_fields_form_validmin'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['form_validpattern'] = $field3['core_db_fields_form_validpattern'];

                     $structure['table'][$this_count2]['fields'][$this_count3]['form_view'] = $field3['core_db_fields_form_view'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['form_add'] = $field3['core_db_fields_form_add'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['form_edit'] = $field3['core_db_fields_form_edit'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['form_del'] = $field3['core_db_fields_form_del'];

                     if ($avedtype == "search"){
                         $structure['table'][$this_count2]['fields'][$this_count3]['form_search'] = $field3['core_db_fields_form_search'];
                         $structure['table'][$this_count2]['fields'][$this_count3]['searchblank'] = $field3['core_db_fields_searchblank'];
                         $structure['table'][$this_count2]['fields'][$this_count3]['searchlike'] = $field3['core_db_fields_searchlike'];
                         $structure['table'][$this_count2]['fields'][$this_count3]['searchdate'] = $field3['core_db_fields_searchdate'];
                         $structure['table'][$this_count2]['fields'][$this_count3]['searchdateto'] = $field3['core_db_fields_searchdateto'];

                         if ($field3['core_e_dgroup_table_field_sq']) {
                            $structure['searchquickfield'][$count_sq] = $field3['core_e_dgroup_table_field_field'];
                            $count_sq++;
                         }
                     }

                     $structure['table'][$this_count2]['fields'][$this_count3]['form_name_lang'] = $this->lang->line($app.$field3['core_db_fields_name']);//echo $this->lang->line($app.$field3['core_db_fields_name']).$this->lang->language[$app.$field3['core_db_fields_name']];exit;
                     $name_lang__d = $field3['core_db_fields_name']."__d";
                     $structure['table'][$this_count2]['fields'][$this_count3]['form_name_lang__d'] = $this->lang->line($app.$name_lang__d);

                     $structure['table'][$this_count2]['fields'][$this_count3][''] = $field3['core_e_dgroup_table_field_seliconv'];

                     if ($field3['core_e_dgroup_table_field_imguse']){
                     $structure['table'][$this_count2]['fields'][$this_count3]['imguse'] = $field3['core_e_dgroup_table_field_imguse'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['imgpath'] = $field3['core_db_fields_imgpath'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['imgpre'] = $field3['core_db_fields_imgpre'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['imgsub'] = $field3['core_db_fields_imgsub'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['avatar'] = $field3['core_db_fields_avatar'];
                     }

                     $structure['table'][$this_count2]['fields'][$this_count3]['fnobr'] = $field3['core_e_dgroup_table_field_fnobr'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['fvnobr'] = $field3['core_e_dgroup_table_field_fvnobr'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['fwidth'] = $field3['core_e_dgroup_table_field_fwidth'];

                     $structure['table'][$this_count2]['fields'][$this_count3]['fnobrpre'] = $field3['core_e_dgroup_table_field_fnobrpre'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['fnobrsub'] = $field3['core_e_dgroup_table_field_fnobrsub'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['fvnobrpre'] = $field3['core_e_dgroup_table_field_fvnobrpre'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['fvnobrsub'] = $field3['core_e_dgroup_table_field_fvnobrsub'];

                     //$structure['table'][$this_count2]['fields'][$this_count3]['autoc'] = $field3['core_e_dgroup_table_field_autoc'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['fnolabel'] = $field3['core_e_dgroup_table_field_fnolabel'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['fvnolabel'] = $field3['core_e_dgroup_table_field_fvnolabel'];

                     $structure['table'][$this_count2]['fields'][$this_count3]['hide'] = $field3['core_e_dgroup_table_field_hide'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['fhidden'] = $field3['core_e_dgroup_table_field_fhidden'];

                     if ($field3['core_e_dgroup_table_field_autoc']) $structure['autocomplete'][$field3['core_e_dgroup_table_field_field']] = $field3['core_e_dgroup_table_field_autoc'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['autocomplete'] = $field3['core_e_dgroup_table_field_autoc'];

                     //char count javascript
                     $structure['table'][$this_count2]['fields'][$this_count3]['charcount'] = $field3['core_e_dgroup_table_field_charcount'];
                     //form edit - show but viewonly
                     $structure['table'][$this_count2]['fields'][$this_count3]['feviewonly'] = $field3['core_e_dgroup_table_field_feviewonly'];
                     //form edit - field on change call js
                     //use of this field changed, load js when loaded in aved=e
                     //if need a on change, put it in the js called
                     $structure['table'][$this_count2]['fields'][$this_count3]['feselonchgjs'] = $field3['core_e_dgroup_table_field_feselonchgjs'];

                     //when search SQL ORDER BY, ignore 0, ORDER BY [1] [2]
                     if ($field3['core_e_dgroup_table_field_searchorder']){
                     $this_searchsql_orderby[$field3['core_e_dgroup_table_field_searchorder']] = $field3['core_db_fields_name'];
                     }

                     if ($field3['core_e_dgroup_table_field_popbutton']){
                     $structure['table'][$this_count2]['fields'][$this_count3]['popbutton'] = $field3['core_e_dgroup_table_field_popbutton'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['popicon'] = $field3['core_e_dgroup_table_field_popicon'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['poplang'] = $field3['core_e_dgroup_table_field_poplang'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['popjs'] = $field3['core_e_dgroup_table_field_popjs'];
                     $structure['table'][$this_count2]['fields'][$this_count3]['popadddivname'] = $field3['core_e_dgroup_table_field_popadddivname'];
                     }


                     //echo $app;
                     //print_r($lang[$app]);
                     //echo $lang[$app][$field3['core_db_fields_name']];
                     //exit;

                     //$formvalue[$this_field['core_db_fields_name']]
                     //$distype
                     switch($field3['core_db_fields_form_fieldtype']){

                            //textarea
                            case "4":
                            $structure['table'][$this_count2]['fields'][$this_count3]['textarea_type'] = $field3['core_db_fields_textarea_type'];
                            $structure['table'][$this_count2]['fields'][$this_count3]['textarea_colrowwrap'] = $field3['core_db_fields_textarea_colrowwrap'];
                            ; break;

                            //password
                            case "7":
                            $structure['table'][$this_count2]['fields'][$this_count3]['pwformat'] = $field3['core_db_fields_form_pwformat'];
                            $structure['table'][$this_count2]['fields'][$this_count3]['lang_confirm'] = $this->lang->line('coreconfirm');
                            ; break;

                            //date
                            case "6":
                            //date(saved format),date_showformat,date_to
                            $structure['table'][$this_count2]['fields'][$this_count3]['date'] = $field3['core_db_fields_form_date'];
                            $structure['table'][$this_count2]['fields'][$this_count3]['date_showformat'] = $field3['core_db_fields_form_date_showformat'];
                            $structure['table'][$this_count2]['fields'][$this_count3]['date_to'] = $field3['core_db_fields_form_date_to'];
                            ; break;

                            //date
                            case "18":
                            //date(saved format),date_showformat,date_to
                            $structure['table'][$this_count2]['fields'][$this_count3]['date'] = $field3['core_db_fields_form_date'];
                            $structure['table'][$this_count2]['fields'][$this_count3]['date_showformat'] = $field3['core_db_fields_form_date_showformat'];
                            $structure['table'][$this_count2]['fields'][$this_count3]['date_to'] = $field3['core_db_fields_form_date_to'];
                            ; break;

                            //date
                            case "12":
                            //date(saved format),date_showformat,date_to
                            $structure['table'][$this_count2]['fields'][$this_count3]['date'] = $field3['core_db_fields_form_date'];
                            $structure['table'][$this_count2]['fields'][$this_count3]['date_showformat'] = $field3['core_db_fields_form_date_showformat'];
                            $structure['table'][$this_count2]['fields'][$this_count3]['date_to'] = $field3['core_db_fields_form_date_to'];
                            ; break;

                            //checkbox
                            case "3":
                            $structure['table'][$this_count2]['fields'][$this_count3]['chktype'] = $field3['core_db_fields_chktype'];
                            $structure['table'][$this_count2]['fields'][$this_count3]['chked'] = $field3['core_db_fields_chked'];
                            //get the lang
                            if ($field3['core_db_fields_chkname0'] && $field3['core_db_fields_chkname1']){
                            $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname0'] = $this->lang->line($app.$field3['core_db_fields_chkname0']);
                            $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname1'] = $this->lang->line($app.$field3['core_db_fields_chkname1']);
                            }else{

								$structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname0_icon'] = "";
								$structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname1_icon'] = "";

                                switch($field3['core_db_fields_chknametype']){
                                /*
                                array("0","No/Yes"),
                                array("1","Off/On"),
                                array("2","Disable/Enable"),
                                array("3","Inactive/Active"),
                                array("4","Valid/Void")
                                array("5","Nondefault/Default")
                                */
                                case "0":
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname0'] = $this->lang->line('coreno');
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname1'] = $this->lang->line('coreyes');
                                ; break;
                                case "1":
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname0'] = $this->lang->line('coreoff');
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname1'] = $this->lang->line('coreon');
                                ; break;
                                case "2":
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname0'] = $this->lang->line('coredisable');
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname1'] = $this->lang->line('coreenable');
                                ; break;
                                case "3":
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname0'] = $this->lang->line('coreinactive');
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname1'] = $this->lang->line('coreactive');
                                ; break;
                                case "4":
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname0'] = $this->lang->line('corevalid');
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname1'] = $this->lang->line('corevoid');
                                ; break;
                                case "5":
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname0'] = $this->lang->line('corenondefault');
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname1'] = $this->lang->line('coredefault');
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname0_icon'] = "";
                                $structure['table'][$this_count2]['fields'][$this_count3]['name_lang_chkname1_icon'] = "tick-small.png";
                                ; break;
                                }

                            //$structure['list']['chkbox'][$field3['core_e_dgroup_table_field_field']]=$field3['core_e_dgroup_table_field_field'];
                            $structure['list']['chkbox'][$field3['core_e_dgroup_table_field_field']]['tablecount']=$this_count2;
                            $structure['list']['chkbox'][$field3['core_e_dgroup_table_field_field']]['fieldcount']=$this_count3;

                            $structure['table'][$this_count2]['fields'][$this_count3]['seliconv'] = $field3['core_e_dgroup_table_field_seliconv'];

                            }
                            ; break;

                            //select
                            case "2":
                            //sellist,selmulti,seldefault,selblank
                            $select_getopt = $this->core_d_element_dgroup_select_getopt($field3,$app);
                            $structure['table'][$this_count2]['fields'][$this_count3]['sellist'] = $select_getopt['list'];

                            if ($field3['core_db_fields_seldefault']){
                            $structure['table'][$this_count2]['fields'][$this_count3]['seldefault'] = $field3['core_db_fields_seldefault'];
                            } elseif (isset($select_getopt['default'])) {
                            $structure['table'][$this_count2]['fields'][$this_count3]['seldefault'] = $select_getopt['default'];
                            }

                            $structure['table'][$this_count2]['fields'][$this_count3]['selmulti'] = $field3['core_db_fields_select'];
                            $structure['table'][$this_count2]['fields'][$this_count3]['selblank'] = $field3['core_db_fields_selblank'];

                            $structure['table'][$this_count2]['fields'][$this_count3]['seliconv'] = $field3['core_e_dgroup_table_field_seliconv'];
                            $structure['table'][$this_count2]['fields'][$this_count3]['seliconf'] = $field3['core_e_dgroup_table_field_seliconf'];
                            //echo $field3['core_e_dgroup_table_field_seliconv']; echo "1";
                            //$structure['list']['sellist'][$field3['core_e_dgroup_table_field_field']]=$field3['core_e_dgroup_table_field_field'];
                            $structure['list']['sellist'][$field3['core_e_dgroup_table_field_field']]['tablecount']=$this_count2;
                            $structure['list']['sellist'][$field3['core_e_dgroup_table_field_field']]['fieldcount']=$this_count3;
                            //echo $structure['list']['sellist'][$field3['core_e_dgroup_table_field_field']]['tablecount'];
                            ; break;

                            //select (button)
                            case "11":

                            //change the type back to 2,
                            $structure['table'][$this_count2]['fields'][$this_count3]['form_fieldtype'] = "2";
                            $structure['table'][$this_count2]['fields'][$this_count3]['selbutton'] = "1";

                            //sellist,selmulti,seldefault,selblank
                            $select_getopt = $this->core_d_element_dgroup_select_getopt($field3,$app);
                            $structure['table'][$this_count2]['fields'][$this_count3]['sellist'] = $select_getopt['list'];

                            if ($field3['core_db_fields_seldefault']){
                            $structure['table'][$this_count2]['fields'][$this_count3]['seldefault'] = $field3['core_db_fields_seldefault'];
                            } elseif (isset($select_getopt['default'])) {
                            $structure['table'][$this_count2]['fields'][$this_count3]['seldefault'] = $select_getopt['default'];
                            }

                            $structure['table'][$this_count2]['fields'][$this_count3]['selmulti'] = $field3['core_db_fields_select'];
                            $structure['table'][$this_count2]['fields'][$this_count3]['selblank'] = $field3['core_db_fields_selblank'];

                            $structure['table'][$this_count2]['fields'][$this_count3]['seliconv'] = $field3['core_e_dgroup_table_field_seliconv'];
                            $structure['table'][$this_count2]['fields'][$this_count3]['seliconf'] = $field3['core_e_dgroup_table_field_seliconf'];

                            //$structure['list']['sellist'][$field3['core_e_dgroup_table_field_field']]=$field3['core_e_dgroup_table_field_field'];
                            $structure['list']['sellist'][$field3['core_e_dgroup_table_field_field']]['tablecount']=$this_count2;
                            $structure['list']['sellist'][$field3['core_e_dgroup_table_field_field']]['fieldcount']=$this_count3;
                            //echo $structure['list']['sellist'][$field3['core_e_dgroup_table_field_field']]['tablecount'];
                            ; break;


                        }

                    //}else
                    if ($avedtype == "list" || $aved == "ss" || $aved == "sq"){
                     /////////////////////////////////////////////////////////////////////////////
                     //LIST
                            $structure['field'][$field3['core_e_dgroup_table_field_field']]['tablecount']=$this_count2;
                            $structure['field'][$field3['core_e_dgroup_table_field_field']]['fieldcount']=$this_count3;

                            //select with button fix
                            if ($field3['core_db_fields_form_fieldtype'] == "11") $field3['core_db_fields_form_fieldtype'] = 2;


                            $structure['listarray'][$field3['core_e_dgroup_table_field_field']]['list_formtype'] = $field3['core_db_fields_form_fieldtype'];
                            $structure['listarray'][$field3['core_e_dgroup_table_field_field']]['list_langc'] = $field3['core_e_dgroup_table_field_langc'];

                            $structure['list']['formtype'] .= $field3['core_db_fields_form_fieldtype'].",";

                            //$structure['list']['langc'] .= $field3['core_e_dgroup_table_field_langc'].",";
                            //$structure['tablelist'][$field3['core_list_fields_tablename']]['fieldlist'] .= $field3['core_list_fields_field'].",";

                            //$result['fieldlist'] .= $field3['core_list_fields_tablename'].".".$field3['core_list_fields_field'].",";

                            if ($field3['core_e_dgroup_table_field_lwidth'] != 0) {
                            $structure['list']['listwidth'] .= $field3['core_e_dgroup_table_field_lwidth'].",";
                            $structure['listarray'][$field3['core_e_dgroup_table_field_field']]['list_width'] = $field3['core_e_dgroup_table_field_lwidth'];
                            }else{
                            $structure['list']['listwidth'] .= "*,";
                            $structure['listarray'][$field3['core_e_dgroup_table_field_field']]['list_width'] = "*";
                            }

                            if ($field3['core_e_dgroup_table_field_lcoltype'] != "") {
                            $structure['list']['listcoltype'] .= $field3['core_e_dgroup_table_field_lcoltype'].",";
                            $structure['listarray'][$field3['core_e_dgroup_table_field_field']]['list_coltype'] = $field3['core_e_dgroup_table_field_lcoltype'];
                            }else{
                            $structure['list']['listcoltype'] .= "ro,";
                            $structure['listarray'][$field3['core_e_dgroup_table_field_field']]['list_coltype'] = "ro";
                            }

                            if ($field3['core_e_dgroup_table_field_lsorttype'] != "") {
                            $structure['list']['listsorttype'] .= $field3['core_e_dgroup_table_field_lsorttype'].",";
                            $structure['listarray'][$field3['core_e_dgroup_table_field_field']]['list_sorttype'] = $field3['core_e_dgroup_table_field_lsorttype'];
                            }else{
                            $structure['list']['listsorttype'] .= "str,";
                            $structure['listarray'][$field3['core_e_dgroup_table_field_field']]['list_sorttype'] = "str";
                            }

                            if ($field3['core_e_dgroup_table_field_ltxtsearch'] != "") {
                            $structure['list']['listtxtsearch'] .= $field3['core_e_dgroup_table_field_ltxtsearch'].",";
                            $structure['listarray'][$field3['core_e_dgroup_table_field_field']]['list_txtsearch'] = $field3['core_e_dgroup_table_field_ltxtsearch'];
                            }else{
                            $structure['list']['listtxtsearch'] .= ",";
                            }


                            if ($field3['core_e_dgroup_table_field_lcoltype'] == "co" || $field3['core_e_dgroup_table_field_lcoltype'] == "coro") {

                                //global $DOCUMENT_ROOT;
                                //include_once $DOCUMENT_ROOT.'/includes/core/data_form.inc';
                                $thiscombolist = core_d_element_dgroup_select_getopt($field3,$app);
                                //exit;
                                echo $thiscombolist['list'];
                                $structure['list']['listcombo'][$this_count3]['items'] = $thiscombolist['list'];
                                $structure['list']['listcombo'][$this_count3]['index'] = $this_count3;

                                $structure['listarray'][$field3['core_e_dgroup_table_field_field']]['list_coltype_list'] = $thiscombolist['list'];
                                $structure['listarray'][$field3['core_e_dgroup_table_field_field']]['list_coltype_index'] = $this_count3;
                            }


                            $structure['list']['listname'] .= $this->lang->line($app.$field3['core_db_fields_name']).",";
                            $structure['listarray'][$field3['core_e_dgroup_table_field_field']]['listname'] = $this->lang->line($app.$field3['core_db_fields_name']);
                            $structure['listarray'][$field3['core_e_dgroup_table_field_field']]['sort'] = $field3[$sortwhich];

                            uasort($structure['listarray'], array($this, 'core_element_dgroup_sort'));
                        }

                    $this_count3++;
                    }
                    //////////////////////////////////////////////////////////////////////////////////////////
                    //
                    // Enf if use
                    //

                    }
                    //////////////////////////////////////////////////////////////////////////////////////////
                    //
                    // Close foreach field
                    //
                $this_count2++;
                }
                //////////////////////////////////////////////////////////////////////////////////////////
                //
                // Close foreach table
                //

                if ($avedtype == "list" || $aved == "ss" || $aved == "sq"){
                $structure['list']['listwidth'] = substr($structure['list']['listwidth'], 0, -1);
                $structure['list']['listcoltype'] = substr($structure['list']['listcoltype'], 0, -1);
                $structure['list']['listname'] = substr($structure['list']['listname'], 0, -1);
                $structure['list']['listsorttype'] = substr($structure['list']['listsorttype'], 0, -1);
                $structure['list']['listtxtsearch'] = substr($structure['list']['listtxtsearch'], 0, -1);
                $structure['list']['formtype'] = substr($structure['list']['formtype'], 0, -1);
                $structure['list']['langc'] = substr($structure['list']['langc'], 0, -1);
                }
                $structure['fieldlist'] = substr($structure['fieldlist'], 0, -1);
                $structure['fieldlist_short'] = substr($structure['fieldlist_short'], 0, -1);
                $structure['fielduse_count'] = $count_fielduse;


            }else{
               $error .= "<br>Missing Table In: ".$dgroupname;
            }




            /////////////////////////////////////////////////////////////////////////////
            //format the multilang + fieldsort
            /////////////////////////////////////////////////////////////////////////////

            if(isset($structure['multilang'])){
            foreach ($structure['multilang'] as $this_multilang) {

                $sqlml = "SELECT * FROM lang_use WHERE lang_use_active = '1'";
                if ($this_multilang['show'] == 1) $sqlml .= " AND lang_use_code = '".$this->lang->lang_use."'";
                $sqlml .= " ORDER BY lang_use_code";

				$resultml = $this->db->query($sqlml);
				$resultml = $resultml->result_array();

                foreach ($resultml as $fieldml) {
                    $this_fieldname = $this_multilang['field']."_".$fieldml['lang_use_code'];
                    if (!isset($structure['fieldsort'][$this_fieldname])){
                      $structure['fieldsort'][$this_fieldname]['sort'] = $this_multilang['sort'];
                      $structure['fieldsort'][$this_fieldname]['table'] = $this_multilang['table'];
                      $structure['fieldsort'][$this_fieldname]['field'] = $this_multilang['field'];
                      $structure['fieldsort'][$this_fieldname]['tablenum'] = $this_multilang['tablenum'];
                      $structure['fieldsort'][$this_fieldname]['fieldnum'] = $this_multilang['fieldnum'];
                      //$dgroup_structure['fieldsort'][$this_fieldname]['show'] = $this_multilang['show'];
                      $structure['fieldsort'][$this_fieldname]['lang'] = $fieldml['lang_use_code'];
                      if ($this_multilang['show'] == 2) $structure['fieldsort'][$this_fieldname]['lang_name'] = $this->lang->langinfo['langlist'][$fieldml['lang_use_code']];
                    }
                }

            }
            }

            if (isset($structure['fieldsort'])) uasort($structure['fieldsort'], array( $this, 'core_element_dgroup_sort'));

            /////////////////////////////////////////////////////////////////////////////
            //format the sql
            /////////////////////////////////////////////////////////////////////////////

            /////////////////////////////////////////////////////////////////////////////
            //form the sql
            $structure['sql'] = "SELECT * FROM ".$structure['table'][0]['table'];
                $thislink_count = 1;$thislink_count2 = 0;

				while (isset($structure['table'][$thislink_count])) {
	                $structure['sql'] .= " LEFT JOIN ".$structure['table'][$thislink_count]['table']." ON ";
	                        //echo $structure['table'][$this_count2]['linkchild'][0];

	                        $thisparentcount = 1;
	                        while (isset($structure['table'][$thislink_count2]['linkchild'][$thisparentcount]) && isset($structure['table'][$thislink_count]['linkparent'][$thisparentcount])){
	                        if ($thisparentcount > 1) $structure['sql'] .= " AND ";
	                        $structure['sql'] .= $structure['table'][$thislink_count2]['linkchild'][$thisparentcount] ." = ". $structure['table'][$thislink_count]['linkparent'][$thisparentcount];
	                        $thisparentcount++;
	                        }

	                $thislink_count++;$thislink_count2++;
	            }


                if ($structure['thisidreq'] && !$thisid) meg(999,"List: thisid missing. [dgroup:".$dgroupname."] [".$app."]");

                if ($aved == "ss" || $aved == "sq" || $aved == "so") $structure['searchsql'] = $structure['sql'];

                //FOR search sql - ORDER BY
                if (isset($this_searchsql_orderby)){
                  foreach ($this_searchsql_orderby as $this_searchsql_orderby_field){
                  $structure['searchsql_orderby'] .= $this_searchsql_orderby_field.",";
                  }
                  $structure['searchsql_orderby'] = " ORDER BY ".substr($structure['searchsql_orderby'], 0, -1);;
                }

                $structure_sqlwhere = " WHERE ";

                if ($structure['avedtype'] == "form"){
                    if ($structure['thisidform'] && $thisid) { $structure['sql'] .= $structure_sqlwhere.$structure['thisidform']." = ".$thisid; }
                } elseif ($structure['avedtype'] == "list") {
                    if ($structure['thisidlist'][1] && $thisid) { $structure['sql'] .= $structure_sqlwhere.$structure['thisidlist'][1]." = ".$thisid; $structure_sqlwhere = " AND "; }
                }

                if (isset($structure['extend_where']) && $structure['extend_where'] != "") { $structure['sql'] .= $structure_sqlwhere.$structure['extend_where']; $structure_sqlwhere = " AND "; }

                //list and it's type is set to parentstyle
                //if the thisid is ZERO, then the WHERE statement must set to "=0" to prevent showing all field
                if ($structure['listparentstyle'] && !$thisid) { $structure['sql'] .= $structure_sqlwhere.$structure['thisidlist'][1]." = 0"; $structure_sqlwhere = " AND "; }

    } else {
        $error .= "<br>Missing DGROUP: ".$dgroupname;
    }

    if (isset($error)) {echo $error; exit;}

return ($structure);
}


function core_d_element_dgroup_select_getopt($this_field,$app){

    //get the sql
    if ($this_field['core_db_fields_seldb']){
        $this_array = explode(",", $this_field['core_db_fields_seldb']);

        if (!isset($this_array[1])){

        $core_app_id2name_sql = $this->App_generalM->core_app_id2name_sql($this_array[0]);

        $sql1 = $core_app_id2name_sql['sql'];
        $this_key = $core_app_id2name_sql['this_key'];
        $use_core_app_id2name = 1;

        } else {

            //core select only
            $sqlget_selectgp = "";
            if ($this_array[0] == "core_select") $sqlget_selectgp = ",core_select_group,core_select_app,core_select_icon16,core_select_icon32";

            $sql1 = "SELECT ".$this_array[1].",".$this_array[2].$sqlget_selectgp." FROM ".$this_array[0];
            if (isset($this_array[3])) $sql1 .= " WHERE ".$this_array[3]." = '".$this_field['core_db_fields_selname']."'";
            if ($this_field['core_db_fields_selorderby']) { $sql1 .= " ORDER BY ".$this_field['core_db_fields_selorderby'];
            } else { $sql1 .= " ORDER BY ".$this_array[1]; }
            $this_name = $this_array[1];
            $this_key = $this_array[2];
            $this_lang_prefix = $this_array[0]."_";
            //this is for lang, if use the default select table, ignore the $app that passed into this function
            //default select table lang are from the core, any other belongs to the apps
            //$app = "core";
        }


    }elseif(!$this_field['core_db_fields_selsql']){
        $sql1 = "SELECT ".$this_field['core_db_fields_selthename'].",".$this_field['core_db_fields_selthekey']." FROM ".$this_field['core_db_fields_seltable'];
        if ($this_field['core_db_fields_selkey']) $sql1 .= " WHERE ".$this_field['core_db_fields_selkey']." = '".$this_field['core_db_fields_selname']."'";
        if ($this_field['core_db_fields_selorderby']) { $sql1 .= " ORDER BY ".$this_field['core_db_fields_selorderby'];
        } else { $sql1 .= " ORDER BY ".$this_field['core_db_fields_selthename']; }
        $this_name = $this_field['core_db_fields_selthename'];
        $this_key = $this_field['core_db_fields_selthekey'];
    }elseif($this_field['core_db_fields_selsql']){
        $sql1 = $this_field['core_db_fields_selsql'];
        $this_name = $this_field['core_db_fields_selthename'];
        $this_key = $this_field['core_db_fields_selthekey'];
    }

        /////////////////////////////////////
        // create the list array
        $list_count = 0;
        //if blank sel enable, add a blank field
        if ($this_field['core_db_fields_selblank']) {
            $resultlist['list'][$list_count]['key'] = '0';
            $resultlist['list'][$list_count]['name'] = '';
            $list_count++;
        }

		$result1 = $this->db->query($sql1);
		$result1 = $result1->result_array();
        foreach ($result1 as $field1) {

            //if there is a default field given and this field is != 0, set this field as default and return
            //if (isset($field1['this_defaultfield'])) $resultlist['default'] = $field1[$this_key];

            if ($this_field['core_db_fields_notshow_keyname']){
                $do_not_show = 0;
                $this_countkey = 0;
                $this_arraykey = explode(",", $this_field['core_db_fields_notshow_keyname']);
                while ($this_arraykey[$this_countkey]){
                    if ($field1[$this_array[2]] == $this_arraykey[$this_countkey]) $do_not_show = 1;
                    $this_countkey++;
                }
            }

            if (isset($this_array[0]) && $this_array[0] == "core_select"){
                //core select
                $this_lang_name = $this_lang_prefix.$field1['core_select_group']."_".$field1[$this_name];
                if (!isset($appset)){
                    if ($field1['core_select_app']) {
                        $app = $field1['core_select_app'];
                        $appset = 1;
                    } else {
                        $app = 'core';
                        $appset = 1;
                    }
                }
                //echo $this_lang_name.$app;
                $this_show_lang_name = $this->lang->line($app.$this_lang_name);
                $this_icon16 = $field1['core_select_icon16'];
                $this_icon32 = $field1['core_select_icon32'];

            }elseif(isset($use_core_app_id2name) && $use_core_app_id2name){

                $this_show_lang_name = $this->App_generalM->core_app_id2name_format($core_app_id2name_sql,$field1);

            }elseif (isset($this_lang_prefix) && $this_lang_prefix) {
                $this_lang_name = $this_lang_prefix.$field1[$this_name];
                $this_show_lang_name = $this->lang->line($app.$this_lang_name);
            }else {
                $this_lang_name = $this_field['core_db_fields_seltable']."_".$field1[$this_name];
                $this_show_lang_name = $this->lang->line($app.$this_lang_name);
            }

            if (!isset($this_show_lang_name) && !$this_show_lang_name) {
                $this_show_lang_name = $field1[$this_name];
            }

            if (!isset($do_not_show)) {
                $this_show_lang_name = preg_replace('/,/', '&#44;', $this_show_lang_name);
                $resultlist['list'][$list_count]['key'] = $field1[$this_key];
                $resultlist['list'][$list_count]['name'] = $this_show_lang_name;
                if (isset($this_icon16)) $resultlist['list'][$list_count]['icon16'] = $this_icon16;
                if (isset($this_icon32)) $resultlist['list'][$list_count]['icon32'] = $this_icon32;
            }

            $list_count++;
        }

return($resultlist);
}





function core_element_dgroup_value($dgroup_structure){

    // this function will return all the value of a given form
    // pass in the dgroupname (array of the tables in the form, the array of fields) generated by core_data_form
    // pass the thisid, id for the "isparent" table indexfield

        ///////////////////////////////////////////////////////////////////
        // Get the value for miltilang (langc = 1 or 2)
        // value stored in table: langc
        $multilangresult = array();
        if(isset($dgroup_structure['multilang'])){
        foreach ($dgroup_structure['multilang'] as $this_multilang) {

            $sql2 = "SELECT langc_lang,langc_value FROM langc WHERE langc_tableid = '".$this->url['id_plain']."' AND langc_table = '".$this_multilang['table']."' AND langc_field = '".$this_multilang['field']."'";
            if ($this_multilang['show'] == 1) $sql2 .= " AND langc_lang = '".$this->lang->lang_use."'";
            $sql2 .= " ORDER BY langc_lang";

			$result2 = $this->db->query($sql2);
			$result2 = $result2->result_array();

            if ($result2){
                foreach ($result2 as $field2) {
                  $this_fieldname = $this_multilang['field']."_".$field2['langc_lang'];
                  $multilangresult[$this_fieldname] = $field2['langc_value'];
                }
            }

            $this_fieldname = $this_multilang['field']."_en";
            if (!$multilangresult[$this_fieldname]){
            $sql = preg_replace("/\*/", $this_multilang['field'], $dgroup_structure['sql']);
			$sqlresult = $this->db->query($sql);
			$sqlresult = $sqlresult->row_array(0);

            if ($sqlresult) $multilangresult[$this_fieldname] = $sqlresult[0];
            }

        }
        }

        ///////////////////////////////////////////////////////////////////
        // e_xtra: Get the value for extra field with multilang (langc = 3)
        // value stored in table: core_e_xtra_value
        if (isset($dgroup_structure['e_xtra'])){
            foreach ($dgroup_structure['table'] as $this_table){
                if (isset($this_table['e_xtra'])){

$sql3 = "SELECT core_e_xtra_value.core_e_xtra_value_value,core_e_xtra_field.core_e_xtra_field_id FROM core_e_xtra_value
LEFT JOIN core_e_xtra_field ON core_e_xtra_value.core_e_xtra_value_fieldid = core_e_xtra_field.core_e_xtra_field_id
WHERE core_e_xtra_value.core_e_xtra_value_linkid = '".$this->url['id_plain']."'
AND core_e_xtra_value.core_e_xtra_value_gpid = '".$this_table['e_xtra_gpid']."'
AND core_e_xtra_value.core_e_xtra_value_lang = '".$this_table['e_xtra_lang']."'";
$result3 = $this->db->query($sql3);
$result3 = $result3->result_array();

                    if (!$result3 && $aved == "v" && !$this->lang->langinfo['thislang_get']){
$sql3 = "SELECT core_e_xtra_value.core_e_xtra_value_value,core_e_xtra_field.core_e_xtra_field_id FROM core_e_xtra_value
LEFT JOIN core_e_xtra_field ON core_e_xtra_value.core_e_xtra_value_fieldid = core_e_xtra_field.core_e_xtra_field_id
WHERE core_e_xtra_value.core_e_xtra_value_linkid = '".$this->url['id_plain']."'
AND core_e_xtra_value.core_e_xtra_value_gpid = '".$this_table['e_xtra_gpid']."'
AND core_e_xtra_value.core_e_xtra_value_lang = '".$this->lang->langinfo['default']."'";
$result3 = $this->db->query($sql3);
$result3 = $result3->result_array();

                    }

                    foreach ($result3 as $field3) {
                    $this_fieldname = "e_xtra_field_".$field3['core_e_xtra_field_id'];
                    $multilangresult[$this_fieldname] = $field3['core_e_xtra_value_value'];
                    }
                    //echo "hello1".$dgroup_structure['fieldsort'][$this_fieldname]['e_xtra_shared']."-".$multilangresult[$this_fieldname];
                    foreach ($this_table['fields'] as $this_field){
                        if ($dgroup_structure['fieldsort'][$this_field['core_db_fields_name']]['e_xtra_shared'] && !$multilangresult[$this_field['core_db_fields_name']]){
                        // echo  "hello";
$sql3 = "SELECT core_e_xtra_value.core_e_xtra_value_value,core_e_xtra_field.core_e_xtra_field_id FROM core_e_xtra_value
LEFT JOIN core_e_xtra_field ON core_e_xtra_value.core_e_xtra_value_fieldid = core_e_xtra_field.core_e_xtra_field_id
WHERE core_e_xtra_value.core_e_xtra_value_linkid = '".$this->url['id_plain']."'
AND core_e_xtra_value.core_e_xtra_value_gpid = '".$this_table['e_xtra_gpid']."'
AND core_e_xtra_value.core_e_xtra_value_lang = 'en'
AND core_e_xtra_value.core_e_xtra_value_fieldid = '".$this_field['e_xtra_fieldid']."'";
$result3 = $this->db->query($sql3);
$result3 = $result3->row_array(0);

                        $multilangresult[$this_field['core_db_fields_name']] = $result3[0];
                        }
                    }

                }
            }
        }

        ///////////////////////////////////////////////////////////////////
        // get the value from proper table
        // value store in individual table
        $sql = preg_replace("/\*/", $dgroup_structure['fieldlist'], $dgroup_structure['sql']);
		$sqlresult = $this->db->query($sql);
		$sqlresult = $sqlresult->row_array(0);

        if ($sqlresult && $multilangresult) {
        $result = array_merge($sqlresult, $multilangresult);
        }else{
        $result = $sqlresult;
        }

return ($result);
}



function core_element_dgroup_sort($a, $b)
{
    return $a['sort'] - $b['sort'];
}


function core_element_dgroup_select_show($sellist,$value,$icon=0){

     if ($sellist){
         foreach ($sellist as $thisoption){
             if($thisoption['key'] == $value) {
                if (!$icon)  {
                    $result = $thisoption['name'];
                } else  {
                    if ($icon && $thisoption['icon16']) {
                        $result['icon'] = $thisoption['icon16'];
                        $result['icontype'] = '16';
                        $result['name'] = $thisoption['name'];
                    } elseif ($icon && $thisoption['icon32']) {
                        $result['icon'] = $thisoption['icon32'];
                        $result['icontype'] = '32';
                        $result['name'] = $thisoption['name'];
                    } else {
                        $result['name'] = $thisoption['name'];
                    }
                }
             }
         }
     }

     //}

//echo $result; exit;

if (isset($result)) return($result);
}

function core_element_dgroup_chkbox_show($opt0,$opt1,$value,$icon=0,$opt0icon=0,$opt1icon=0){

        if ($value){
           if (!$icon)  {
              return ($opt1);
           } else {
              $result['icon'] = $opt1icon;
              $result['name'] = $opt1;
              return($result);
           }
        } else {
           if (!$icon)  {
              return ($opt0);
           } else {
              $result['icon'] = $opt0icon;
              $result['name'] = $opt0;
              return($result);
           }
        }

}


}

