<?php
class Html extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		require_once 'forms/html.php';
		$this->add_form(new HtmlForm());
	}
}
?>
