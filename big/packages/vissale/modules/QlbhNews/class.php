<?php
class QlbhNews extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		if(User::is_login()){
			require_once 'db.php';
			require_once 'forms/list.php';
			$this->add_form(new QlbhNewsForm());
		}
	}
}
?>