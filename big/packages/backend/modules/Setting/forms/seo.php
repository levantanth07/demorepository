<?php
class SettingForm extends Form
{
	function __construct()
	{
		Form::Form('SettingForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		if(Url::get('cmd')=='seo')
		{
			$languages = DB::select_all('language');
			foreach($languages as $key=>$value)
			{		
				Portal::set_setting('site_title_'.$key,Url::get('site_title_'.$key),false,'PORTAL');
				Portal::set_setting('website_keywords_'.$key,Url::get('website_keywords_'.$key),false,'PORTAL');
				Portal::set_setting('website_description_'.$key,Url::get('website_description_'.$key),false,'PORTAL');
			}
			Portal::set_setting('site_name',Url::get('site_name'),false,'PORTAL');
			Portal::set_setting('google_analytics',Url::get('google_analytics'),false,'PORTAL');
			Portal::set_setting('auto_link',Url::get('auto_link'),false,'PORTAL');
            Portal::set_setting('ads_text_link',Url::get('ads_text_link'),false,'PORTAL');
            Portal::set_setting('home_faq',Url::get('home_faq'),false,'PORTAL');
			if($_FILES)
			{
				foreach($_FILES as $key=>$value)
				{
				    if($key){
                        SettingDB::save_image($key);
                    }
				}
			}
			Url::js_redirect(true,'Bạn vừa cập nhật thành công',array('cmd'=>'seo'));
		}
	}
	function draw()
	{
		if(!can_tuha_administrator()){
			Url::access_denied();
		}
		$languages = DB::fetch_all('select id,name,icon_url,code from language where active=1');
		foreach($languages as $key=>$value)
		{
			$_REQUEST['website_keywords_'.$key] = Portal::get_setting('website_keywords_'.$key,'');
			$_REQUEST['website_description_'.$key] = Portal::get_setting('website_description_'.$key,'');
			$_REQUEST['site_title_'.$key] = Portal::get_setting('site_title_'.$key,'');
		}
		$this->map['google_analytics'] = Portal::get_setting('google_analytics','');
		$this->map['auto_link'] = Portal::get_setting('auto_link','');
		$_REQUEST['site_name'] = Portal::get_setting('site_name','');
		$_REQUEST['site_icon'] = Portal::get_setting('site_icon','');
        $_REQUEST['image_url'] = Portal::get_setting('image_url','');
        $_REQUEST['ads_text_link'] = Portal::get_setting('ads_text_link','');

        $_REQUEST['home_faq'] = Portal::get_setting('home_faq','');

		$this->map['languages'] = $languages;
		$this->map['prefix'] = PREFIX;
		$this->parse_layout('seo',$this->map);
	}
}
?>