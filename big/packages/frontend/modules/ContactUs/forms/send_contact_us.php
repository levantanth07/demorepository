<?php
class SendContactUsForm extends Form{
	function __construct(){
		Form::Form('SendContactUsForm');
		//$this->link_js('packages/core/includes/js/jquery/jquery.validate.js');
		$this->add('full_name',new TextType(true,'full_name_invalid',0,50));
		//$this->add('address',new TextType(true,'address_invalid',0,1000));
		$this->add('email',new EmailType(false,'email_invalid'));
		//$this->add('title',new TextType(true,'title_invalid',0,1000));
		$this->add('content',new TextType(true,'content_invalid',0,5000));
	}
	function on_submit(){
		$captcha = $_SESSION["captcha"]['code'];
		if($this->check()){
			if(Url::get("captcha") == $captcha and !empty($captcha)){
				$content = Url::get('content').'<br>';
				$new_array=array(
								'name'=>$_REQUEST['full_name'],
								'email'=>$_REQUEST['email'],
								'phone'=>$_REQUEST['phone'],
								'address'=>$_REQUEST['address'],
								'time'=>time(),
								'content'=>$content,
								'status'=>'UNCENSOR',
								'portal_id'=>PORTAL_ID
								);
				if(DB::insert('contact',$new_array)){
					unset($_SESSION['items']);
					//if(Portal::get_setting('received_notification_from_contact')==1){
						$subject = 'Liên hệ';
						$from = $new_array['email'];
						$mail_content = @file_get_contents('cache/email_template/contact.html');
						$arr_replace = array(
							'[[date]]'=>date('d/m/Y'),
							'[[full_name]]'=>$new_array['name'],
							'[[address]]'=>$new_array['address'],
							'[[email]]'=>$new_array['email'],
							'[[phone]]'=>$new_array['phone'],
							'[[content]]'=>$new_array['content']
						);
						$mail_content = strtr($mail_content,$arr_replace);
						if(Portal::get_setting('company_email')){
							System::send_mail($from,Portal::get_setting('company_email'),$subject,$mail_content);
						}
					}
					echo '<script>alert("'.Portal::get_setting('contact_notification_text_'.Portal::language().'').'");window.location ="lien-he.html?action=success"</script>';
					exit();
			}else{
			 	$this->error('captcha','invalid_captcha');
				//return;
			}
		}
	}
	function draw(){
		$this->map['introduction'] = Portal::get_setting('contact_information');
		$this->parse_layout('layout',$this->map);
	}
}
?>