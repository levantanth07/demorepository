<?php
class SendMagazineOrderForm extends Form
{
	function SendMagazineOrderForm()
	{
		Form::Form('SendMagazineOrderForm');
		//$this->link_js('packages/core/includes/js/jquery/jquery.validate.js');
		$this->add('full_name',new TextType(true,'full_name_invalid',0,50));
		//$this->add('address',new TextType(true,'address_invalid',0,1000));
		$this->add('email',new EmailType(false,'email_invalid'));
		$this->link_js('packages/core/includes/js/jquery/jquery.validate.js');
	}
	function on_submit()
	{
		if($this->check())
		{
			$packages = array(
				125400 => '6 tháng',
				237600 => '1 Năm'
			);
			$new_array=array(
							'full_name'=>$_REQUEST['full_name'],
							'gender'=>$_REQUEST['gender'],
							'address'=>$_REQUEST['address'],
							'country_id'=>$_REQUEST['country_id'],
							'phone'=>$_REQUEST['phone'],
							'email'=>$_REQUEST['email'],
							'description'=>'Đặt tạp chí gói <strong>'.System::display_number(Url::get('select_package')).'đ</strong> ('.$packages[Url::get('select_package')].')',
							'total_amount'=>Url::get('select_package'),
							'time'=>time(),
							'checked'=>0
							);
			if(Url::get('bank_transfer')){
				if($id=DB::insert('magazine_order',$new_array)){
					//unset($_SESSION['items']);
					$subject = (($new_array['gender']=='Nam'?'Ông':'Bà').' '.$new_array['full_name']).' đặt tạp chí Thích Ăn Phở '.$packages[Url::get('select_package')].'';
					$from = $new_array['email'];
					$mail_content = @file_get_contents('cache/email_template/magazine_order.html');
					$arr_replace = array(
						'[[|date|]]'=>date('d/m/Y'),
						'[[|full_name|]]'=>($new_array['gender']=='Nam'?'Ông':'Bà').' '.$new_array['full_name'],
						'[[|address|]]'=>$new_array['address'],
						'[[|country|]]'=>DB::fetch('SELECT id,name_'.Portal::language().' as name from zone where id = '.$new_array['country_id'].'','name'),
						'[[|phone|]]'=>$new_array['phone'],
						'[[|email|]]'=>$new_array['email'],
						'[[|description|]]'=>$new_array['description']
					);
					$_SESSION['description'] = $new_array['description'];
					$mail_content = strtr($mail_content,$arr_replace);
					//Portal::get_setting('email_support_online_bac')
					System::send_mail($from,'acc@thuonggiathitruong.vn',$subject,$mail_content);
					System::send_mail($from,$from,$subject,$mail_content);
					Url::redirect_current(array('action'=>'bank_transfer','id'=>User::encode_password($id)));
					//header('location:dat-tap-chi.html?action=bank_transfer&id='.User::encode_password($id));
				}
			}
		}
	}
	function draw()
	{
		$this->map['introduction'] = Portal::get_setting('contact_information');
		$this->map['gender_list'] = array(
			''=>'Chọn',
			'Nam'=>'Nam',
			'Nữ'=>'Nữ'
		);
		$this->map['country_id_list'] = String::get_list(DB::fetch_all('SELECT id,name_'.Portal::language().' as name FROM zone WHERE '.IDStructure::direct_child_cond(ID_ROOT,true).' ORDER BY name'));
		if(!Url::check('country_id')){
			$_REQUEST['country_id'] = 1;
		}
		if(Url::get('action')=='bank_transfer' and Url::get('id') and $order = DB::select('magazine_order','md5(concat(id,"vuonggialong")) = "'.Url::get('id').'"')){
			$layout = 'bank_transfer';
			$this->map['id'] = 'TGTT'.str_pad($order['id'],3,'0',STR_PAD_LEFT);
		}elseif(Url::get('action')=='online_payment'){
			$layout = 'online_payment';
		}else{
			$layout = 'layout';
		}
		$this->parse_layout($layout,$this->map);
	}
}
?>