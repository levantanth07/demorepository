<?php
class UtilsForm extends Form
{
	function UtilsForm ()
	{
		Form::Form('UtilsForm ');
		$this->link_js('packages/core/includes/js/jquery/ui.tabs.js');
		$this->link_css('assets/default/css/cms.css');
		$this->link_css('assets/default/css/jquery/tabs.css');
	}
	function on_submit()
	{
		{
			Url::redirect_current();
		}
	}
	function draw()
	{
		$golden  = XML::fetch_all('cache/utils/golden.xml');
		$weather = XML::fetch_all('cache/utils/weather.xml');
		$currency= XML::fetch_all('cache/utils/currency.xml');
		$this->parse_layout('list',array(
			'golden'=>$golden
			,'weather'=>$weather
			,'currency'=>$currency
		));
	}
}
?>