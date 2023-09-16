<?php 
class AdminRoles extends Module{
	function __construct($row){
		Module::Module($row);
		require_once('db.php');
		if(User::is_login() and Session::get('group_id')){
			switch(Url::get('cmd'))
			{
				case 'add':
					require_once 'forms/edit.php';
					$this->add_form(new EditAdminRolesForm());
					break;
				case 'edit':
					require_once 'forms/edit.php';
					$this->add_form(new EditAdminRolesForm());
					break;
				default:
					require_once 'forms/list.php';
					$this->add_form(new ListAdminRolesForm());
					break;
			}
		}else{
			URL::access_denied();
		}
	}

}
?>