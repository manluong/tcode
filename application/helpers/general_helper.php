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
	$CI = get_instance();
	return 'n'.$CI->encrypt->encode($id);
}

function decode_id($id) {
	$CI = get_instance();
	return $CI->encrypt->decode(substr($id,1));
}

function id_is_encrypted($id) {
	return (substr($id, 0, 1) == 'n');
}

function f_password_encrypt($password, $method=1){
	//$string='roshan'; //this is original string
	//$encrypted='d6dfb33a2052663df81c35e5496b3b1b'; //which is md5('roshan')
	//if(strcmp(md5($string),$encrypted)==0)

	switch ($method){
		case "1": $password = md5($password); break;
	}

	return($password);
}