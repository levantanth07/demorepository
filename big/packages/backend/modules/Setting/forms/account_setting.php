<?php
class AccountSettingForm extends Form{
	function __construct(){
		Form::Form('AccountSettingForm');
		$this->link_css('assets/default/css/cms.css');
		$this->link_css('assets/default/css/jquery/tabs.css');
		//$this->link_js('assets/default/css/tabs/tabpane.js');
	}
	function on_submit(){
		if(Url::get('cmd') == 'save'){
			foreach($_REQUEST as $key=>$value){
				if(preg_match('/config_(.*)/',$key,$matches)){
					if($key == 'config_product_module_enable'){
						if($value){
							$status = 'SHOW';
						}else{
							$status = 'HIDE';
						}
						$sql = '
							update
								function
							SET
								status="'.$status.'"
							WHERE
								'.IDStructure::child_cond(DB::structure_id('function',118)).'
							';
						DB::query($sql);
						header('location:?page=function&cmd=export_cache');
						header('location:?page=setting'.''.(Url::get('a')?'&a='.Url::get('a'):''));
					}
					Portal::set_setting($matches[1],$value,false,'PORTAL');
				}
			}
			if($_FILES){
				foreach($_FILES as $key=>$value){
					if(preg_match('/config_(.*)/',$key,$matches)){
						SettingDB::save_image($matches[1]);
					}
				}
			}
			Session::delete('portal');
			Url::redirect_current(array('a'));
		}
	}
	function draw(){
		//require_once Portal::template_js('core').'/tinymce/init_tinyMCE.php';
		if(!can_tuha_administrator()){
			Url::access_denied();
		}
		$languages = DB::select_all('language');
		$is_active = array(
			0=>Portal::language('Stop'),
			1=>Portal::language('Runing')
		);
		if(Portal::$current->settings){
			foreach(Portal::$current->settings as $key=>$value){
				if(is_string($value) and !isset($_REQUEST['config_'.$key])){
					$_REQUEST['config_'.$key] = $value;
				}
			}
		}
		$arr = array(0=>'Không',1=>'Có');
		$this->parse_layout('account_setting',array(
			'config_language_default_list'=>MiString::get_list($languages)
			,'config_rewrite_list'=>$arr
			,'languages'=>$languages
			,'config_is_active_list'=>$is_active
			,'config_use_cache_list'=>$arr
			,'config_use_double_click_list'=>$arr
			,'config_use_log_list'=>$arr
			,'config_use_recycle_bin_list'=>$arr
			,'config_received_notification_from_contact_list'=>$arr
			,'config_representative_office'=>$arr
			,'prefix'=>''//PREFIX
			,'config_product_module_enable_list'=>$arr
			,'config_product_module_show_price_list'=>$arr
			,'config_product_module_cart_list'=>$arr
			,'config_news_enable_module_list'=>$arr
			,'config_service_enable_module_list'=>$arr
            ,'config_display_errors_list'=>[''=>'Chọn']+$arr
		));
	}
}
?>