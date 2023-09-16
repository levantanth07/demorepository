<?php
class HtmlForm extends Form
{
	function __construct()
	{
		Form::Form('HtmlForm');
	}
	function draw()
	{
		$this->map['html_code'] = Module::get_setting('html_code');
		$this->parse_layout('html',$this->map);
	}
}
?>
