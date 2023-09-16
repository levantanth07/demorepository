<?php 
class AdminRatingTemplate extends Module{
	function __construct($row){
		Module::Module($row);
        require_once 'packages/vissale/lib/php/vissale.php';
		if(check_user_privilege('ADMIN_KHO') or check_user_privilege('ADMIN_KETOAN') or check_user_privilege('ADMIN_MARKETING')){
			require_once 'forms/edit.php';
			$this->add_form(new EditAdminRatingTemplateForm());
		}else{
			URL::access_denied();
		}
	}

}
?>