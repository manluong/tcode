<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Element_dgroup_save extends Element_dgroup {

	private $_log_data = array();

	function __construct() {
		parent::__construct();
		$CI =& get_instance();
		$this->_log_data =& $CI->log_data;
	}


function dgroup_save($dgroup_structure,$dgroup_value,$aved){
    global $_POST;

	$error_json = "";

    ////////////////////////////////////////////////////////////////////////////////
    //error checking
    ////////////////////////////////////////////////////////////////////////////////
    if ($dgroup_structure['fieldsort']){

		foreach (array_keys($dgroup_structure['fieldsort']) as $this_fieldname){

		    $this_field = $dgroup_structure['table'][$dgroup_structure['fieldsort'][$this_fieldname]['tablenum']]['fields'][$dgroup_structure['fieldsort'][$this_fieldname]['fieldnum']];
		    if (isset($_POST[$this_fieldname])) $this_field_value = $_POST[$this_fieldname];

		    switch($this_field['form_fieldtype']){

		    //date
		    case "6":
		    //date(saved format),date_showformat,date_to
		    if ($this_field_value){
		        $this_field_value = explode("/", $this_field_value);
		        if (!checkdate($this_field_value[1], $this_field_value[2], $this_field_value[0])) $error_json .= '"'.$this_fieldname.'":"Date Error",';
		        if (isset($this_field_value[3])) $error_json .= '"'.$this_fieldname.'":"Date Error",';
		    }

		    ; break;

		    //num
		    case "9":
		    //if there is no value from a number, remove it from [fieldsort] which later will loop for insert/update sql statement
		    //if do not remove, it will create the field as 0 rather then NULL
		    if (!is_numeric($this_field_value)) {
		    	if ($this_field['db_null']) {
		    		$_POST[$this_fieldname] = NULL;
		    	} else {
		    		$_POST[$this_fieldname] = 0;
		    	}
		    }

		    ; break;

		    //password
		    case "7":
			//emcrypt password
		    if ($_POST[$this_fieldname]) { $_POST[$this_fieldname] = f_password_encrypt($_POST[$this_fieldname],1); }

		    ; break;



		    }

		}

    }



    ////////////////////////////////////////////////////////////////////////////////
    // Saving (if no error_json)
    ////////////////////////////////////////////////////////////////////////////////
    if (!$error_json) {

	$this_parent_id = "";
    foreach ($dgroup_structure['table'] as $this_table) {

        $fieldarray = array();

        if (isset($this_table['fields']) && !isset($this_table['e_xtra'])) {

	        foreach ($this_table['fields'] as $this_field) {
	            if (!isset($this_field['multilang'])) {
	            	if(isset($_POST[$this_field['core_db_fields_name']])) $fieldarray[$this_field['core_db_fields_name']] = $_POST[$this_field['core_db_fields_name']];
	            }elseif ($this_field['multilang'] == 1 || $this_field['multilang'] == 2){
	            $thismlfield = $this_field['core_db_fields_name']."_en";
	            $fieldarray[$this_field['core_db_fields_name']] = $_POST[$thismlfield];
	            }

	            //log - check if log instruction for logging current valuee
				if (isset($this->_log_data['log_type']['eventfield'][$this_field['core_db_fields_name']])) {
	                $this->_log_data['log_type']['eventfield'][$this_field['core_db_fields_name']]['cur'] = $dgroup_value[$this_field['core_db_fields_name']];
	                $this->_log_data['log_type']['eventfield'][$this_field['core_db_fields_name']]['new'] = $fieldarray[$this_field['core_db_fields_name']];
	            }
				//print_r($this->LogM->log_type);die();
	            /*
	            if ($log['eventfield'][$this_field['core_db_fields_name']]){
	                $log['eventfield'][$this_field['core_db_fields_name']]['cur'] = $dgroup_value[$this_field['core_db_fields_name']];
	                $log['eventfield'][$this_field['core_db_fields_name']]['new'] = $fieldarray[$this_field['core_db_fields_name']];
	            }
				 *
				 */

	        }

	        if ($this_table['isparent']) {
				$req_table = $this_table['table'];
				$req_index = $this_table['index'];
				$req_id = $this->url['id_plain'];

	        } else {
				$req_table = $this_table['table'];
				$req_index = explode(".", $this_table['linkparent'][1]);
				$req_index = $req_index[1];
				$req_id = $this_parent_id;
	        }

	        if (isset($this_table['linkchild'][1]) && ($aved == "es" || $aved == "ds")){
	          	$this_linkchild = explode(".", $this_table['linkchild'][1]);
	            if ($this_linkchild[1] != $req_index){
		            $sql = "SELECT ".$this_linkchild[1]." FROM ".$req_table." WHERE ".$req_index." = ".$req_id;
		            $result = $this->db->query($sql);
					$result = $result->row_array(0);
		            $this_parent_id = $result[$this_linkchild[1]];
	            }else{
		            $this_parent_id = $req_id;
	            }
	        }

	        $where = $req_index. " = '".$req_id."'";

	        if ($aved == "es" && $fieldarray){

	            ///////////////////////////////////////
	            //EDITSAVE sql
	            if ($this_table['isparent']) {
					if ($fieldarray) {
						$sql = $this->db->update_string($req_table, $fieldarray, $where);
						$updated = $this->db->query($sql);
					}
	            } else {
	                $sql = "SELECT ".$req_index." FROM ".$req_table." WHERE ".$where;
	                $result = $this->db->query($sql);
	                if ($result->num_rows() > 0){
	                    if ($fieldarray) {
						$sql = $this->db->update_string($req_table, $fieldarray, $where);
						$updated = $this->db->query($sql);
						}
	                    //$updated = $db->update($req_table, $fieldarray, $where);
	                    //echo "<br>got existing!!!<br>";
	                } else {
	                    $fieldarray[$req_index] = $req_id;
						$sql = $this->db->insert_string($req_table, $fieldarray);
						$this->db->query($sql);
	                    $lastid = $this->db->insert_id();
	                    //print_r($fieldarray[$req_index]);
	                    //echo "<br>no existing!!!<br>";
	                }
	            }

	            $saveid = $this->url['id_plain'];

	        }elseif ($aved == "as" && $fieldarray){

	            ///////////////////////////////////////
	            //ADDSAVE sql
	            if (isset($dgroup_structure['extend_add'])){
	            foreach ($dgroup_structure['extend_add'] as $extend_add){
	                if($extend_add['value'] == "thisid") $extend_add['value'] = $this->url['id_plain'];
	                if($extend_add['value'] == "cardid") $extend_add['value'] = $id['cardid'];
	                if($extend_add['table'] == $this_table['table']) $fieldarray[$extend_add['field']] = $extend_add['value'];
	            }
				}

	            if ($this_table['isparent']) {
	            //if the default base type is a list
	            //the id submited is suppose to be for the field of listid instead of formid

	                if ($dgroup_structure['basetype'] == "list" && $this->url['id_plain']){
	                        $thislistidfield = explode(".", $dgroup_structure['thisidlist'][1]);
	                        if ($req_table == $thislistidfield[0]){
	                        $fieldarray[$thislistidfield[1]] = $this->url['id_plain'];
	                        }
	                    $req_id="";
	                }

	                //if thisid exist, make it as the index
	                if ($req_id) $fieldarray[$req_index] = $req_id;

	                $sql = $this->db->insert_string($req_table, $fieldarray);
					$this->db->query($sql);
	                $newid = $this->db->insert_id();

	                if ($req_id) $newid = $req_id;
	                $req_id = $newid;

	            } else {

	                $fieldarray[$req_index] = $req_id;
	                $sql = $this->db->insert_string($req_table, $fieldarray);
					$this->db->query($sql);
	                $lastid = $this->db->insert_id();

	            }

	            $saveid = $newid;
	            $log['saveid'] = $newid;
				$this->_log_saveid = $newid;

	        }elseif ($aved == "ds" && $where){

				//get listid before delete
				if ($dgroup_structure['basetype'] == "list" && $dgroup_structure['thisidlist'][1]){
				$list_key = explode(".", $dgroup_structure['thisidlist'][1],2);
				$listid_sql = "SELECT ".$list_key[1]." FROM ".$dgroup_structure['table'][0]['table']." WHERE ".$dgroup_structure['table'][0]['index']." = '".$this->url['id_plain']."' LIMIT 1";
				$listid = $this->db->query($listid_sql);
				$listid = $listid->row_array(0);
				$listid = $listid[$list_key[1]];

				}
	            ///////////////////////////////////////
	            //DELSAVE sql
	            $this->db->delete($req_table, $where);

	        }


			//
			// $this_parent_id
			//
	        if (isset($this_table['linkchild']) && $aved == "as"){
	        	$this_linkchild = explode(".", $this_table['linkchild'][1]);
	          	if ($this_linkchild[1] != $req_index){
		        	$sql = "SELECT ".$this_linkchild[1]." FROM ".$req_table." WHERE ".$req_index." = ".$req_id;
					$result = $this->db->query($sql);
					$result = $result->row_array(0);
					$this_parent_id = $result[$this_linkchild[1]];

		        }else{
		        	$this_parent_id = $req_id;
		        }
	        }



		//
		// for e_xtra table
		//
        }elseif (isset($this_table['e_xtra'])) {

            $req_table = "global_setting.core_e_xtra_value";
            if (!$saveid) $saveid = $this->url['id_plain'];
            foreach ($this_table['fields'] as $this_field) {

                $where=array();
                $fieldarray=array();

                $fieldarray["core_e_xtra_value_linkid"] = $saveid;
                $fieldarray["core_e_xtra_value_gpid"] = $this_table['e_xtra_gpid'];
                $fieldarray["core_e_xtra_value_fieldid"] = $this_field['e_xtra_fieldid'];
                $fieldarray["core_e_xtra_value_value"] = $_POST[$this_field['core_db_fields_name']];

                $fieldarray_update["core_e_xtra_value_value"] = $_POST[$this_field['core_db_fields_name']];

                $where[0] = "core_e_xtra_value_linkid = '".$saveid."'";
                $where[1] = "core_e_xtra_value_gpid = '".$this_table['e_xtra_gpid']."'";
                $where[2] = "core_e_xtra_value_fieldid = '".$this_field['e_xtra_fieldid']."'";

                if ($this_field['e_xtra_shared']){
                $fieldarray["core_e_xtra_value_lang"] = 'en';
                $where[3] = "core_e_xtra_value_lang = 'en'";
                $where_lang = 'en';
                } else {
                $fieldarray["core_e_xtra_value_lang"] = $this_table['e_xtra_lang'];
                $where[3] = "core_e_xtra_value_lang = '".$this_table['e_xtra_lang']."'";
                $where_lang = $this_table['e_xtra_lang'];
                }

                if ($aved == "es" || $aved == "as"){

                    $sqlchk = "SELECT core_e_xtra_value_value FROM global_setting.core_e_xtra_value WHERE core_e_xtra_value_linkid = '".$saveid."' AND core_e_xtra_value_gpid = '".$this_table['e_xtra_gpid']."' AND core_e_xtra_value_fieldid = '".$this_field['e_xtra_fieldid']."' AND core_e_xtra_value_lang = '".$where_lang."' LIMIT 1";
                    $resultchk = $this->db->query($sqlchk);
					$resultchk = $resultchk->row_array(0);

                    if ($_POST[$this_field['core_db_fields_name']]){
                        if ($resultchk){
                        $sql = $this->db->update_string($req_table, $fieldarray_update, $where);
						$updated = $this->db->query($sql);

                        }else{
                        $sql = $this->db->insert_string($req_table, $fieldarray);
		                $this->db->query($sql);

                        }
                    }elseif($resultchk){
                    $db->delete($req_table, $where);
                    }

                }elseif ($aved == "ds"){
                    $db->delete($req_table, $where);
                }

            }


        }
    }



    //
    // multi lang field
    //
    if(isset($dgroup_structure['multilang'])){
    //global $lang_name,$lang_use;
    $req_table = "langc";

    foreach (array_keys($dgroup_structure['fieldsort']) as $this_fieldname){

        if ($dgroup_structure['fieldsort'][$this_fieldname]['lang']){

            $where=array();
            $fieldarray=array();

            $fieldarray["langc_table"] = $dgroup_structure['fieldsort'][$this_fieldname]['table'];
            $fieldarray["langc_field"] = $dgroup_structure['fieldsort'][$this_fieldname]['field'];
            $fieldarray["langc_lang"] = $dgroup_structure['fieldsort'][$this_fieldname]['lang'];
            $fieldarray["langc_value"] = $_POST[$this_fieldname];
            $fieldarray["langc_tableid"] = $saveid;

            $where[0] = "langc_tableid = '".$saveid."'";
            $where[1] = "langc_table = '".$dgroup_structure['fieldsort'][$this_fieldname]['table']."'";
            $where[2] = "langc_field = '".$dgroup_structure['fieldsort'][$this_fieldname]['field']."'";
            $where[3] = "langc_lang = '".$dgroup_structure['fieldsort'][$this_fieldname]['lang']."'";

            //save en filed into parent table
            //if ($dgroup_structure['fieldsort'][$this_fieldname]['lang']){
            //$fieldarray_parent[$dgroup_structure['fieldsort'][$this_fieldname]['field']] = $_POST[$this_fieldname];
            //}

            if ($aved == "es" || $aved == "as"){

                $sqlchk = "SELECT langc_value FROM langc WHERE langc_tableid = '".$saveid."' AND langc_table = '".$dgroup_structure['fieldsort'][$this_fieldname]['table']."' AND langc_field = '".$dgroup_structure['fieldsort'][$this_fieldname]['field']."' AND langc_lang = '".$dgroup_structure['fieldsort'][$this_fieldname]['lang']."' LIMIT 1";
                $resultchk = $this->db->query($sqlchk);
				$resultchk = $resultchk->row_array(0);

                if ($_POST[$this_fieldname]){
                    if ($resultchk){
                    $sql = $this->db->update_string($req_table, $fieldarray, $where);
					$updated = $this->db->query($sql);
                    }else{
                   $sql = $this->db->insert_string($req_table, $fieldarray);
		            $this->db->query($sql);
                    }
                }elseif($resultchk){
				$this->db->delete($req_table, $where);
                }

            }elseif ($aved == "ds"){
                $this->db->delete($req_table, $where);
            }

        }

    }
    }



    }
    ////////////////////////////////////////////////////////////////////////////////
    // END Saving (if no error_json)
    ////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////////
    // Returning Result
    ////////////////////////////////////////////////////////////////////////////////

    if (!isset($listid) && $dgroup_structure['basetype'] == "list" && $dgroup_structure['thisidlist'][1]){
		$list_key = explode(".", $dgroup_structure['thisidlist'][1],2);
		$listid_sql = "SELECT ".$list_key[1]." FROM ".$dgroup_structure['table'][0]['table']." WHERE ".$dgroup_structure['table'][0]['index']." = '".$saveid."' LIMIT 1";
		$listid = $this->db->query($listid_sql);
		$listid = $listid->row_array(0);
		$listid = $listid[$list_key[1]];
	}

  	if ($error_json){

      	$form['save_error_json'] = '{
        	'.substr($error_json, 0, -1).'
      	}';
		$form['save_success'] = 0;

 	}elseif ($aved == "ds"){
		$form['save_success'] = 1;
		$form['list_id'] = encode_id($listid);
		$form['save_id'] = $this->url['id_encrypted'];

  	}else{

		if ($saveid < 10000000000){
			$form['save_success'] = 1;
			$form['save_id'] = encode_id($saveid);
			$form['list_id'] = $listid;
		} else {
			$form['save_success'] = 0;
			$form['save_error_msg'] = $saveid;
			$form['list_id'] = $listid;
		}

  	}

	$form['dgrouptype'] = "save";

    ////////////////////////////////////////////////////////////////////////////////
    // END Returning Result
    ////////////////////////////////////////////////////////////////////////////////


return ($form);
}


}