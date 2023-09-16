<?php
class FbSettingForm extends Form
{
	function FbSettingForm()
	{
		Form::Form('FbSettingForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		if(Url::get('cmd')=='seo')
		{
			$languages = DB::select_all('language');
			foreach($languages as $key=>$value)
			{		
				$key = DB::escape($key);
				$value  = DB::escape($value);
				Portal::set_setting('site_title_'.$key,Url::get('site_title_'.$key),false,'PORTAL');
				Portal::set_setting('website_keywords_'.$key,Url::get('website_keywords_'.$key),false,'PORTAL');
				Portal::set_setting('website_description_'.$key,Url::get('website_description_'.$key),false,'PORTAL');
			}
			Portal::set_setting('site_name',DB::escape(Url::get('site_name')),false,'PORTAL');
			Portal::set_setting('google_analytics',DB::escape(Url::get('google_analytics')),false,'PORTAL');
			Portal::set_setting('auto_link',DB::escape(Url::get('auto_link')),false,'PORTAL');
			if($_FILES)
			{
				foreach($_FILES as $key=>$value)
				{
					FbSettingDB::save_image($key);
				}
			}
			Session::delete('portal');
			Url::redirect_current(array('cmd'=>'seo'));
		}
	}
	function draw()
	{
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
		$this->map['languages'] = $languages;
		$this->map['prefix'] = PREFIX;
		$this->parse_layout('seo',$this->map);
	}
}
?>