<?php 
class QlbhManageSupplier extends Module
{
	function __construct($row)
	{
		Module::Module($row);
        if(check_user_privilege('ADMIN_KHO')){
			require_once 'forms/edit.php';
			$this->add_form(new EditQlbhManageSupplierForm());
		}
		else
		{
			Url::js_redirect('/','Bạn không có quyền truy cập tính năng này!');
		}
	}
}
?>