<?php
/*
	WRITTEN BY  :	THEDEATH
	Edit by 	:	hoatbv
	Date		:	30/07/2009
*/
class SiteMap extends Module
{
	function SiteMap($row)
	{
		Module::Module($row);
		require_once 'db.php';
		require_once 'forms/list.php';
		$this->add_form(new SiteMapForm());
	}
}
?>