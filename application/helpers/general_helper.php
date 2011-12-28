<?php

/*
function f_thisid_encode($thisid) {
	if ($thisid){
		$key = rand(1000, 9999);
		for ($i=0; $i<strlen($thisid); $i++) {
			$char = substr($thisid, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result .= $char;
		}
		//echo "---".$key."---".$result."!---";
		return ("n".$key.base64_encode($result));
	}
	//return ($thisid);
}

function f_thisid_decode($thisid){
	if (id_is_encrypted($thisid)){
		$result = '';
		$key = substr($thisid, 1, 4);
		$str = base64_decode(substr($thisid, 5));
		for($i=0; $i<strlen($str); $i++) {
			$char = substr($str, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result .= $char;
		}
	} else {
		$result = $thisid;
	}

	return explode('|', $result);
}
 */

function encode_id($id) {
	$CI =& get_instance();
	//return urlencode('n'.$CI->encrypt->encode($id));

	//$key = substr(base_url(), 1, 2);
	//$CI->encrypt->set_cipher(MCRYPT_BLOWFISH);
	//return 'n'.urlencode($CI->encrypt->encode($id, $key));

	return 'n'.encode_id2($id);
}

function decode_id($id) {
	$CI =& get_instance();
	//return $CI->encrypt->decode(substr(urldecode($id),1));

	//$key = substr(base_url(), 1, 2);
	//$encrypt = urldecode(substr($id, 0, 1));
	//$CI->encrypt->set_cipher(MCRYPT_BLOWFISH);
	//return $CI->encrypt->decode($encrypt, $key);

	return decode_id2($id);
}

function id_is_encrypted($id) {
	return (substr($id, 0, 1) == 'n');
}

function f_password_encrypt($password, $method=1){
	//$string='roshan'; //this is original string
	//$encrypted='d6dfb33a2052663df81c35e5496b3b1b'; //which is md5('roshan')
	//if(strcmp(md5($string),$encrypted)==0)

	switch ($method){
		case '1': $password = md5($password); break;
	}

	return($password);
}

