<?php 
class AdminShippingServices extends Module{
	function __construct($row){
		Module::Module($row);
		if(User::is_login() and Session::get('group_id') and Session::get('admin_group')){
			require_once 'forms/edit.php';
			$this->add_form(new EditAdminShippingServicesForm());
		}else{
			URL::access_denied();
		}
	}

}
?>