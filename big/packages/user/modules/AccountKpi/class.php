<?php 
class AccountKpi extends Module{
    public static $group_id;
    public static $user_id;
    public static $account_id;
	function __construct($row){
        self::$group_id = Session::get('group_id');
        self::$account_id = Session::get('user_id');
        self::$user_id = get_user_id();
		Module::Module($row);
		if(check_user_privilege('MARKETING') or check_user_privilege('ADMIN_MARKETING')){
			require_once 'forms/edit.php';
			$this->add_form(new EditAccountKpiForm());
		}else{
			URL::access_denied();
		}
	}
}
?>