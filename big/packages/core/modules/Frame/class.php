<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY VUONGIGALONG
******************************/
class Frame extends Module
{
	function Frame($row)
	{
		Module::Module($row);
		require_once 'forms/list.php';
		$this->add_form(new FrameForm());
	}
}
?>