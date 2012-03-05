<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class App_generalM extends CI_Model {

	var $moreid = array();

	function __construct() {
		parent::__construct();
	}


function core_app_userinfo($username){

    global $db;

    $sql = "SELECT * FROM access_user LEFT JOIN card ON access_user.access_user_cardid = card.card_id WHERE access_user_username = '".$username."' LIMIT 1";
    $result = $db->fetchRow($sql, 2);

    if ($result['card_fname']){
    $thisresult['name'] = $result['card_fname'];
    }elseif ($result['card_lname']){
    $thisresult['name'] = $result['card_lname'];
    }elseif ($result['card_orgname']){
    $thisresult['name'] = $result['card_orgname'];
    }

    $thisresult['cardid'] = $result['card_id'];

    $thisresult['subgp'] = core_app_getsubgp($thisresult['cardid']);

    $core_app_getaccessgp = core_app_getaccessgp($thisresult['cardid']);

    $thisresult = array_merge($thisresult, $core_app_getaccessgp);

return($thisresult);
}

function core_app_getsubgp($cardid){

    global $db;

    $sql1 = "SELECT access_usergp_gpsub FROM access_usergp WHERE access_usergp_cardid = '$cardid'";

    $result1 = $db->fetchAll($sql1, 2);
    if ($result1){
        foreach ($result1 as $field1) {
          $thisresult[] = $field1['access_usergp_gpsub'];
        }
    }

return($thisresult);
}

function get_accessgp($cardid){

    $sql = "SELECT access_link_gpmaster FROM access_link WHERE access_link_cardid = ".$cardid." LIMIT 1";
	$result = $this->db->query($sql);
	$result = $result->row_array(1);


	$thisresult['accessgp'] = $result['access_link_gpmaster'];

    switch($thisresult['accessgp']){
    case "1":  break;
    case "2":
        //staff
        $sql = "SELECT staff_id FROM staff WHERE staff_cardid = ".$cardid." LIMIT 1";
        $result = $this->db->query($sql);
        $result = $result->row_array(1);
        if ($result) {
        $thisresult["staffid"] = $result['staff_id'];
        $thisresult["en"]["staffid"] = encode_id($result['staff_id']);
        }
        break;
    case "3":
        //client
        $sql = "SELECT client_id FROM client WHERE client_cardid = ".$cardid." LIMIT 1";
        $result = $this->db->query($sql);
        $result = $result->row_array(1);
        if ($result) {
        $thisresult["clientid"] = $result['client_id'];
        $thisresult["en"]["clientid"] = encode_id($result['client_id']);
        }
        break;
    case "4":  break;
    case "5":
        //vendor
        $sql = "SELECT vendor_id FROM vendor WHERE vendor_cardid = ".$cardid." LIMIT 1";
		$result = $this->db->query($sql);
		$result = $result->row_array(1);
        if ($result) {
        $thisresult["vendorid"] = $result['vendor_id'];
        $thisresult["en"]["vendorid"] = encode_id($result['vendor_id']);
        }
        break;
    case "6":  break;
    case "7":  break;

    }

return($thisresult);
}


//get other id from the THISID whorking on
//example, working on customer A invoice ID, also get the cardid and client id based on the invoice id
function get_moreid($thisidtype){

    global $moreid,$shortaved,$id;
    //this function will get an thisid, and return all other id in an array
    switch($thisidtype){

    case "client":
        $sql = "SELECT client_cardid FROM client WHERE client_id = ".$this->url['id_plain']." LIMIT 1";
		$result = $this->db->query($sql);
		$result = $result->row_array(1);
        if ($result) {
        $getthisid["cardid"] = $result['client_cardid'];
        $moreid["en"]["cardid"] = encode_id($result['client_cardid']);
        }
        $moreid["clientid"] = $this->url['id_plain'];
        $moreid["en"]["clientid"] = $this->url['id_encrypted'];
    break;

    case "staff":
        $sql = "SELECT staff_cardid FROM staff WHERE staff_id = ".$this->url['id_plain']." LIMIT 1";
		$result = $this->db->query($sql);
		$result = $result->row_array(1);
        if ($result) {
        $moreid["cardid"] = $result['staff_cardid'];
        $moreid["en"]["cardid"] = encode_id($result['staff_cardid']);
        }
        $moreid["staffid"] = $this->url['id_plain'];
        $moreid["en"]["staffid"] = $this->url['id_encrypted'];
    break;

    case "vendor":
        $sql = "SELECT vendor_cardid FROM vendor WHERE vendor_id = ".$this->url['id_plain']." LIMIT 1";
        $result = $this->db->query($sql);
		$result = $result->row_array(1);
        if ($result) {
        $moreid["cardid"] = $result['vendor_cardid'];
        $moreid["en"]["cardid"] = encode_id($result['vendor_cardid']);
        }
        $moreid["vendorid"] = $this->url['id_plain'];
        $moreid["en"]["vendorid"] = $this->url['id_encrypted'];
    break;


    case "card":
        $moreid = $this->get_accessgp($this->url['id_plain']);
        $moreid["cardid"] = $this->url['id_plain'];
        $moreid["en"]["cardid"] = $this->url['id_encrypted'];
    break;

    case "invoice":

        $sql = "SELECT a_invoice_clientid FROM a_invoice WHERE a_invoice_id = ".$this->url['id_plain']." LIMIT 1";
        $clientid = $db->fetchOne($sql, 2);
        $sql = "SELECT client_cardid FROM client WHERE client_id = ".$clientid." LIMIT 1";
        $result = $db->fetchOne($sql, 2);
        if ($result) {
        $moreid["cardid"] = $result;
        $moreid["en"]["cardid"] = encode_id($result);
        }
        $moreid["clientid"] = $clientid;
        $moreid["en"]["clientid"] = encode_id($clientid);

    break;

    case "invoiceitem":

        if($shortaved == "a"){
            $sql = "SELECT a_invoice_clientid FROM a_invoice WHERE a_invoice_id = ".$this->url['id_plain']." LIMIT 1";
            $clientid = $db->fetchOne($sql, 2);
            $sql = "SELECT client_cardid FROM client WHERE client_id = ".$clientid." LIMIT 1";
            $result = $db->fetchOne($sql, 2);
            if ($result) {
            $moreid["cardid"] = $result;
            $moreid["en"]["cardid"] = f_thisid_encode($result);
            }
            $moreid["clientid"] = $clientid;
            $moreid["en"]["clientid"] = f_thisid_encode($clientid);
        }elseif($shortaved == "e" || $shortaved == "d" || $shortaved == "v"){
            $sql = "SELECT a_invoice_item_invid FROM a_invoice_item WHERE a_invoice_item_id = ".$this->url['id_plain']." LIMIT 1";
            $invid = $db->fetchOne($sql, 2);
            $sql = "SELECT a_invoice_clientid FROM a_invoice WHERE a_invoice_id = ".$invid." LIMIT 1";
            $clientid = $db->fetchOne($sql, 2);
            $sql = "SELECT client_cardid FROM client WHERE client_id = ".$clientid." LIMIT 1";
            $result = $db->fetchOne($sql, 2);
            if ($result) {
            $moreid["cardid"] = $result;
            $moreid["en"]["cardid"] = f_thisid_encode($result);
            }
            $moreid["clientid"] = $clientid;
            $moreid["en"]["clientid"] = f_thisid_encode($clientid);
        }

    break;

    }

	$moreid['set'] = 1;
	$this->moreid = $moreid;

}



function core_app_id2name($app,$theid,$moreinfo=0){
	//input the app,id
	//output the standard name representation
	if ($theid) {
		switch($app){
			  case "card": $idtype = "contactid"; break;
			  case "client": $idtype = "clientid"; break;
			  case "staff": $idtype = "staffid"; break;
			  case "vendor": $idtype = "vendorid"; break;
			  case "product": $idtype = "productid"; break;
			  case "invoice": $idtype = "invid"; break;
		}

		$fieldsql = $this->core_app_id2name_sql($idtype);
		$sql = preg_replace("/ORDER BY/", " WHERE ".$fieldsql['this_key']." = '".$theid."' ORDER BY", $fieldsql['sql']);
		$query = $this->db->query($sql);
		$fieldvalue = $query->row_array();
		if ($fieldvalue) $result = $this->core_app_id2name_format($fieldsql,$fieldvalue);
    }

    if ($moreinfo) {
        switch ($app) {
			case "invoice":
			$clientid = app_convertid("invoiceid","clientid",$theid);
			$result .= " - ".core_app_id2name("client",$clientid);
			break;
        }
    }
	return($result);
}


function core_app_id2name_sql($idtype){
//input the whatid
//output the sql

        global $getthisid,$langinfo;

        $result['idtype'] = $idtype;

        switch ($idtype){

        case "contactid":
        $result['sql'] = "SELECT card_id,card_orgname,card_formatname,card_lname,card_fname FROM card ORDER BY card_fname,card_lname,card_orgname";
        $result['this_name'] = "card_id";
        $result['this_name2'] = "card_orgname";
        $result['this_name3'] = "card_formatname";
        $result['this_name4'] = "card_lname";
        $result['this_name5'] = "card_fname";
        $result['this_key'] = "card_id";
        $result['formattype'] = 1;
        break;

        case "clientid":
        $result['sql'] = "SELECT client.client_id,card.card_orgname,card.card_formatname,card.card_lname,card.card_fname FROM client LEFT JOIN `card` ON client.client_cardid  = card.card_id ORDER BY card_fname,card_lname,card_orgname";
        $result['this_name'] = "client_id";
        $result['this_name2'] = "card_orgname";
        $result['this_name3'] = "card_formatname";
        $result['this_name4'] = "card_lname";
        $result['this_name5'] = "card_fname";
        $result['this_key'] = "client_id";
        $result['formattype'] = 2;
        break;

        case "vendorid":
        $result['sql'] = "SELECT vendor.vendor_id,card.card_orgname,card.card_formatname,card.card_lname,card.card_fname FROM `vendor` LEFT JOIN `card` ON vendor.vendor_cardid  = card.card_id ORDER BY card_fname,card_lname,card_orgname";
        $result['this_name'] = "vendor_id";
        $result['this_name2'] = "card_orgname";
        $result['this_name3'] = "card_formatname";
        $result['this_name4'] = "card_lname";
        $result['this_name5'] = "card_fname";
        $result['this_key'] = "vendor_id";
        $result['formattype'] = 2;
        break;

        case "staffid":
        $result['sql'] = "SELECT staff.staff_id,card.card_orgname,card.card_formatname,card.card_lname,card.card_fname FROM staff LEFT JOIN `card` ON staff.staff_cardid  = card.card_id ORDER BY card_fname,card_lname";
        $result['this_name'] = "staff_id";
        $result['this_name2'] = "card_orgname";
        $result['this_name3'] = "card_formatname";
        $result['this_name4'] = "card_lname";
        $result['this_name5'] = "card_fname";
        $result['this_key'] = "staff_id";
        $result['formattype'] = 2;
        break;

        case "productid":
        $result['sql'] = "SELECT a_product_name,a_product_id FROM a_product ORDER BY a_product_categoryid DESC, a_product_name ASC";
        $result['this_name'] = "product_id";
        $result['this_name2'] = "a_product_name";
        $result['this_name3'] = "a_product_id";
        $result['this_key'] = "a_product_id";
        $result['formattype'] = 3;
        break;

        case "proddurationtype":
        $result['sql'] = "SELECT a_product_durationtype_id,a_product_durationtype_name FROM a_product_durationtype ORDER BY a_product_durationtype_name";
        $result['this_name'] = "a_product_durationtype_name";
        $result['this_key'] = "a_product_durationtype_id";
        break;

        case "invid":
        $result['sql'] = "SELECT a_invoice_id FROM a_invoice ORDER BY a_invoice_id";
        //WHERE a_subscription.a_subscription_clientid = 5 ORDER BY a_subscription_id";
        $result['this_name'] = "a_invoice_id";
        $result['this_key'] = "a_invoice_id";
        break;

        case "currency":
        $result['sql'] = "SELECT currency_id,currency_code,currency_default FROM global_setting.currency ORDER BY currency_default DESC, currency_code ASC";
        $result['this_name'] = "currency_code";
        $result['this_key'] = "currency_id";
        $this_defaultfield = "currency_default";
        break;

        case "taxid":
        $result['sql'] = "SELECT set_tax_id,set_tax_name,set_tax_default FROM global_setting.set_tax ORDER BY set_tax_default DESC, set_tax_name ASC";
        $result['this_name'] = "set_tax_name";
        $result['this_key'] = "set_tax_id";
        $this_defaultfield = "set_tax_default";
        break;

        case "subid":
        $result['sql'] = "SELECT a_subscription.a_subscription_clientid,a_subscription.a_subscription_id,a_product.a_product_name FROM a_subscription LEFT JOIN `a_product` ON a_subscription.a_subscription_productid = a_product.a_product_id ORDER BY a_subscription_id";
        //WHERE a_subscription.a_subscription_clientid = 5 ORDER BY a_subscription_id";
        $result['this_name'] = "subscription_id";
        $result['this_name2'] = "a_subscription_id";
        $result['this_name3'] = "client_id";
        $result['this_name4'] = "a_subscription_clientid";
        $result['this_name5'] = "a_product_name";
        $result['this_key'] = "a_subscription_id";
        $result['formattype'] = 4;
        break;

        case "subidbyclient":
        $result['sql'] = "SELECT a_subscription_clientid,a_subscription_id,a_product_name FROM a_subscription LEFT JOIN `a_product` ON a_subscription.a_subscription_productid = a_product.a_product_id";
        if ($getthisid["clientid"]) $sql1 .= " WHERE a_subscription.a_subscription_clientid = '".$getthisid["clientid"]."' ORDER BY a_subscription_id";
        //echo $sql1;
        $result['this_name'] = "subscription_id";
        $result['this_name2'] = "a_subscription_id";
        $result['this_name3'] = "client_id";
        $result['this_name4'] = "a_subscription_clientid";
        $result['this_name5'] = "a_product_name";
        $result['this_key'] = "a_subscription_id";
        $result['formattype'] = 4;
        break;

        case "xtragpid":
        $result['sql'] = "SELECT core_e_xtra_group_id,core_e_xtra_group_name FROM global_setting.core_e_xtra_group ORDER BY core_e_xtra_group_name";
        $result['this_name'] = "core_e_xtra_group_name";
        $result['this_key'] = "core_e_xtra_group_id";
        break;

        case "countrylist":
        $result['sql'] = "SELECT countries_name,countries_iso_2 FROM global_setting.countries ORDER BY countries_name";
        $result['this_name'] = "countries_name";
        $result['this_key'] = "countries_iso_2";
        break;

        case "timezone":
        $result['sql'] = "SELECT countries_timezone_zone,countries_timezone_".$this->lang->lang_use." FROM global_setting.countries_timezone ORDER BY countries_timezone_zone";
        $result['this_name'] = "countries_timezone_".$this->lang->lang_use;
        $result['this_key'] = "countries_timezone_zone";
        break;

        }

return($result);
}

function core_app_id2name_format($fieldsql,$fieldvalue){
//input the value
//output the representation

    global $lang;

    if (!isset($fieldsql['formattype'])) $fieldsql['formattype'] = "";

    switch ($fieldsql['formattype']){

    case "1":
    //contact id
        $result = "";
        if ($fieldvalue[$fieldsql['this_name5']] || $fieldvalue[$fieldsql['this_name4']]) $result = $fieldvalue[$fieldsql['this_name4']].", ".$fieldvalue[$fieldsql['this_name5']];
        if ($fieldvalue[$fieldsql['this_name2']]) {
            if ($result) $result .= " - ";
            $result .= $fieldvalue[$fieldsql['this_name2']];
        }
        $result .= " [".$lang['core'][$fieldsql['this_name']].":".$fieldvalue[$fieldsql['this_name']]."]";
        //}
        //$result = preg_replace('/,/', '&#44;', $result);

    break;

    case "2":
    //client,vendor,staff id
    //client id, use orgname if exist, else use formated name, else use last,first name

        if ($fieldvalue[$fieldsql['this_name2']]){
        $result = $fieldvalue[$fieldsql['this_name2']]." [".$lang['core'][$fieldsql['this_name']].":".$fieldvalue[$fieldsql['this_name']]."]";
        }elseif ($fieldvalue[$fieldsql['this_name3']]){
        $result = $fieldvalue[$fieldsql['this_name3']]." [".$lang['core'][$fieldsql['this_name']].":".$fieldvalue[$fieldsql['this_name']]."]";
        }else{
        $result = $fieldvalue[$fieldsql['this_name4']].", ".$fieldvalue[$fieldsql['this_name5']]." [".$lang['core'][$fieldsql['this_name']].":".$fieldvalue[$fieldsql['this_name']]."]";
        }
        //$result = preg_replace('/,/', '&#44;', $result);
    break;

    case "3":
    //product
        $result = $fieldvalue[$fieldsql['this_name2']]." [".$lang['core'][$fieldsql['this_name']].":".$fieldvalue[$fieldsql['this_name3']]."]";
    break;

    case "4":
    //subid,subidbyclient
        $result = $fieldvalue[$fieldsql['this_name5']]." [".$lang['core'][$fieldsql['this_name']].":".$fieldvalue[$fieldsql['this_name2']]."]"." [".$lang['core'][$fieldsql['this_name3']].":".$fieldvalue[$fieldsql['this_name4']]."]";
    break;

    default:
        $result = $fieldvalue[$fieldsql['this_name']];
    break;

    }

return($result);
}



function app_convertid($fromid,$toid,$theid){

    global $db;

    $sql = "SELECT * FROM core_convertid WHERE core_convertid_fromid = '$fromid' AND core_convertid_toid = '$toid' LIMIT 1";
    $this_array = $db->fetchRow($sql, 2);

    if ($this_array){
    $sql = "SELECT ".$this_array['core_convertid_tofield']." FROM ".$this_array['core_convertid_table']." WHERE ".$this_array['core_convertid_fromfield']." = '$theid' LIMIT 1";
    $result = $db->fetchOne($sql, 2);
    }

    if ($this_array['core_convertid_table2'] && $result){
    $sql = "SELECT ".$this_array['core_convertid_tofield2']." FROM ".$this_array['core_convertid_table2']." WHERE ".$this_array['core_convertid_fromfield2']." = '$result' LIMIT 1";
    $result = $db->fetchOne($sql, 2);
    }

    if ($this_array['core_convertid_table3'] && $result){
    $sql = "SELECT ".$this_array['core_convertid_tofield3']." FROM ".$this_array['core_convertid_table3']." WHERE ".$this_array['core_convertid_fromfield3']." = '$result' LIMIT 1";
    $result = $db->fetchOne($sql, 2);
    }

return($result);
}

}