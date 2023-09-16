<?php
// AUTHOR   : MINHTC
// DATE     : 27/10/2009
// FUNCTION : HIEN THI NOI DUNG CHI TIET TIN TUC
class Content extends Module
{
	function Content($row)
	{
		Module::Module($row);
		require_once 'db.php';
		require_once 'forms/list.php';
		$this->add_form(new ContentForm());
	}
}
?>