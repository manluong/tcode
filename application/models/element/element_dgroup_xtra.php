<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Element_dgroup_xtra extends Element_dgroup {

	function __construct() {
		parent::__construct();
	}

function core_element_xtra_structure($structure){

    $sql = preg_replace("/\*/", $structure['e_xtra']['field'], $structure['sql']);
	$result = $this->db->query($sql);
	$result = $result->row_array(1);
	$xtra_gpid = $result[$structure['e_xtra']['field']];
    //echo $sql."=".$xtra_gpid; exit;

    //continue only if there is a group of extra field specified
    if ($xtra_gpid){

$sql1 = "SELECT * FROM global_setting.core_e_xtra_field
LEFT JOIN langc ON core_e_xtra_field.core_e_xtra_field_id = langc.langc_tableid
AND langc.langc_table = 'core_e_xtra_field'
AND langc.langc_lang = '".$this->lang->langinfo['use']."'
WHERE core_e_xtra_field.core_e_xtra_field_gpid = '$xtra_gpid'";

	$result1 = $this->db->query($sql1);
	$result1 = $result1->result_array();
    if ($result1){

        $tablenum = count($structure['table']);
        $fieldsort_count = 200;
        $fieldnum = 0;

		//$lang["e_xtra"] = f_layout_lang_load("e_xtra");
        //$app = "e_xtra";

        $structure['table'][$tablenum]['table'] = "core_e_xtra_value";
        $structure['table'][$tablenum]['e_xtra'] = "1";
        $structure['table'][$tablenum]['e_xtra_lang'] = $this->lang->langinfo['use'];
        $structure['table'][$tablenum]['e_xtra_gpid'] = $xtra_gpid;

        //for each of the field
        foreach ($result1 as $field1) {

            $fieldname = "e_xtra_field_".$field1['core_e_xtra_field_id'];

            //$structure['thisidform'] = $field3['core_e_dgroup_table_field_table'].".".$field3['core_e_dgroup_table_field_field'];
            //$structure['table'][$this_count2]['index'] = $field3['core_e_dgroup_table_field_field'];
            //$structure['list']['listkey'] = $field3['core_e_dgroup_table_field_field'];

            //if ($field3['core_e_dgroup_table_field_thisidlist']) {
            //$structure['thisidlist'][$field3['core_e_dgroup_table_field_thisidlist']] = $field3['core_e_dgroup_table_field_table'].".".$field3['core_e_dgroup_table_field_field'];
            //}

             if ($field1['core_e_xtra_field_sort'] == 0) { $field1['core_e_xtra_field_sort'] = $fieldsort_count; $fieldsort_count++; }
             $structure['fieldsort'][$fieldname]['sort'] = $field1['core_e_xtra_field_sort'];
             $structure['fieldsort'][$fieldname]['table'] = "core_e_xtra_value";
             $structure['fieldsort'][$fieldname]['tablenum'] = $tablenum;
             $structure['fieldsort'][$fieldname]['fieldnum'] = $fieldnum;
             $structure['fieldsort'][$fieldname]['fieldname'] = $fieldname;
             $structure['fieldsort'][$fieldname]['e_xtra'] = "1";
             $structure['fieldsort'][$fieldname]['e_xtra_shared'] = $field1['core_e_xtra_field_shared'];

             //$structure['fieldlist'] .= $field3['core_e_dgroup_table_field_table'].".".$field3['core_e_dgroup_table_field_field'].",";
             //$structure['fieldlist_short'] .= $field3['core_e_dgroup_table_field_field'].",";

             $structure['table'][$tablenum]['fields'][$fieldnum]['e_xtra'] = "1";
             $structure['table'][$tablenum]['fields'][$fieldnum]['e_xtra_shared'] = $field1['core_e_xtra_field_shared'];
             $structure['table'][$tablenum]['fields'][$fieldnum]['e_xtra_fieldid'] = $field1['core_e_xtra_field_id'];
             $structure['table'][$tablenum]['fields'][$fieldnum]['sort'] = $field1['core_e_xtra_field_sort'];
             $structure['table'][$tablenum]['fields'][$fieldnum]['core_db_fields_name'] = $fieldname;
             $structure['table'][$tablenum]['fields'][$fieldnum]['db_type'] = "varchar";
             $structure['table'][$tablenum]['fields'][$fieldnum]['db_length'] = "255";
             $structure['table'][$tablenum]['fields'][$fieldnum]['db_defaultvalue'] = "";

			 $structure['table'][$tablenum]['fields'][$fieldnum]['hide'] = "";
			 $structure['table'][$tablenum]['fields'][$fieldnum]['fvnobr'] = "";
			 $structure['table'][$tablenum]['fields'][$fieldnum]['fvnobrpre'] = "";
			 $structure['table'][$tablenum]['fields'][$fieldnum]['fvnobrsub'] = "";
			 $structure['table'][$tablenum]['fields'][$fieldnum]['fvnolabel'] = "";


             //if ($avedtype == "form" || $avedtype == "list" || $avedtype == "search"){
             /////////////////////////////////////////////////////////////////////////////
             //FORM or SEARCH or LIST

             if (!$field1['core_e_xtra_field_formtype']) $field1['core_e_xtra_field_formtype']=1;
             //if no form type selected, default is input(text input)
             $structure['table'][$tablenum]['fields'][$fieldnum]['form_fieldtype'] = $field1['core_e_xtra_field_formtype'];
             if (isset($field1['core_e_xtra_field_formrequired'])) $structure['table'][$tablenum]['fields'][$fieldnum]['form_required'] = $field1['core_e_xtra_field_formrequired'];
             //$structure['table'][$tablenum]['fields'][$fieldnum]['form_validate'] = $field1['core_e_xtra_field_formvalidate'];
             //$structure['table'][$tablenum]['fields'][$fieldnum]['form_minlenght'] = "";

             //$structure['table'][$tablenum]['fields'][$fieldnum]['form_view'] = 0;
             //$structure['table'][$tablenum]['fields'][$fieldnum]['form_add'] = 0;
             //$structure['table'][$tablenum]['fields'][$fieldnum]['form_edit'] = 0;
             //$structure['table'][$tablenum]['fields'][$fieldnum]['form_del'] = 0;
             /*
             if ($avedtype == "search"){
                 $structure['table'][$tablenum]['fields'][$fieldnum]['form_search'] = $field1['core_db_fields_form_search'];
                 $structure['table'][$tablenum]['fields'][$fieldnum]['searchblank'] = $field1['core_db_fields_searchblank'];
                 $structure['table'][$tablenum]['fields'][$fieldnum]['searchlike'] = $field1['core_db_fields_searchlike'];
                 $structure['table'][$tablenum]['fields'][$fieldnum]['searchdate'] = $field1['core_db_fields_searchdate'];
                 $structure['table'][$tablenum]['fields'][$fieldnum]['searchdateto'] = $field1['core_db_fields_searchdateto'];
             }
             */

             if ($field1['langc_value']){
             $structure['table'][$tablenum]['fields'][$fieldnum]['form_name_lang'] = $field1['langc_value'];
             } else {
             $structure['table'][$tablenum]['fields'][$fieldnum]['form_name_lang'] = $field1['core_e_xtra_field_name'];
             }
             //$name_lang__d = $field1['core_db_fields_name']."__d";
             //$structure['table'][$tablenum]['fields'][$fieldnum]['form_name_lang__d'] = $lang[$app][$name_lang__d];


             //switch($field1['core_e_xtra_field_formtype']){
             //}

             //}else

            $fieldnum++;

            }//end if use

    }else{
       //$error = "<br>No field defined for this e_xtra group id: ".$xtra_gpid;
       $structure = 0;;
    }

    //html_show_array($structure['fieldsort']);


    //if ($structure) {

    uasort($structure['fieldsort'], array($this, 'core_element_dgroup_sort'));

    }else{

       //$error = "<br>No e_xtra group id";
       $structure = 0;

    }//end if gpid

    //$dgroup_structure = array_merge($dgroup_structure['fieldsort'], $structure['fieldsort']);
    //$dgroup_structure = array_merge($dgroup_structure['table'], $structure['table']);

    //}

return ($structure);
}



function core_element_xtra_value($xtra_structure){

    foreach ($xtra_structure['table'] as $this_table){
        if ($this_table['e_xtra']){
        $sql = "SELECT core_e_xtra_value_value FROM global_setting.core_e_xtra_value WHERE core_e_xtra_value_linkid = ".$this->url['id_plain']." AND core_e_xtra_value_gpid = '$xtra_gpid' AND core_e_xtra_value_lang = '".$this_table['e_xtra_lang']."'";
		$result = $this->db->query($sql);
		$result = $result->result_array();
        }
    }
return ($result);
}


}