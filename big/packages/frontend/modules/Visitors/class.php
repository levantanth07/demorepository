<?php
/*	create by : ngocnv
	date : 10/08/2009
	Function : Hien thi Luot truy cap
*/
class Visitors extends Module
{
	function Visitors($row)
	{
		Module::Module($row);
		require_once 'db.php';
		require_once 'forms/list.php';
		$this->add_form(new VisitorsForm());
	}
}
?>