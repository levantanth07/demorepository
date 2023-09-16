<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY thedeath
******************************/
class DefinitionConfig extends Module
{
	function DefinitionConfig($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_admin(false,ANY_CATEGORY))
		{
			require_once 'forms/list.php';
			$this->add_form(new ListDefinitionConfigForm());
		}
		else
		{
			URL::access_denied();
		}
	}
}
?>