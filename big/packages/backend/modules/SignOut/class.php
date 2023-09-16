<?php
if(User::is_login()){
	if (Session::is_set('debuger_id')){
		if (Session::is_set('user_id')){
			$id=Session::get('user_id');
			// DB::update('account',array('last_online_time'=>time(),'session_id'=>''),'id="'.$id.'"');
			setcookie('user_id',"",time()-3600);
			//Session::delete('user_id');
			if(isset($_SESSION['user_id'])){
				unset($_SESSION['user_id']);
			}
			if(isset($_SESSION['exel_items'])){
				unset($_SESSION['exel_items']);
			}
			if(isset($_SESSION['group_id'])){
				unset($_SESSION['group_id']);
			}
			if(isset($_SESSION['admin_group'])){
				unset($_SESSION['admin_group']);
			}
			if(isset($_SESSION['account_type'])){
				unset($_SESSION['account_type']);
			}
			if(isset($_SESSION['master_group_id'])){
				unset($_SESSION['master_group_id']);
			}
            if(isset($_SESSION['callio_payload'])){
                unset($_SESSION['callio_payload']);
            }
            if(isset($_SESSION['voip24h_payload'])){
                unset($_SESSION['voip24h_payload']);
            }
			//System::debug($_SESSION);
			//echo 1;die;
			/*$url = 'https://vissale.com/logout';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);*/
		} 
		URL::redirect_url('/');
	}else{
		DB::update('account',array('last_online_time'=>time(),'session_id'=>''),'id="'.Session::get('user_id').'"');
		Session::delete('user_id');
		Session::delete('users_user_id');
		Session::delete('phone_store_id');
		Session::delete('admin_group');
		Session::delete('account_type');
		Session::delete('master_group_id');
		Session::delete('group_id');
		Session::delete('user_data');
		Session::delete('user_login');
        Session::delete('start_time_login');
        Session::delete('chup_anh_nhan_vien');
        Session::delete('id_login_user_photos');
        Session::delete('callio_payload');
        Session::delete('voip24h_payload');
		URL::redirect_url('/');
	}
} else {
	Url::access_denied();
}
?>
