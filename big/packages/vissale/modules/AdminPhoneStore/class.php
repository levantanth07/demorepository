<?php 
class AdminPhoneStore extends Module{
	function __construct($row){
		Module::Module($row);
		if(User::is_login() and Session::get('admin_group')){
			require_once 'forms/edit.php';
			$this->add_form(new EditAdminPhoneStoreForm());
		}else{
			URL::access_denied();
		}
	}

}
?>