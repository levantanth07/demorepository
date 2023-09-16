<?php
class MagazineOrder extends Module
{
	function MagazineOrder($row)
	{
		Module::Module($row);
		require_once 'db.php';
		switch(URL::get('cmd'))
		{
			case 'check_ajax':
				$this->check(); break;
			default:
				require_once 'forms/list.php';
				$this->add_form(new SendMagazineOrderForm());
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
