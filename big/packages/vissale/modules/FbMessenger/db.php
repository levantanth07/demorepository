<?php 
class FbMessengerDB{
	static function save_image($field){
		require_once 'packages/core/includes/utils/upload_file.php';
		$dir = substr(PORTAL_ID,1).'/icon/';
		update_upload_file($field,$dir);
		if(Url::get($field)!=''){
			Portal::set_setting($field,Url::get($field),false,'PORTAL');
		}
	}
	static function get_settings(){
		$sql = '
			SELECT
				CONCAT(_key,"_",group_id) as id,
			  	`_key`,
			  	`value`
			FROM
				fb_cron_config
			WHERE
				group_id='.Session::get('group_id')
		;
		$items = DB::fetch_all($sql);
		return $items;
	}
	static function update_setting($key,$value){
		if(DB::exists('select `_key`,`value` from fb_cron_config WHERE `_key`="'.$key.'" and group_id='.Session::get('group_id'))){
			DB::update('fb_cron_config',array('value'=>$value),'`_key`="'.$key.'" and group_id='.Session::get('group_id'));
		}else{
			$arr = array(
					'group_id'=>Session::get('group_id'),
					'_key'=>$key,
					'value'=>$value
			);
			DB::insert('fb_cron_config',$arr);
		}
	}
	static function get_friendpages(){
		$sql = '
			SELECT
				fb_pages.*
			FROM
				fb_pages
			WHERE
				group_id='.Session::get('group_id').'
			ORDER BY
				fb_pages.status,fb_pages.page_name
			'
		;
		$items = DB::fetch_all($sql);
		return $items;
	}
}
?>