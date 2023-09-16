<?php 
class CashFlowCategory extends Module{
    public static $group_id;
	function __construct($row){
	    self::$group_id = Session::get('group_id');
		Module::Module($row);
		if(User::is_login() and self::$group_id){
		    require_once  'db.php';
			require_once 'forms/edit.php';
			$this->add_form(new EditCashFlowCategoryForm());
		}else{
			URL::access_denied();
		}
	}

}
?>