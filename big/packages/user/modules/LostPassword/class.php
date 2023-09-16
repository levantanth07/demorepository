<?php
class LostPassword extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		if(Url::get('do')=='confirm' and Url::get('user') and $user=DB::select('account','md5(concat(id,"'.CATBE.'"))="'.Url::get('user').'"') and Url::get('pass')){
			DB::update('account',array('password'=>Url::get('pass')),'id ="'.$user['id'].'"');
			Url::js_redirect('dang-nhap','Quý khách đã đổi mật khẩu thành công!');
		}else{
			require_once 'forms/lost_password.php';
			$this->add_form(new LostPasswordForm);
		}
	}
}
?>
