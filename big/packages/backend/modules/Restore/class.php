<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY thedeath
******************************/
class Restore extends Module
{
	function Restore($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_admin(false,ANY_CATEGORY))
		{
			require_once 'forms/restore.php';
			$this->add_form(new RestoreForm());
		}
		else
		{
			Url::access_denied();
		}
	}
}
?>
