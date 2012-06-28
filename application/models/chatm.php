<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

    class ChatM extends MY_Model {
        function __construct() {
            parent::__construct();
	}
        function forward(){
            $ch = curl_init("http://jabber.8fcloud.net:5280/http-bind");
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
        function add_user($name,$company,$realname){
            if($realname == ''){
                $realname = $this->UserM->get_name();
            }
            if($name == ''){
                $name = $this->UserM->get_id();
            }

            $context = stream_context_create(array(
                    'http' => array(
                            'header'  => "Authorization: Basic bGFtcHA6dGVsY29zb25AMTk="
                    )
            ));
            $content = file_get_contents("http://jabber.8fcloud.net:8080/xmpp/create.php?name=$name&realname=".base64_encode($realname)."&company=$company",false,$context);
            return $content;
        }
        function delete_user($name,$company){
            $context = stream_context_create(array(
                    'http' => array(
                            'header'  => "Authorization: Basic bGFtcHA6dGVsY29zb25AMTk="
                    )
            ));
            $content = file_get_contents("http://jabber.8fcloud.net:8080/xmpp/delete.php?name=$name&company=$company",false,$context);
            return $content;
        }
    }