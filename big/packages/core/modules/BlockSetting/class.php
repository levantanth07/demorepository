<?php
class BlockSetting extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_view(false,ANY_CATEGORY))
		{
			require_once 'forms/list.php';
			$this->add_form(new ListBlockSettingForm());
		}
		else
		{
			URL::access_denied();
		}
	}
}
?>