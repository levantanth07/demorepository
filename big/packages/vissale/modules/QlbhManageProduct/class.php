<?php 
class QlbhManageProduct extends Module
{
	function __construct($row)
	{
		Module::Module($row);
        if(check_user_privilege('ADMIN_KHO'))
		{
			
			require_once 'forms/edit.php';
			$this->add_form(new EditQlbhManageProductForm());
		}
		else
		{
			URL::access_denied();
		}
	}
}
?>