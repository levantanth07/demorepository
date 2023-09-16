<?php
class ColumnLayoutForm extends Form
{
	function ColumnLayoutForm()
	{
		Form::Form('ColumnLayoutForm');
	}
	function draw()
	{
		$this->parse_layout('list');
	}
}
?>