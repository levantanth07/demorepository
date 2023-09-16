<?php
class Banner extends Module
{
	function __construct($row)
	{
		Module::Module($row);
        if(!Url::get('page')){
            Portal::$image_url = Portal::get_setting('image_url');
        }
        if($acc_id = DB::escape(Url::get('ref')) and DB::exists('select id from account where id="'.$acc_id.'"')){
            if(!isset($_COOKIE['ref_account_id']) and !$_COOKIE['ref_account_id']){
                setcookie('ref_account_id',$acc_id,time() + (86400 * 30), "/");
            }
        }

		require_once 'db.php';
		require_once 'forms/list.php';
		$this->add_form(new BannerForm());
	}
}
?>