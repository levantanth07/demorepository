<?php
class InitialForm extends Form
{
	function InitialForm()
	{
		Form::Form('InitialForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
	}
	function draw()
	{
		$this->parse_layout('list');
	}
}
?>