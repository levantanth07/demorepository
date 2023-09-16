<?php 
class AdminFbPost extends Module{
	function __construct($row){
		Module::Module($row);
		if(check_user_privilege('ADMIN_MARKETING')){
			require_once 'forms/edit.php';
			$this->add_form(new EditAdminFbPostForm());
		}else{
			URL::access_denied();
		}
	}
}
?>