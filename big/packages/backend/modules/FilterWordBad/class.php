<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY thedeath
******************************/
class FilterWordBad extends Module
{
	function FilterWordBad($row)
	{
		if(User::can_admin(MODULE_FILTERWORDBAD,ANY_CATEGORY))
		{
			Module::Module($row);
			require_once 'db.php';
			require_once 'forms/list.php';
			$this->add_form(new FilterWordBadForm());
		}
		else
		{
			Url::access_denied();
		}
	}
}
?>