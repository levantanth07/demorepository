<?php
class MenuHome extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'forms/list.php';
		require_once 'db.php';
		if(User::is_login())
		{
			if(!Url::get('category_id'))
			{
				$_REQUEST['category_id'] =5;
			}
			$this->add_form(new MenuHomeForm());
		}else
		{
			Url::redirect('sign_in');
		}
	}
}
?>
