<?php
class AccountFbSettingForm extends Form{
    protected $map;
	function __construct(){
		Form::Form('AccountFbSettingForm');
		$this->link_css('assets/default/css/cms.css');
		$this->link_css('assets/default/css/jquery/tabs.css');
		//$this->link_js('assets/default/css/tabs/tabpane.js');
	}
	function on_submit(){
		if(Url::get('cmd') == 'save'){
			foreach($_REQUEST as $key=>$value){
				if(preg_match('/config_(.*)/',$key,$matches)){
					FbSettingDB::update_setting($matches[1],$value);
				}
			}
			Url::js_redirect(true,'Dữ liệu đã cập nhật...!',array('new'=>1));
		}
	}
	function draw(){
	    ini_set('display_errors',1);
		$this->map = array();
		$group_id = Session::get('group_id');
		$settings = FbSettingDB::get_settings();
		foreach($settings as $key=>$value){
			if(!isset($_REQUEST['config_'.$value['_key']])){
				$_REQUEST['config_'.$value['_key']] = $value['value'];
			}
		}
		$arr = array(0=>'Không',1=>'Có');
		$this->map += array(
				'config_like_comment_list'=>$arr
				,'config_hide_phone_comment_list'=>$arr
				,'config_hide_nophone_comment_list'=>$arr
				,'config_reply_conversation_list'=>$arr
		);
		$cond = '1=1';
		if($keyword= DB::escape(trim(Url::get('keyword')))){
			$cond .= ' AND (
				fb_pages.page_id like "%'.$keyword.'%" 
				OR fb_pages.page_name like "%'.$keyword.'%"
				OR groups.name like "%'.$keyword.'%"
			)';
		}
		$cond .= ' AND fb_pages.group_id='.$group_id.'';
		//echo $cond;
		$this->map['pages'] = MiString::array2js(FbSettingDB::get_friendpages($cond));
		$this->map['user_id'] = DB::fetch('select id from users where username="'.Session::get('user_id').'"','id');
		$this->map['md5_user_id'] = md5('vs'.$this->map['user_id']);
		if(Url::get('new')==1){
			$layout = 'fb_setting_new';
		}else{
			$layout = 'fb_setting';
		}
		$layout = 'fb_setting';
		$vichat_pages = FbSettingDB::get_vichat_pages($group_id);
		$this->map['vichat_pages'] = $vichat_pages;
		$this->parse_layout($layout,$this->map);
	}
}
?>