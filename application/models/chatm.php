<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

    class ChatM extends MY_Model {
        function __construct() {
            parent::__construct();
	}
        function forward(){
            $ch = curl_init("http://46.137.219.16:5280/http-bind");
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $header = array('Accept-Encoding: gzip, deflate','Content-Type: text/xml; charset=utf-8');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            $output = '';
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            echo $output;
	}
    }