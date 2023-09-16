<?php
class AccountLog extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(Session::get('admin_group') or User::is_admin())
		{
			require_once 'forms/list.php';
			$this->add_form(new ListAccountLogForm());
		}
		else
		{
			URL::access_denied();
		}
	}
}
?>