<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY VUONGGIALONG
******************************/
class ColumnLayout extends Module
{
	function ColumnLayout($row)
	{
		Module::Module($row);
		require_once 'forms/list.php';
		$this->add_form(new ColumnLayoutForm());
	}
}
?>