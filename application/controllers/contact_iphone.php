<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact_iphone extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('CardM');
		$this->load->model('Card_AddressM');
		$this->load->model('Card_EmailM');
		$this->load->model('Card_SocialM');
		$this->load->model('Card_TelM');
		$this->load->model('InvoiceM');
		$this->load->model('HelpdeskM');
	}

	/*--Iphone--*/
	function index(){
		if(isset($_POST)){
			//$_POST['id'] = $_POST['id'];
			$addon_email = $_POST['addon_email'];
			$addon_tel = $_POST['addon_tel'];
			$addon_address = $_POST['addon_address'];
			$addon_social = $_POST['addon_social'];
			$addon_notes = $_POST['addon_notes'];
			$addon_extra = $_POST['addon_extra'];
			
			/*--Save email--*/
			$addon_email = explode(';',$addon_email);
			for($i=0 ; $i<count($addon_email) ; $i++){
				$email = explode(',',$addon_email[$i]);
					$_POST['addon_email'][$i]['id'] = trim($email[0]);
					$_POST['addon_email'][$i]['email'] = trim($email[1]);
					$_POST['addon_email'][$i]['type'] = trim($email[2]);
					$_POST['addon_email'][$i]['is_default'] = trim($email[3]);
			}
			/*--Save phone--*/
			$addon_tel = explode(';',$addon_tel);
			for($i=0 ; $i<count($addon_tel) ; $i++){
				$tel = explode(',',$addon_tel[$i]);
					$_POST['addon_tel'][$i]['id'] = trim($tel[0]);
					$_POST['addon_tel'][$i]['type'] = trim($tel[1]);
					$_POST['addon_tel'][$i]['number'] = trim($tel[2]);
					$_POST['addon_tel'][$i]['country'] = trim($tel[3]);
					$_POST['addon_tel'][$i]['area'] = trim($tel[4]);
					$_POST['addon_tel'][$i]['extension'] = trim($tel[5]);
					$_POST['addon_tel'][$i]['is_default'] = trim($tel[6]);
			}
			/*--Save address--*/
			$addon_address = explode(';',$addon_address);
			for($i=0 ; $i<count($addon_address) ; $i++){
				$add = explode(',',$addon_address[$i]);
					$_POST['addon_address'][$i]['id'] = trim($add[0]);
					$_POST['addon_address'][$i]['type'] = trim($add[1]);
					$_POST['addon_address'][$i]['line_1'] = trim($add[2]);
					$_POST['addon_address'][$i]['line_2'] = trim($add[3]);
					$_POST['addon_address'][$i]['is_default'] = trim($add[4]);
			}
			/*--Save social--*/
			$addon_social = explode(';',$addon_social);
			for($i=0 ; $i<count($addon_social) ; $i++){
				$social = explode(',',$addon_social[$i]);
					$_POST['addon_social'][$i]['id'] = trim($social[0]);
					$_POST['addon_social'][$i]['type'] = trim($social[1]);
					$_POST['addon_social'][$i]['name_id'] = trim($social[2]);
			}
			/*--Save notes--*/
			$addon_notes = explode(';',$addon_notes);
			for($i=0 ; $i<count($addon_notes) ; $i++){
				$note = explode(',',$addon_notes[$i]);
					$_POST['addon_notes'][$i]['id'] = trim($note[0]);
					$_POST['addon_notes'][$i]['note'] = trim($note[1]);
			}
			/*--Save extra--*/
			$addon_extra = explode(';',$addon_extra);
			for($i=0 ; $i<count($addon_extra) ; $i++){
				$extra = explode(',',$addon_extra[$i]);
					$_POST['addon_extra'][$i]['id'] = trim($extra[0]);
					$_POST['addon_extra'][$i]['gender'] = trim($extra[1]);
					$_POST['addon_extra'][$i]['birth_date'] = trim($extra[2]);
			}

			$id_save = $this->CardM->save();
			echo 'success';
		}
	}
}