<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY thedeath
******************************/
class Utils extends Module
{
	function Utils($row)
	{
		Module::Module($row);
		require_once 'packages/core/includes/utils/xml.php';
		if(Url::get('cmd')=='auto_update')
		{
			$this->update_weather();
			$this->update_golden();
			$this->update_currency();
		}
		if(User::can_admin(false,ANY_CATEGORY))
		{
			switch(Url::get('cmd'))
			{
				case 'update':
					$this->update();
					break;
				default:
					require_once 'forms/list.php';
					$this->add_form(new UtilsForm());
					break;
			}
		}
		else
		{
			Url::access_denied();
		}
	}
	function update_weather()
	{
		$url = 'http://www.nchmf.gov.vn/website/vi-VN/81/Default.aspx';
		$content = @file_get_contents($url);
		$province = '/\<td width=\"20%\" align=left class=\"thoitiet_hientai rightline\"\>([^\<]+)\<\/td\>/';
		$weather = '/\<img src=\"http\:\/\/www\.nchmf\.gov\.vn\/Upload\/WeatherSymbol\/([^\"]+)\" border=0\>/';
		$temperature = '/class=\"thoitiet_hientai rightline\"\>\<strong\>([0-9]+)/';
		//Duc them
		$doam = '/\<td width=\"15%\" align=\"center\" class=\"thoitiet_hientai rightline\"\>([^<]+)<\/td\>/';
		$extra1 = '/\<td width=\"30\%\" align=\"left\" ([^>]+)\>([^<]+)\<\/td\>/';
		$extra2 = '/\<span class=\"thoitiet_hientai\"  style=\"Display:Yes\"\>([^<]+)\<\/span\>/';
		//$humidity = '/class=\"thoitiet_hientai rightline\"\>\<strong\>([0-9]+)/'; // do am
		if(preg_match_all($province,$content,$matches))
		{
			$name_province = $matches[1];
		}
		if(preg_match_all($weather,$content,$matches))
		{
			$images= $matches[1];
		}
		if(preg_match_all($temperature,$content,$matches))
		{
			$temperatures = $matches[1];
		}
		if(preg_match_all($extra1,$content,$matches))
		{
			$extra1 = $matches[2];
		}
		if(preg_match_all($extra2,$content,$matches))
		{
			$extra2 = $matches[1];
		}
		if(preg_match_all($doam,$content,$matches))
		{
			$doams = $matches[1];
		}
		if(isset($name_province) and isset($temperatures) and isset($images))
		{
			$items = array();
			foreach($images as $key=>$value)
			{
				$sou = 'http://www.nchmf.gov.vn/Upload/WeatherSymbol/'.$value;
				$des = 'upload/default/icon/'.substr($sou,strrpos($sou,'/')+1);
				if(!file_exists($des))
				{
					@copy($sou,$des);
				}
				$items[$key+1]['id'] = $key+1;
				$items[$key+1]['images'] = 'http://'.$_SERVER['SERVER_NAME'].'/'.$des;
				$items[$key+1]['province'] = $name_province[$key];
				$items[$key+1]['temperature'] = $temperatures[$key];
				$items[$key+1]['extra1'] = isset($extra1[$key])?$extra1[$key]:'';
				$items[$key+1]['extra2'] = isset($extra2[$key])?$extra2[$key]:'';
				$items[$key+1]['doam'] = isset($doams[$key])?$doams[$key]:'';
			}
			XML::create_xml('cache/utils/weather',$items);
		}
	}
	function update_golden()
	{
		$url = 'http://www3.tuoitre.com.vn/transweb/giavang.htm';
		$items = array();
		$content = @file_get_contents($url);
		$pattern_name = '/\<td name=\"Table1_1_1\" class=\"cssMainTD\"\>([^\<]+)\<\/td\>/';
		$pattern_buy  = '/\<td name=\"Table1_1_2\" class=\"cssTD\"\>([^\<]+)\<\/td\>/';
		$pattern_sell = '/\<td name=\"Table1_1_3\" class=\"cssTD\"\>([^\<]+)\<\/td\>/';
		if(preg_match_all($pattern_name,$content,$matches))
		{
			$names= $matches[1];
		}
		if(preg_match_all($pattern_buy,$content,$matches))
		{
			$buy= $matches[1];
		}
		if(preg_match_all($pattern_sell,$content,$matches))
		{
			$sell= $matches[1];
		}
		if(isset($names) and isset($buy) and isset($sell))
		{
			foreach($names as $key=>$value)
			{
				$items[$key+1]['id'] = $key+1;
				$items[$key+1]['name'] = $value;
				$items[$key+1]['sell'] = $sell[$key];
				$items[$key+1]['buy'] = $buy[$key];
			}
		}
		XML::create_xml('cache/utils/golden',$items);
	}
	function update_currency()
	{
		require_once 'cache/tables/currency.cache.php';
		XML::create_xml('cache/utils/currency',$currency);
	}
	function update()
	{
		$this->update_weather();
		$this->update_golden();
		$this->update_currency();
		Url::redirect_current();
	}
}
?>