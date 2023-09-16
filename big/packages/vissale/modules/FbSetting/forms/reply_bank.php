<?php
class ReplyBankForm extends Form{
	function __construct(){
		Form::Form('ReplyBankForm');
		$this->link_css('assets/default/css/cms.css');
		$this->link_css('assets/default/css/jquery/tabs.css');
		//$this->link_js('assets/default/css/tabs/tabpane.js');
	}
	function on_submit(){
		if(Url::get('cmd') == 'reply_bank' and Url::iget('page_id')){
			foreach($_REQUEST as $key=>$value){
				if(preg_match('/config_(.*)/',$key,$matches)){
					FbSettingDB::update_page_reply_bank(Url::iget('page_id'),$matches[1],$value);
				}
			}
			Url::js_redirect(true,'Dữ liệu đã cập nhật...!',array('new'=>1));
		}
	}
	function draw(){
		$this->map = array();
		$settings = FbSettingDB::get_page_reply_bank(Url::iget('page_id'));
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
		$cond .= ''.(User::is_admin()?'':' AND group_id='.Session::get('group_id').'').'';
		//echo $cond;
		$this->map['pages'] = FbSettingDB::get_friendpages($cond);
		$this->map['user_id'] = DB::fetch('select id from users where username="'.Session::get('user_id').'"','id');
		$this->map['md5_user_id'] = md5('vs'.$this->map['user_id']);
		$layout = 'reply_bank';
		//FbSettingDB::clone_reply_to_page();
		$this->parse_layout($layout,$this->map);
	}
}
?>