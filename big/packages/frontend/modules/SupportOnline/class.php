<?php
class SupportOnline extends Module
{
	function SupportOnline($row)
	{
		Module::Module($row);
		require_once 'db.php';
		require_once 'forms/list.php';
		$this->add_form(new SupportOnlineForm());
	}
}
?>
