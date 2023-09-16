<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY thedeath
******************************/
class Backup extends Module
{
	function Backup($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_admin(false,ANY_CATEGORY))
		{
			require_once 'forms/backup.php';
			$this->add_form(new BackupForm());
		}
		else
		{
			Url::access_denied();
		}
	}
}
?>
