<?php 
class AdminRolesActivities extends Module{
	function __construct($row){
		Module::Module($row);
		if(User::is_login() and User::is_admin()){
			require_once 'forms/edit.php';
			$this->add_form(new EditAdminRolesActivitiesForm());
		}else{
			URL::access_denied();
		}
	}

}
?>