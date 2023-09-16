<?php
class FaqList extends Module
{
	function FaqList($row)
	{
		Module::Module($row);
		require_once 'db.php';
		require_once 'forms/list.php';
		$this->add_form(new FaqListForm());
	}
}
?>