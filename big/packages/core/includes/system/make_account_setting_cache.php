<?php
/******************************
COPY RIGHT BY Catbeloved - Framework
******************************/

function make_account_setting_cache($id)
{
	$user_settings = DB::fetch_all('	
		SELECT 
			setting_id as id,
			IF(setting.style = 2,"@VERY_LARGE_INFORMATION", value) as value 
		FROM 
			`setting`
			inner join `account_setting` on `setting_id`=`setting`.id
		WHERE
			account_setting.account_id="'.$id.'"
	');	
	$settings = array();
	foreach($user_settings as $user_setting)
	{
		$settings[$user_setting['id']]=$user_setting['value'];
	}
	$code = var_export($settings,true).';';
	DB::update('account',array('cache_setting'=>$code),'id="'.$id.'"');
	return $code;
}
?>