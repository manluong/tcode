<?php

function element_fdata($this_element_name,$dataarray=""){

global $db,$h_apps_html,$DOCUMENT_ROOT,$an,$app,$aved,$thisid,$h_json;

$sql1 = "SELECT * FROM core_e_fdata WHERE core_e_fdata_name = '$this_element_name' LIMIT 1";
$result1 = $db->fetchRow($sql1, 2);

if ($result1){

    if ($result1['core_e_fdata_type'] == "jsonac"){
	
        $jsonac_value = explode(",", $result1['core_e_fdata_jsonac_value']);
        $jsonac_label = explode(",", $result1['core_e_fdata_jsonac_label']);
        $jsonac_seprator = explode("^,", $result1['core_e_fdata_jsonac_seprator']);

		$core_element_dgroup = core_element_dgroup($result1['core_e_fdata_dgroupname'],"",$app,"sq","","","sq","",0,0,0,0,1,1,"",1);
		//html_show_array($core_element_dgroup);exit;
	
        //$this_listfield = $h_apps_html[0]['data']['listfield'];

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




        /*
        $result = array();
        foreach ($items as $key=>$value) {
        	if (strpos(strtolower($key), $q) !== false) {
        		array_push($result, array(
        			"name" => $key,
        			"to" => $value
        		));
        	}
        }
        echo json_encode($result);
        */


?>