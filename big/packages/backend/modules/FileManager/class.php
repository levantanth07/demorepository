<?php 
class FileManager extends Module
{
	function FileManager($row)
	{
		if(User::can_edit(MODULE_NEWSADMIN,ANY_CATEGORY))
		{
			/*if(Url::get('cmd') == 'create_folder'){
				$name = Url::get('name');
				$path = Url::get('path');
				require 
				exit();
			}*/
			Module::Module($row);
			require_once 'db.php';
			require_once 'forms/view.php';
			$this->add_form(new FileManagerForm());
		}
		else
		{
			Url::access_denied();
		}	
	}
}
?>