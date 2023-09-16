<?php 
class AdminGroupsMaster extends Module{
	function __construct($row){
		Module::Module($row);
		if(User::is_admin()){
			require_once 'forms/edit.php';
			$this->add_form(new EditAdminGroupsMasterForm());
		}else{
			URL::access_denied();
		}
	}

}
?>