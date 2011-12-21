<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Element_dgroup_list extends Element_dgroup {

	function __construct() {
		parent::__construct();
	}


function dgroup_list($dgroup_structure,$dgroup_value,$element_button,$searcharray=array(),$is_fdata=0){

	global $db,$langinfo,$aved,$thisid,$thisid_en;

    $thisidform = explode(".", $dgroup_structure['thisidform']);


    //
    // button
    //
    //from element_button
    //seprate them into
    //new $element_button and $element_button_row
	$new_ele_bu = array();
	$element_button_row = array();

	if ($element_button) {
	foreach ($element_button['buttons'] as $this_button) {

		//format the name of the button
		//use the field name in a button
		//like "back to [parent field name]", example use in product app
		$button_useit = 1;
		if ($dgroup_structure['listparentstyle'] && !$thisid[0]) {
			//if $dgroup_structure['listparentstyle']
			//button type "parent"
			//if we are already at the top, we want to remove the "go to parent button
			if ($this_button['type'] == "parent"){
				$button_useit = 0;
		    }

		}elseif($dgroup_structure['listparentstyle'] && $dgroup_structure['listparentnamefield']){
	        //if there is a button type parent, and parentnamefield is defined in dgroup element setting
	        //and there is XXparentname set in land or lang__d of the button
	        //type to replace XXparentname with the real parent name
	        if ($this_button['type'] == "parent"){
	            if(preg_match("/XXparentname/",$this_button['lang']) || preg_match("/XXparentname/",$this_button['lang__d'])){
	                if (!$this_parentname){
	                $this_parentid = explode(".", $dgroup_structure['thisidform']);
	                $parentcatid = $db->fetchOne('SELECT '.$dgroup_structure['thisidlist'][1].' FROM '.$this_parentid[0].' WHERE '.$this_parentid[1].' = '.$thisid[0]);
	                $this_parentname = $db->fetchOne('SELECT '.$dgroup_structure['listparentnamefield'].' FROM '.$this_parentid[0].' WHERE '.$this_parentid[1].' = '.$parentcatid);
	                }
	                if ($parentcatid == 0 && !$this_parentname){
	                $this_button['lang'] = preg_replace("/XXparentname/",$dgroup_structure['listparenttop_lang'], $this_button['lang']);
	                $this_button['lang__d'] = preg_replace("/XXparentname/",$dgroup_structure['listparenttop_lang'], $this_button['lang__d']);
	                } elseif ($this_parentname){
	                $this_button['lang'] = preg_replace("/XXparentname/",$this_parentname, $this_button['lang']);
	                $this_button['lang__d'] = preg_replace("/XXparentname/",$this_parentname, $this_button['lang__d']);
	                }
	            }
	        }
		}

		//create a new array for $element_button_row
		//do not add these buttons in new $element_button
		if ($this_button['position'] == "rowend" || $this_button['position'] == "rowstart" || $this_button['position'] == "rowone"){
			$element_button_row[] = $this_button;
			$button_useit = 0;
		}

		//mark which button has to replace "rowid" to the thisid field
		//for fewer code later
		$count = 0;
		$button_row_ridpos = array();
		while (isset($element_button_row[$count])){

			if ($element_button_row[$count]['targetid'] == "rid"){
				$button_row_ridpos[] = $count;
			}elseif($element_button_row[$count]['targetid'] == "thisid"){
				$fieldreplace_usethisid = 1;
			}

		$count++;
		}


		if ($button_useit) {

          	//check button lang if FIELD is used, replace if so
			if (isset($element_button['listtitle'])) {
				if (preg_match("/FIELD/",$this_button['lang'])){

					$this_fieldname = explode("FIELD", $this_button['lang'],3);
					if ($thisid[0]){
						$this_fieldname2 = $db->fetchOne('SELECT '.$this_fieldname[1].' FROM '.$thisidform[0].' WHERE '.$thisidform[1].' = '.$thisid[0]);
						$this_button['lang'] = $this_fieldname[0].$this_fieldname2.$this_fieldname[2];
					}else{
						$this_button['lang'] = $this_fieldname[0].$this_fieldname[2];
					}

				}
			}


		    if ($this_button['targetid'] == "parentid") {

				if (!$parentid){
			        $this_parentid = explode(".", $dgroup_structure['thisidform']);
			        $parentid = $db->fetchOne('SELECT '.$dgroup_structure['thisidlist'][1].' FROM '.$this_parentid[0].' WHERE '.$this_parentid[1].' = '.$thisid[0]);
			        $parentid = f_thisid_encode($parentid);
				}
				$this_button['parentid'] = $parentid;
		    }

		$new_ele_bu[] = $this_button;
		}

	}
	$element_button['buttons'] = $new_ele_bu;
	}


	//ROW BUTTON

	//
	//load $rid fieldname
    //
    $this_ridfield_array = explode(".", $dgroup_structure['thisidform']);
    if (!preg_match("/".$dgroup_structure['thisidform'].",/",$dgroup_structure['fieldlist']) && !preg_match("/,".$dgroup_structure['thisidform']."/",$dgroup_structure['fieldlist'])) {
    $dgroup_structure['fieldlist'] = $dgroup_structure['fieldlist'].",".$dgroup_structure['thisidform'];
    $dgroup_structure['fieldlist_short'] = $dgroup_structure['fieldlist_short'].",".$dgroup_structure['thisidform'];
    $this_ridfield = $this_ridfield_array[1];
    $this_ridshow = 0;
    $this_riduse = 1;
    }else{
    $this_ridfield = $this_ridfield_array[1];
    $this_ridshow = 1;
    $this_riduse = 1;
    }

	//from element_button_row
	//pull all the FIELD (to be replaced with value)
	//output $fieldreplace
	$count = 0;
	$fieldreplace = array();
	$fieldreplace_thisid = "";
	$fieldreplace_usethisid = "";
	$fieldreplace_targetvalue = array();

	$fieldreplace = array();
	while (isset($element_button_row[$count])){

		if (substr($element_button_row[$count]['targetid'],0,5) == "FIELD") {
			$fieldreplace[] = $element_button_row[$count]['targetid_field'] = $fieldreplace_thisid = substr($element_button_row[$count]['targetid'],5);

		}

		if (isset($element_button_row[$count]['targetvalue']) && preg_match("/FIELD/",$element_button_row[$count]['targetvalue'])){
        	$this_targetvalue_array = explode("FIELD", $element_button_row[$count]['targetvalue']);
            foreach ($this_targetvalue_array as $this_targetvalue) {
            	$this_targetvalue_name = explode("XX", $this_targetvalue);
            	$fieldreplace[] = $element_button_row[$count]['targetvalue_field'][] = $fieldreplace_targetvalue[] = $this_targetvalue_name[0];
            }

		}

	$count++;
	}

	//check for all fields inside $fieldreplace are in the fieldlist
	//if no, add to the fieldlist, as fieldlist is used to generate the SQL statement
	//if not in field list the value will not be loaded from DB
	foreach ($fieldreplace as $this_fieldreplace){
            if (!preg_match("/".$this_fieldreplace.",/",$dgroup_structure['fieldlist']) && !preg_match("/.".$this_fieldreplace."/",$dgroup_structure['fieldlist'])) {
            $dgroup_structure['fieldlist'] = $dgroup_structure['fieldlist'].",".$this_valuereplace['field'];
            $dgroup_structure['fieldlist_short'] = $dgroup_structure['fieldlist_short'].",".$this_valuereplace['field'];
            }
	}




	//
    // come up with sql to get the real data
    //
    if ($aved == "ss" || $aved == "sq" || $is_fdata){

        if ($aved == "sq" || $is_fdata){

            global $_POST;
            if ($_POST['searchquickvalue']){
            $searchquickvalue = $_POST['searchquickvalue'];
            } else {
            global $_GET;
            $searchquickvalue = $_GET['term'];
            }

            foreach($dgroup_structure['searchquickfield'] as $this_sqfield){
                $searcharray[$this_sqfield] = $searchquickvalue;
            }
            $sqlwhereandor = " OR ";

        } else {

            $sqlwhereandor = " AND ";

        }

        $sqlwhere = "";
        $sqlwherecont = " WHERE ";
        foreach (array_keys($searcharray) as $fieldname){
            if($searcharray[$fieldname]) { $sqlwhere .= $sqlwherecont.$fieldname." LIKE '%".$searcharray[$fieldname]."%'"; $sqlwherecont = $sqlwhereandor;}
        }

        $sql1 = preg_replace("/\*/", $dgroup_structure['fieldlist'],$dgroup_structure['searchsql']);
        $sql1 = $sql1.$sqlwhere.$dgroup_structure['searchsql_orderby'];

    }else{

        $sql1 = preg_replace("/\*/", $dgroup_structure['fieldlist'],$dgroup_structure['sql']);

    }

    $data_list = array();
    $data_listcount = 0;

	$result1 = $this->db->query($sql1);
	$result1 = $result1->result_array();

	$list['dgrouptype'] = "list";




	/**
	 * title
	 */
    //if (!$dgroup_structure['listnotitle']) {
        foreach (array_keys($dgroup_structure['listarray']) as $fieldname){
        	$list['title'][] = $dgroup_structure['listarray'][$fieldname]['listname'];
        }
    //}


	/*
	 * rows
	 */
	$this_row_button = array();

    if ($result1){

        foreach ($result1 as $field1) {

			//$this_row_button = $element_button_row;

			//encode thisid, insert into the $element_button_row array
            $this_rid = encode_id($field1[$this_ridfield]);
			if ($button_row_ridpos){
				foreach ($button_row_ridpos as $this_pos){
					$this_row_button[$this_pos]['targetid'] = $this_rid;

				}
			}

			/**
			 * rowid
			 */
			$list['rowid'][$data_listcount] = $this_rid;



            $thislist_count = 0;

            foreach (array_keys($dgroup_structure['listarray']) as $fieldname){

				//
				// switch and put all the actual list data in array
				//
                if ($thislist_count < $dgroup_structure['fielduse_count']){

                    //put the dgroup field data into $this_field for easy access namespace
                    $this_field = $dgroup_structure['table'][$dgroup_structure['field'][$fieldname]['tablecount']]['fields'][$dgroup_structure['field'][$fieldname]['fieldcount']];

                    if ($dgroup_structure['listarray'][$fieldname]['list_langc']){
                    $fieldname_thislang = $fieldname."_".$langinfo['use'];

                    $sqllangc = "SELECT langc_value FROM langc WHERE langc_tableid = '".$field1[$dgroup_structure['list']['listkey']]."' AND langc_table = '".$dgroup_structure['fieldsort'][$fieldname_thislang]['table']."' AND langc_field = '".$dgroup_structure['fieldsort'][$fieldname_thislang]['field']."'";
                    $sqllangc .= " AND langc_lang = '".$langinfo['use']."' LIMIT 1";
                    $langcresult = $this->db->query($sqllangc);
					$langcresult = $langcresult->row_array(0);
					$langcresult = $langcresult['langc_value'];

                    $data_list[$data_listcount][] = $langcresult;

                    } elseif ($dgroup_structure['listarray'][$fieldname]['list_formtype'] == 2){
                    //$dgroup_structure['list']['sellist'][$fieldname]['tablecount']
                    //$dgroup_structure['list']['sellist'][$fieldname]['fieldcount']
                    $iconview = $dgroup_structure['table'][$dgroup_structure['list']['sellist'][$fieldname]['tablecount']]['fields'][$dgroup_structure['list']['sellist'][$fieldname]['fieldcount']]['seliconv'];
                    $this_row_result = $this->core_element_dgroup_select_show($dgroup_structure['table'][$dgroup_structure['list']['sellist'][$fieldname]['tablecount']]['fields'][$dgroup_structure['list']['sellist'][$fieldname]['fieldcount']]['sellist'],$field1[$fieldname],$iconview);

                        if (!$iconview){
                            $data_list[$data_listcount][] = $this_row_result;

                        } elseif ($this_row_result['icon']){
                            if ($this_row_result['icontype'] == '16'){
                                //$data_list[$data_listcount][] = '';//'IMG:html/images/icons/16/'.$this_row_result['icon'].','.$this_row_result['name'].',16,16';
								$data_list[$data_listcount][] = '<img src="html/images/icons/16/'.$this_row_result['icon'].'" width="16" height="16" alt="'.$this_row_result['name'].'"/>';

                            } else {
                                //$data_list[$data_listcount][] = '';//'IMG:html/images/icons/32/'.$this_row_result['icon'].','.$this_row_result['name'].',32,32';
								$data_list[$data_listcount][] = '<img src="html/images/icons/32/'.$this_row_result['icon'].'" width="32" height="32" alt="'.$this_row_result['name'].'"/>';
                            }
                        } else {

                            $data_list[$data_listcount][] = $this_row_result['name'];

                        }

                    } elseif ($dgroup_structure['listarray'][$fieldname]['list_formtype'] == 3){

                        $iconview = $dgroup_structure['table'][$dgroup_structure['list']['chkbox'][$fieldname]['tablecount']]['fields'][$dgroup_structure['list']['chkbox'][$fieldname]['fieldcount']]['seliconv'];

                        $this_row_result = $this->core_element_dgroup_chkbox_show(
                        $dgroup_structure['table'][$dgroup_structure['list']['chkbox'][$fieldname]['tablecount']]['fields'][$dgroup_structure['list']['chkbox'][$fieldname]['fieldcount']]['name_lang_chkname0'],
                        $dgroup_structure['table'][$dgroup_structure['list']['chkbox'][$fieldname]['tablecount']]['fields'][$dgroup_structure['list']['chkbox'][$fieldname]['fieldcount']]['name_lang_chkname1'],
                        $field1[$fieldname],
                        $iconview,
                        $dgroup_structure['table'][$dgroup_structure['list']['chkbox'][$fieldname]['tablecount']]['fields'][$dgroup_structure['list']['chkbox'][$fieldname]['fieldcount']]['name_lang_chkname0_icon'],
                        $dgroup_structure['table'][$dgroup_structure['list']['chkbox'][$fieldname]['tablecount']]['fields'][$dgroup_structure['list']['chkbox'][$fieldname]['fieldcount']]['name_lang_chkname1_icon']
                        );

                        if (!$iconview){
                            $data_list[$data_listcount][] = $this_row_result;

                        }elseif ($this_row_result['icon']) {
							//$data_list[$data_listcount][] = 'IMG:html/images/icons/16/'.$this_row_result['icon'].','.$this_row_result['name'].',16,16';
							$data_list[$data_listcount][] = '<img src="html/images/icons/16/'.$this_row_result['icon'].'" width="16" height="16" alt="'.$this_row_result['name'].'"/>';
                        }else {
                            $data_list[$data_listcount][] = '';

                        }

                    } elseif ($dgroup_structure['listarray'][$fieldname]['list_formtype'] == 4) {
                    $data_list[$data_listcount][] = nl2br($field1[$fieldname]);

                    } elseif ($dgroup_structure['listarray'][$fieldname]['list_formtype'] == 6 || $dgroup_structure['listarray'][$fieldname]['list_formtype'] == 12 || $dgroup_structure['listarray'][$fieldname]['list_formtype'] == 18) {

                    $this_value = core_date_convert($this_field['date'],$this_field['date_showformat'],$field1[$fieldname]);
                    $data_list[$data_listcount][] = $this_value;

                    } else {
                    $data_list[$data_listcount][] = $field1[$fieldname];

                    }

                }




				//check if this field is used to replace some button value
				if ($fieldreplace_thisid == $fieldname || $fieldreplace_usethisid || in_array($fieldname,$fieldreplace_targetvalue)) {
					$count = 0;
					$fieldreplace = array();
					while (isset($element_button_row[$count])){

						if (isset($element_button_row[$count]['targetid_field']) && $element_button_row[$count]['targetid_field'] == $fieldname){
							$this_row_button[$count]['targetid'] = encode_id($field1[$fieldname]);
						}elseif(isset($element_button_row[$count]['targetid']) && $element_button_row[$count]['targetid'] == "thisid"){
							$this_row_button[$count]['targetid'] = $thisid_en;
						}

						if (in_array($fieldname,$fieldreplace_targetvalue)){
							foreach($element_button_row[$count]['targetvalue_field'] as $this_value_field){
								if ($this_value_field == $fieldname){
									$this_row_button[$count]['targetvalue'] = preg_replace("/FIELD".$fieldname."/XX",$field1[$fieldname], $element_button_row[$count]['targetvalue']);
								}
							}
						}


					$count++;
					}
				}



            $thislist_count++;
            }

		$list['table_button'][$data_listcount] = $this_row_button;

/*
 *
    		foreach (array_keys($element_button_row) as $this_button_key){
				if ($this_button['position'] == "rowend"){
					$list['button_t'][$data_listcount][$this_button_key] = $this_button;
				} elseif ($this_button['position'] == "rowstart"){
					$list['button_t'][$data_listcount]['rowstart'][] = $this_button;
				} elseif ($this_button['position'] == "rowone"){
					$list['button_t'][$data_listcount]['rowone'][] = $this_button;
				}
			}
 *
 */

            $data_listcount++;
		}
    }



    /*
	 * data
	 *
	 * returning result are
	 * [title]
	 * [rowid]$data_listcount]
	 * [button][$data_listcount]
	 * [data][$data_listcount]
	 *
	 */



	foreach (array_keys($element_button_row) as $this_button_key){
		$element_button_row[$this_button_key]['key'] = $this_button_key;
		if ($element_button_row[$this_button_key]['position'] == "rowend"){
			$result_button['rowend'][] = $element_button_row[$this_button_key];
		} elseif ($element_button_row[$this_button_key]['position'] == "rowstart"){
			$result_button['rowstart'][] = $element_button_row[$this_button_key];
		} elseif ($element_button_row[$this_button_key]['position'] == "rowone"){
			$result_button['rowone'][] = $element_button_row[$this_button_key];
		}
	}

	$result['data'] = $list;
    $result['data']['button'] = $result_button;
    $result['data']['table'] = $data_list;
    $result['data']['listnotitle'] = $dgroup_structure['listnotitle'];
	$result['data']['listarray'] = $dgroup_structure['listarray'];
	$result['element_button'] = $element_button;

	//$result['data']['button'] = $element_button_row;
	//$list['list']['listtitle'] = $dgroup_structure['listarray'];
    //$list['list']['listfield'] = $dgroup_structure['fieldsort'];
	//print_r($result);
return($result);
}

function core_element_dgroup_aved_list_changebuttonpos($element_button){

    $thiscount = 0;
    while ($element_button['buttons'][$thiscount]) {
    //echo $element_button['buttons'][$thiscount]['autobutton'];
    if ($element_button['buttons'][$thiscount]['autobutton'] && ($element_button['buttons'][$thiscount]['type'] == "e" || $element_button['buttons'][$thiscount]['type'] == "d" || $element_button['buttons'][$thiscount]['type'] == "v")) $element_button['buttons'][$thiscount]['position'] = "rowend";
    $thiscount++;
    }

return ($element_button);
}

function dgroup_searcharray($dgroup_structure){

	global $_POST,$_GET;

	foreach ($dgroup_structure['table'] as $this_table) {

	    if ($this_table['fields'] && !$this_table['e_xtra']) {

	        foreach ($this_table['fields'] as $this_field) {
	            if (!$this_field['multilang']) {
	            $fieldarray[$this_field['core_db_fields_name']] = $_POST[$this_field['core_db_fields_name']];
	                if (!$_POST[$this_field['core_db_fields_name']] && $_GET[$this_field['core_db_fields_name']]) $fieldarray[$this_field['core_db_fields_name']] = $_GET[$this_field['core_db_fields_name']];
	            }elseif ($this_field['multilang'] == 1 || $this_field['multilang'] == 2){
	            $thismlfield = $this_field['core_db_fields_name']."_en";
	            $fieldarray[$this_field['core_db_fields_name']] = $_POST[$thismlfield];
	                if (!$_POST[$thismlfield] && $_GET[$thismlfield]) $fieldarray[$this_field['core_db_fields_name']] = $_GET[$thismlfield];
	            }
	        }

	    }

	}

return ($searcharray);
}

}