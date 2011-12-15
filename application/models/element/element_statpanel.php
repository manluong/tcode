<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Element_statpanel extends CI_Model {

	function __construct() {
		parent::__construct();
	}
	
	function get_statpanel($this_element_name,$an,$app,$aved,$thisid){
	
	$sql1 = "SELECT * FROM core_e_statpanel WHERE core_e_statpanel_gp = '$this_element_name' ORDER BY core_e_statpanel_sort";
	$result1 = $this->db->query($sql1);
	$result1 = $result1->result_array();
	//echo $this_element_name;
	//print_r($result1);
	
	if ($result1){
	
	    //style and menu are decided by the first set (by sort)
	    $statpanel['style'] = $result1[0]['core_e_statpanel_style'];
	    $statpanel['menu'] = $result1[0]['core_e_statpanel_menu'];
	    $statpanel['menustyle'] = $result1[0]['core_e_statpanel_menustyle'];
	    $statpanel['menuposition'] = $result1[0]['core_e_statpanel_menuposition'];
	    $statpanel['divcss'] = $result1[0]['core_e_statpanel_divcss'];
	
	    $count_thisset = 0;
	    foreach ($result1 as $field1) {
	
	        /*
	        $field1['core_e_statpanel_style']
	        $field1['core_e_statpanel_menu']
	        $field1['core_e_statpanel_menustyle']
	        $field1['core_e_statpanel_menuposition']
	        $field1['core_e_statpanel_divcss']
	        */
	
	        $sql2 = "SELECT * FROM core_e_statpanel_item WHERE core_e_statpanel_item_set = '".$field1['core_e_statpanel_set']."' AND core_e_statpanel_item_gp = '$this_element_name' ORDER BY core_e_statpanel_item_sort";
	        $result2 = $this->db->query($sql2);
			$result2 = $result2->result_array();
	        if ($result2){
	            $count_thisrow = 0;
	            foreach ($result2 as $field2) {
	
	              $statpanel['set'][$count_thisset][$count_thisrow]['set'] = $field2['core_e_statpanel_item_set'];
	              $statpanel['set'][$count_thisset][$count_thisrow]['desp'] = $field2['core_e_statpanel_item_desp'];
	              $statpanel['set'][$count_thisset][$count_thisrow]['bg'] = $field2['core_e_statpanel_item_bg'];
	              if (!$field2['core_e_statpanel_item_xapp']){
	              $statpanel['set'][$count_thisset][$count_thisrow]['xapp'] = $app;
	              } else {
	              $statpanel['set'][$count_thisset][$count_thisrow]['xapp'] = $field2['core_e_statpanel_item_xapp'];
	              }
	              $statpanel['set'][$count_thisset][$count_thisrow]['xan'] = $field2['core_e_statpanel_item_xan'];
	              $statpanel['set'][$count_thisset][$count_thisrow]['xaved'] = $field2['core_e_statpanel_item_xaved'];
	              $statpanel['set'][$count_thisset][$count_thisrow]['xthisid'] = $field2['core_e_statpanel_item_xthisid'];
	              $statpanel['set'][$count_thisset][$count_thisrow]['xdiv'] = $field2['core_e_statpanel_item_xdiv'];
	              $statpanel['set'][$count_thisset][$count_thisrow]['xlink'] = "?app=".$statpanel['set'][$count_thisset][$count_thisrow]['xapp']."&an=".$field2['core_e_statpanel_item_xan']."&aved=".$field2['core_e_statpanel_item_xaved']."&thisid=".$field2['core_e_statpanel_item_xthisid'];
	
	              //print_r($field2);
	              //get the result
	              if ($field2['core_e_statpanel_item_sql']){
	
	                  $sql_statpanel = $field2['core_e_statpanel_item_sql'];
	                  $resultstat = $db->fetchAll($sql_statpanel, 2);
	                  if ($resultstat){
	
	                      switch ($field2['core_e_statpanel_item_sqlresult']){
	
	                      case '1':
	                      //count
	                      $statpanel['set'][$count_thisset][$count_thisrow]['result'][0] = count($resultstat);
	                      break;
	
	                      case '2':
	                      //fatchone
	                      $statpanel['set'][$count_thisset][$count_thisrow]['result'][0] = $resultstat[0][0];
	                      break;
	
	                      case '3':
	                      //fieldsum
	                      foreach ($resultstat as $fieldstat) {
	                        //$this_field = $field1['core_e_statpanel_item_sqlfieldsum'];
	                      }
	                      break;
	
	                      case '4':
	                      //fieldlist
	                      break;
	
	                      }
	                  }
	
	
	              }elseif($field2['core_e_statpanel_item_function']){
	
	
	              }
	
	
	              if ($statpanel['set'][$count_thisset][$count_thisrow]['result'][0] > 1){
	              //$statpanel['set'][$count_thisset][$count_thisrow]['unit'] = preg_replace("/XXs/","s", $statpanel['set'][$count_thisset][$count_thisrow]['unit']);
	              $statpanel['set'][$count_thisset][$count_thisrow]['unit'] = $field2['core_e_statpanel_item_units'];
	              } else {
	              $statpanel['set'][$count_thisset][$count_thisrow]['unit'] = $field2['core_e_statpanel_item_unit'];
	}
	
	
	
	
	              //get the style
	
	
	
	            $count_thisrow++;
	            }
	        }
	
	    $count_thisset++;
	    }
	}else{
	
	    echo "No statpanel element: ".$this_element_name;
	
	}
	
	
		$result['outputdiv'] = 1;
		$result['data'] = $statpanel;
		
	return($result);
	}

	

}