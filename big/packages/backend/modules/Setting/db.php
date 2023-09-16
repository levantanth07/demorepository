<?php 
class SettingDB{
	static function save_image($field){
		require_once 'packages/core/includes/utils/upload_file.php';
		$dir = substr(PORTAL_ID,1).'/icon/';
		update_upload_file($field,$dir,'IMAGE',false,false,false);
		if(Url::get($field)){
			Portal::set_setting($field,Url::get($field),false,'PORTAL');
		}
	}
}
?>