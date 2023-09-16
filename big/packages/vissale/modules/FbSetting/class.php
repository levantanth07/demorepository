<?php
class FbSetting extends Module
{
	function __construct($row)
	{
	    require_once('packages/vissale/lib/php/vissale.php');
		if(User::is_login() and Session::get('group_id') and Session::get('admin_group')){
			Module::Module($row);
			require_once('db.php');
			switch(Url::get('cmd'))
			{
                case 'import_vichat_page':
                    $this->import_vichat_page(Url::get('page_id'),Url::get('name'));
                    break;
				case 'delete':
					$this->delete_page();
					break;
				case 'reply_bank':
					$this->reply_bank();
					break;
				case 'register_page':
					$this->register_page(Url::get('app_type'));
					break;
				case 'unregister_page':
					$this->unregister_page(Url::get('app_type'));
					break;
				case 'front_end':
					$this->front_back();
					break;
				case 'unlink':
					$this->delete_file();
					break;
				default:
					$this->fb_setting();
					break;
			}
		}
		else
		{
			Url::access_denied();
		}
	}
    function import_vichat_page($page_id,$name){
        require_once 'packages/backend/modules/SignIn/db.php';
        if($page_id and $name){
            $group_id =  Session::get('group_id');
			$page_id = DB::escape($page_id);
			$name = DB::escape($name);
            if($row=DB::fetch('select id,page_id,page_name from fb_pages where page_id="'.$page_id.'" and group_id='.$group_id)){
                DB::update('fb_pages',['page_name'=>$name,'status'=>2],'id='.$row['id']);
            }else{
                DB::insert('fb_pages',['status'=>2,'page_name'=>$name,'page_id'=>$page_id,'group_id'=>$group_id,'created'=>date('Y-m-d H:i:s')],'id='.$row['id']);
            }
            echo '{"RESULT":1}';
        }else{
            echo '{"RESULT":0}';
        }
        exit();
    }
	function delete_page(){
		if(User::can_admin(false,ANY_CATEGORY) and $id = DB::escape(Url::get('id')) and $row=DB::fetch('select id,page_id from fb_pages where id='.$id)){
			DB::delete('fb_pages','id='.$id);
			DB::delete('fb_page_reply_bank','page_id='.$id);
			DB::delete('fb_posts','page_id="'.$row['page_id'].'"');
			DB::delete('fb_post_comments','page_id="'.$row['page_id'].'"');
			Url::js_redirect(true,'Xóa thành công',array('new'=>1));
		}
	}
	function delete_file()
	{
		if(Url::get('link') and file_exists(Url::get('link')) and User::can_delete(false,ANY_CATEGORY))
		{
			@unlink(Url::get('link'));
		}
		echo '<script>window.close();</script>';
	}
	function register_page($app_type='vissale_app')//custom_app
	{
		if(Url::get('page_id')) {
			if($app_type=='vissale_app'){
				$arr = array('subscribed_messenger'=>1,'status'=>0);
			}else{
				$arr = array('subscribed_customer_app'=>1,'status'=>0);
			}
			$url = 'https://admin.tuha.vn/fb_module/api/subscribed_apps.php?page_id='.Url::get('page_id').'&act=active&app_type='.$app_type;
			$content = file_get_contents($url);
			/*$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			//curl_exec('https://login.vissale.com/api/cc.php');
			shell_exec("curl {https://login.vissale.com/api/cc.php}");
			curl_close($ch);*/
			if($content){
				DB::update('fb_pages',$arr , 'page_id="' . DB::escape(Url::get('page_id')) . '"');
				//die('Đăng ký thành công!');
				Url::js_redirect(true,'Đăng ký thành công (^_^) ...',array('new'=>1));
			}else{
				Url::js_redirect(true,'Đăng ký không thành công (*_*)',array('new'=>1));
			}
		}
	}
	function unregister_page($app_type='vissale_app')
	{
		if(Url::get('page_id')) {
			if($app_type=='vissale_app'){
				$arr = array('subscribed_messenger'=>0,'status'=>0);
			}else{
				$arr = array('subscribed_customer_app'=>0,'status'=>0);
			}
			DB::update('fb_pages', $arr, 'page_id="' . DB::escape(Url::get('page_id')) . '"');
			$url = 'https://admin.tuha.vn/fb_module/api/subscribed_apps.php?page_id='.Url::get('page_id').'&act=deactive&app_type='.$app_type;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			//curl_exec('https://login.vissale.com/api/cc.php');
			shell_exec("curl {https://login.tuha.vn/api/cc.php}");
			curl_close($ch);
			Url::js_redirect(true,'Dữ liệu đang được xử lý ...',array('new'=>1));
		}
	}
	function reply_bank(){
		require_once 'forms/reply_bank.php';
		$this->add_form(new ReplyBankForm());
	}
	function front_back()
	{
		require_once 'forms/front.php';
		$this->add_form(new FrontEndForm());
	}
	function fb_setting()
	{
		require_once 'forms/fb_setting.php';
		$this->add_form(new AccountFbSettingForm());
	}
}
?>