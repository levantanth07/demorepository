<?php
/* 	WRITTEN BY ngocub
	DATE 16/12/2009
*/
class NewsHot extends Module
{
	function NewsHot($row)
	{
		Module::Module($row);
		require_once 'forms/list.php';
		require_once 'db.php';
		$this->add_form(new NewsHotForm());
	}
}
?>