<?php 
class AdminRatingQuestionTemplate extends Module{
	function __construct($row){
		Module::Module($row);
        require_once 'packages/vissale/lib/php/vissale.php';
		if(check_user_privilege('ADMIN_CS')){
			require_once 'forms/edit.php';
			$this->add_form(new EditAdminRatingQuestionTemplateForm());
		}else{
			Url::js_redirect('admin_orders','Chỉ có quản lý shop mới có quyền truy cập tính năng này.',['cmd'=>'care_list']);
		}
	}

}
?>