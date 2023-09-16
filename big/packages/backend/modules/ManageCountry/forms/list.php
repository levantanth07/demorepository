<?php
class ManageCountryForm extends Form
{
	function ManageCountryForm()
	{
		Form::Form('ManageCountryForm');
	}
	function on_submit()
	{
		
	}
	function draw()
	{
		$items = ManageCountryDB::get_items();
		$this->parse_layout('list',array(
			'items'=>$items
		));
	}
}
?>