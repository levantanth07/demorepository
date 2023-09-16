<?php
class View extends Module
{
	function View($row)
	{
		Module::Module($row);
		require_once 'db.php';
		require_once 'forms/list.php';
		$this->add_form(new ViewForm());
	}
}
?>
