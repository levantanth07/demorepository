<?php
class LostPasswordForm extends Form
{
	function __construct()
	{
		Form::Form('LostPasswordForm');
		$this->add('email',new EmailType(true,'invalid_email'));
		$this->link_js('assets/standard/js/jquery.js');
		$this->link_js('assets/standard/js/bootstrap.min.js');
		$this->link_js('packages/core/includes/js/jquery/jquery.cookie.js');
		
		$this->link_css('assets/standard/css/signin.css');
		$this->link_css('assets/standard/css/bootstrap.min.css');
	}
	function on_submit()
	{
		if ($this->check())
		{
			if($row=DB::fetch('select * from party where email ="'.Url::get('email').'"'))
			{
				$new_password=$this->random_string();
				//echo $new_password;
				//$items=DB::fetch('select * from party where email ="'.Url::get('email').'"');
				$user=$row['user_id'];
				$link='https://app.tuha.vn/'.Url::build('quen-mat-khau').'&user='.User::encode_password($user).'&pass='.User::encode_password($new_password).'&do=confirm';
				$messenger=file_get_contents('packages/user/modules/LostPassword/forms/messenger.html');
				$message=str_replace('[[|password|]]',$new_password,$messenger);
				$message=str_replace('[[|user|]]',$user,$message);
				$message=str_replace('[[|link|]]',$link,$message);
				$subject='QLBH: Password recovery - Khoi phuc mat khau!';
				if($email = Url::get('email') and System::send_mail('noreply.tuha@gmail.com',addslashes($email),$subject,$message))
				{
					//DB::update('account',array('password'=>User::encode_password($new_password)),'id ="'.$user.'"');
					echo '<script>window.location = "'.Url::build('quen-mat-khau',['action'=>'success']).'"</script>';
					//Url::redirect_current(array('action'=>'success'));
				}else{
				    die;
					Url::redirect_current(array('action'=>'error'));
				}
			}
			else
			{
				$this->error('email','Email này chưa đăng ký.');
			}	
		}		
		
	}
	function draw()
	{	
		$this->parse_layout('lost_password');		
	}
	function random_string()
	{
		$len = 8;
		$base='ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz123456789';
		$max=strlen($base)-1;
		$activatecode='';
		mt_srand((double)microtime()*1000000);
		while (strlen($activatecode)<$len+1)
		  $activatecode.=$base{mt_rand(0,$max)};		  
		return $activatecode;
	}
	
}
?>
