<?php
class ContactUs extends Module{
	function __construct($row){
		Module::Module($row);
		require_once 'db.php';
		switch(URL::get('cmd')){
			case 'check_ajax':
				$this->check(); break;
			default:
				Portal::$document_title = Portal::language('contact');
				require_once 'forms/send_contact_us.php';
				$this->add_form(new SendContactUsForm());
		}
	}
	function check(){
		if($comfirm_code = Url::get('verify_confirm_code')){
			if($comfirm_code == Session::get('security_code')) echo 'true';
			else echo 'false';
		}
		exit();
	}
}
?>
