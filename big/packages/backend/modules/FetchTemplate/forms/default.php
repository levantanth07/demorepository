<?php
class DefaultForm extends Form
{
	function DefaultForm()
	{
		Form::Form('DefaultForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
	}
	function draw()
	{
		$this->parse_layout('default');
	}
}
?>