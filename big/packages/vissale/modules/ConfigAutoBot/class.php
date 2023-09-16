<?php
class ConfigAutoBot extends Module
{
	function __construct($row)
	{
		if(User::is_login() and Session::get('admin_group')){
			Module::Module($row);
			require_once('db.php');
			switch(Url::get('cmd'))
			{
				case 'register_page':
					$this->register_page();
					break;
				case 'unregister_page':
					$this->unregister_page();
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
	function delete_file()
	{
		if(Url::get('link') and file_exists(Url::get('link')) and User::can_delete(false,ANY_CATEGORY))
		{
			@unlink(Url::get('link'));
		}
		echo '<script>window.close();</script>';
	}
	function register_page()
	{
		if(Url::get('page_id')) {
			DB::update('fb_pages', array('status' => "0"), 'page_id="' . Url::get('page_id') . '"');
			Url::js_redirect(true);
		}
	}
	function unregister_page()
	{
		if(Url::get('page_id')) {
			DB::update('fb_pages', array('status' => 1), 'page_id="' . Url::get('page_id') . '"');
			Url::js_redirect(true);
		}
	}
	function front_back()
	{
		require_once 'forms/front.php';
		$this->add_form(new FrontEndForm());
	}
	function fb_setting()
	{
		require_once 'forms/fb_setting.php';
		$this->add_form(new AccountConfigAutoBotForm());
	}
}
?>