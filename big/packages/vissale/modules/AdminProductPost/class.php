<?php 
class AdminProductPost extends Module{
	function __construct($row){
		Module::Module($row);
		mysql_query("SET charset 'utf8';");
		mysql_query("SET names 'utf8';");
		if(User::is_login() and Session::get('group_id') and Session::get('admin_group')){
			require_once 'db.php';
			require_once 'forms/edit.php';
			$this->add_form(new EditAdminProductPostForm());
		}else{
			URL::access_denied();
		}
	}

}
?>