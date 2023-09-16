<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY thedeath
******************************/
class ManageAdvertisment extends Module
{
	function ManageAdvertisment($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_view(false,ANY_CATEGORY))
		{
			switch(Url::get('cmd'))
			{
				case 'advertisment':
					require_once 'forms/advertisment.php';
					$this->add_form(new ManageAdvertismentForm());
					break;
				default:
					require_once 'forms/list.php';
					$this->add_form(new ListManageAdvertismentForm());
					break;
			}
		}
		else
		{
			URL::access_denied();
		}
	}
}
?>