function encode_id2($id){
	$result="";
	//$key = rand(1000, 9999);
	$key = substr(base_url(), 1, 2);
	for($i=0; $i<strlen($id); $i++) {
		$char = substr($id, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	}
	return $key.urlencode(base64_encode($result));
}

function decode_id2($id){
	$result="";
	//$key = substr($id, 1, 4);
	$key = substr(base_url(), 1, 2);

    $str = base64_decode(urldecode(substr($id, 3)));
    for($i=0; $i<strlen($str); $i++) {
      $char = substr($str, $i, 1);
      $keychar = substr($key, ($i % strlen($key))-1, 1);
      $char = chr(ord($char)-ord($keychar));
      $result.=$char;
    }
	return $result;
}





function get_return_url($default_url='') {
	$CI =& get_instance();

	if (!$CI->has_return) return $default_url;

	$seg = array('app','action','id','subaction');

	$result = array();
	foreach($seg AS $s) {
		if ($CI->re_url[$s] !== FALSE) $result[] = $CI->re_url[$s];
	}

	return implode('/', $result);
}

function set_return_url($querystring=FALSE) {
	$seg = array('app','action','id','subaction');
	$result = array();
	$CI =& get_instance();

	if ($querystring) {
		foreach($seg AS $s) {
			if ($CI->url[$s] === FALSE && $CI->url[$s] != '') continue;
			$result[] = 're_'.$s.'='.$CI->url[$s];
		}
		return '?'.implode('&', $result);
	} else {
		$CI->load->helper('form');
		foreach($seg AS $s) {
			if ($CI->url[$s] === FALSE && $CI->url[$s] != '') continue;
			$result[] = form_hidden('re_'.$s, $CI->url[$s]);
		}
		return implode("\n\r", $result);
	}
}

function reset_return_url_form() {
	$seg = array('app','action','id','subaction');
	$result = array();
	$CI =& get_instance();

	$CI->load->helper('form');
	foreach($seg AS $s) {
		if ($CI->re_url[$s] === FALSE && $CI->re_url[$s] != '') continue;
		$result[] = form_hidden('re_'.$s, $CI->re_url[$s]);
	}

	if (count($result)==0) return '';
	return implode('<br />', $result);
}

function execute_return_url($link_only=FALSE) {
	$CI =& get_instance();

	if (!$CI->has_return) return NULL;
	if ($link_only) return get_return_url();

	redirect(get_return_url());
}





function meg($mescode=0, $details=''){
	$CI =& get_instance();
//process pass in message code
//go database get message in user's language and message type
//save in megshow
//type
//1=notice
//2=wanning

//meg(999,"data_form.inc: Form with more then one table but no field on child table selected.");

	$messages = array(
		100 => 'No such App exist in this installation:',
		101 => 'No such Action in App:',
		102 => 'ID inconsistency.:',
		999 => 'Exit:',
	);

	$message = '<div id="messageinner">'.$messages[$mescode];
	if ($details != '') $message .= '['.$details.']';
	$message .= '</div>';
	$CI->system_messages[] = $message;

	// if code 999, show message and exit the application
	if ($mescode == '999'){
		echo megshow();
		exit;
	}
}


function megshow(){
	$CI =& get_instance();
	$message = '';

	if (count($CI->system_messages) > 0) $message = '<div id="message">' . implode('', $CI->system_messages) . '</div>';

	return $message;
}




function get_template() {
	$CI =& get_instance();
	return $CI->layout['name'];
}


function get_current_stamp() {
	$timestamp = time();
	$timestamp = $timestamp - date('Z');
	return mdate("%Y-%n-%j %H:%i:%s", $timestamp);
}

function parse_stamp($stamp, $format='ISO') {
	$timestamp = strtotime($stamp);

	return parse_timestamp($timestamp, $format);
}

function parse_stamp_user($stamp, $format='ISO') {
	$timestamp = strtotime($stamp);

	$CI =& get_instance();
	$timezone = $CI->UserM->get_timezone();
	$timestamp = $timestamp + ((int)$timezone*60*60);

	return parse_timestamp($timestamp, $format);
}

function parse_timestamp($timestamp, $format='ISO') {
	switch($format) {
		case 'ISO':
			$format_string = '%Y/%m/%d %H:%i:%s';
			break;
		case 'ISO_DATE':
			$format_string = '%Y/%m/%d';
			break;
		case 'ISO_TIME':
			$format_string = '%H:%i:%s';
			break;
		case 'ATOM':
			return standard_date('DATE_ATOM', $timestamp);
			break;
		case 'ISO_8601':
			//return standard_date('DATE_ISO8601', $timestamp);
			return date('c', $timestamp);
			break;
		default:
			$format_string = $format;
			break;
	}

	return mdate($format_string, $timestamp);
}


function extract_distinct_values($data, $key) {
	$result = array();
	foreach($data AS $d) {
		$result[$d[$key]] = '';
	}
	return array_keys($result);
}


function byte_size($bytes) {
	$size = $bytes / 1024;
	if($size < 1024)
		{
		$size = number_format($size, 2);
		$size .= ' KB';
		}
	else
		{
		if($size / 1024 < 1024)
			{
			$size = number_format($size / 1024, 2);
			$size .= ' MB';
			}
		else if ($size / 1024 / 1024 < 1024)
			{
			$size = number_format($size / 1024 / 1024, 2);
			$size .= ' GB';
			}
		}
	return $size;
}

/** Input /a/b/file.jpg
 * returns dirpath /a/b and filename file.jpg
 */
function explode_filename_dirpath ($path) {
	$i = explode($path);
	$filename = array_pop($i);
	return array('dirpath' =>$i, 'filename'=>$filename);
}

function array_reverse_order($array) {
	return array_combine(array_reverse(array_keys($array)), array_reverse(array_values($array)));
}