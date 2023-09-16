<?php 
class AdminStatus extends Module{
	function __construct($row){
		Module::Module($row);
		require_once 'AdminStatusDB.php';
		if(Session::get('group_id')
            and (
                Session::get('admin_group')
                or check_user_privilege('MARKETING')
                or check_user_privilege('ADMIN_MARKETING')
            )
        ){
			require_once 'forms/edit.php';
			$this->add_form(new EditAdminStatusForm());
		}else{
			URL::access_denied();
		}
	}

}
?>