	<?php 
class FbSettingDB{
	static function save_image($field){
		require_once 'packages/core/includes/utils/upload_file.php';
		$dir = substr(PORTAL_ID,1).'/icon/';
		update_upload_file($field,$dir);
		if(Url::get($field)!=''){
			Portal::set_setting( DB::escape($field), DB::escape(Url::get($field)),false,'PORTAL');
		}
	}
	static function get_page_reply_bank($page_id){
		$page_id = DB::escape($page_id);
		$sql = '
			SELECT
					id,
			  	`_key`,
			  	`content` as value
			FROM
				fb_page_reply_bank
			WHERE
				fb_page_reply_bank.page_id='.$page_id
		;
		$items = DB::fetch_all($sql);
		return $items;
	}
	static function update_page_reply_bank($page_id,$key,$content){
		$page_id = DB::escape($page_id);
		$key = DB::escape($key);
		$content = DB::escape($content);
		if($row=DB::fetch('select id from fb_page_reply_bank WHERE _key="'.$key.'"  and `page_id`="'.$page_id.'"')){
			DB::update('fb_page_reply_bank',array('_key'=>$key,'content'=>$content),'id='.$row['id']);
		}else{
			$arr = array(
					'page_id'=>$page_id,
					'_key'=>$key,
					'content'=>$content,
					'created_time'=>time(),
					'created_acc_id'=>Session::get('user_id'),
					'actived'=>1
			);
			DB::insert('fb_page_reply_bank',$arr);
		}
	}
	static function get_settings($group_id=false){
		$group_id = $group_id?$group_id:Session::get('group_id');
		$sql = '
			SELECT
				CONCAT(_key,"_",group_id) as id,
			  	`_key`,
			  	`value`
			FROM
				fb_cron_config
			WHERE
				group_id='.$group_id
		;
		$items = DB::fetch_all($sql);
		return $items;
	}
	static function update_setting($key,$value){
		$key = DB::escape($key);
		$value = DB::escape($value);
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
	static function get_friendpages($cond='1=1'){
		$sql = '
			SELECT
				fb_pages.page_id as id,fb_pages.page_name,
				fb_pages.page_id,fb_pages.subscribed_messenger,
				fb_pages.subscribed_customer_app,
				fb_pages.token,fb_pages.messenger_token,
				groups.name as group_name
			FROM
				fb_pages
				left outer JOIN `groups` ON groups.id = fb_pages.group_id
			WHERE
				'.$cond.'
			ORDER BY
				fb_pages.id DESC
			LIMIT 0,1000
			'
		;
		$items = DB::fetch_all($sql);
		return $items;
	}
	static function get_settings_($group_id=false){
		$group_id = $group_id?$group_id:Session::get('group_id');
		$sql = '
			SELECT
				CONCAT(_key,"_",group_id) as id,
			  	`_key`,
			  	`value`
			FROM
				fb_cron_config
			WHERE
				group_id='.$group_id.'
				and (
					_key = "reply_comment_has_phone"
					OR _key = "reply_comment_nophone"
					OR _key = "reply_conversation_has_phone"
					OR _key = "reply_conversation_nophone"
			)'
		;
		$items = DB::fetch_all($sql);
		return $items;
	}
	static function clone_reply_to_page(){
		$groups = DB::fetch_all('select id from `groups`');
		foreach($groups as $key=>$val){
			$pages = DB::fetch_all('select id from fb_pages where group_id='.$key);
			$settings = FbSettingDB::get_settings_($key);
			foreach($pages as $k=>$v){
				foreach($settings as $k1=>$v1){
					FbSettingDB::update_page_reply_bank($k,$v1['_key'],$v1['value']);
				}
			}
		}
	}
	static function get_vichat_pages($group_id){
	    $group_id = 2056;
	    $url = 'https://api-vichat.tuha.vn/api/pages/get-by-group?shop_id='.$group_id;
	    $result_arr = [];
	    try{
            ini_set("allow_url_fopen", 1);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = curl_exec($ch);
            curl_close($ch);
            $obj = json_decode($result);
            $obj_ = object_to_array($obj);
			if(is_array($obj_) && count($obj_)> 0){
				foreach($obj_ as $key=>$value){
					$result_arr[$value['id']]['id'] = $value['id'];
					$result_arr[$value['id']]['name'] = $value['name'];
				}
			}
            
        }catch (Exception $e){
			echo "Lỗi lấy thông tin nhóm từ Palbox. Vui lòng thử lại sau.";
        }
        return $result_arr;
    }
}
?>