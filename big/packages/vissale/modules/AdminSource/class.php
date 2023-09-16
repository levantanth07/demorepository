<?php 
class AdminSource extends Module{
	function __construct($row){
		Module::Module($row);
		if(User::is_login() and Session::get('group_id')){
			require_once 'forms/edit.php';
			$this->add_form(new EditAdminSourceForm());
		}else{
			URL::access_denied();
		}
	}

}
?>