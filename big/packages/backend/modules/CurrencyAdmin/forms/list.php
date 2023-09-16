<?php
class CurrencyAdminForm extends Form
{
	function CurrencyAdminForm()
	{
		Form::Form('CurrencyAdminForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
	}
	function draw()
	{
		$items = CurrencyAdminDB::get_items();
		$this->parse_layout('list',array(
			'items'=>$items
		));
	}
}
?>