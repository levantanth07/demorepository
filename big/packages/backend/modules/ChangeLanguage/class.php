<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY TCV PORTAL
EDIT BY NGOCNV
******************************/
if(URL::check('language_id') and DB::exists_id('language',$_REQUEST['language_id']))
{
	Session::set('language_id', $_REQUEST['language_id']);
	Session::set('language_changed',1);
	if(User::is_login())
	{
		/*DB::update('user',array('language_id'=>$_REQUEST['language_id']),'id="'.Session::get('user_id').'"');*/
	}
	if(URL::get('href'))
	{
		URL::redirect_url(urldecode(Url::get('href')));
	}
	else
	{
		header('location:/');
		exit;
	}
}
else
{
	class ChangeLanguage extends Module
	{
		function ChangeLanguage($row)
		{
			Module::Module($row);
		require_once 'db.php';
		}
	}
}
?>
