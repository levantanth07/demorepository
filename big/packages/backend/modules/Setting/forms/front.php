<?php
class FrontEndForm extends Form
{
	function __construct()
	{
		Form::Form('FrontEndForm');
		$this->link_css('assets/default/css/cms.css');
		$this->link_css('assets/default/css/tabs/tabpane.css');
		$this->link_js('assets/default/css/tabs/tabpane.js');
	}
	function save_image($field)
	{
		require_once 'packages/core/includes/utils/upload_file.php';
		$dir = substr(PORTAL_ID,1).'/icon/';
		update_upload_file('config_'.$field,$dir);
		if(Url::get('config_'.$field)!='')
		{
			Portal::set_setting($field,Url::get('config_'.$field),false,'PORTAL');
		}
	}
	function on_submit()
	{
		if(Url::get('cmd') == 'front_end')
		{
			foreach($_REQUEST as $key=>$value)
			{
				if(preg_match('/config_(.*)/',$key,$matches))
				{
					Portal::set_setting($matches[1],$value,false,'PORTAL');
				}
			}
			if($_FILES)
			{
				foreach($_FILES as $key=>$value)
				{
					if(preg_match('/config_(.*)/',$key,$matches))
					{
						$this->save_image($matches[1]);
					}
				}
			}
			Session::delete('portal');
			Url::redirect_current(array('cmd'));
		}
	}
	function draw()
	{
		if(Portal::$current->settings)
		{
			foreach(Portal::$current->settings as $key=>$value)
			{
				if(is_string($value) and !isset($_REQUEST['config_'.$key]))
				{
					$_REQUEST['config_'.$key] = $value;
				}
			}
		}
		$this->parse_layout('front');
	}
}
?>