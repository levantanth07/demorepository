<?php 
class AdminBundles extends Module{
	function __construct($row){
		Module::Module($row);
		if(check_user_privilege('ADMIN_KHO') or check_user_privilege('ADMIN_KETOAN') or check_user_privilege('ADMIN_MARKETING')){
			require_once 'forms/edit.php';
			$this->add_form(new EditAdminBundlesForm());
		}else{
			URL::access_denied();
		}
	}

}
?>