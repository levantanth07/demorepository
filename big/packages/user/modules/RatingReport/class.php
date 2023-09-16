<?php
class RatingReport extends Module{
    public static $group_id;
    public static $master_group_id;
    public static $user_id;
    public static $account_id;
    public static $admin_group;
    public static $quyen_xuat_kho;
    public static $quyen_marketing;
    public static $quyen_admin_marketing;
    public static $quyen_bung_don;
    public static $quyen_cskh;
    public static $quyen_xuat_excel;
    public static $quyen_ke_toan;
    public static $quyen_admin_ke_toan;
    public static $quyen_bc_doanh_thu_nv;
    public static $is_account_group_manager;
    public static $quyen_bc_doanh_thu_mkt;
    public static $quyen_chia_don;
    public static $type;
    public static $account_type;
    public static $account_privilege_code;
    public static $mkt_cost_per_revenue_danger;
    public static $mkt_cost_per_revenue_warning;
    public static $date_init_value;
	public static $item = array();
	function __construct($row){
		Module::Module($row);
        if(System::is_local()){
            self::$date_init_value = 'NULL';//'0000-00-00 00:00:00';
        }else{
            self::$date_init_value = '0000-00-00 00:00:00';
        }
        require_once 'db.php';
        require_once 'packages/vissale/lib/php/vissale.php';
        RatingReport::$group_id = Session::get('group_id');
        RatingReport::$account_id = Session::get('user_id');
        RatingReport::$master_group_id = Session::get('master_group_id');
        RatingReport::$user_id = get_user_id();
        RatingReport::$admin_group = Session::get('admin_group');
        RatingReport::$account_type = Session::get('account_type');
        RatingReport::$is_account_group_manager = is_account_group_manager();
        RatingReport::$quyen_bc_doanh_thu_nv = check_user_privilege('BC_DOANH_THU_NV');
        RatingReport::$quyen_bc_doanh_thu_mkt = check_user_privilege('BC_DOANH_THU_MKT');
        RatingReport::$quyen_marketing = check_user_privilege('MARKETING');
        RatingReport::$quyen_admin_marketing = check_user_privilege('ADMIN_MARKETING');
        RatingReport::$quyen_chia_don = check_user_privilege('CHIADON');
        RatingReport::$quyen_cskh = check_user_privilege('CSKH');
        RatingReport::$account_privilege_code = '';
		require_once 'db.php';
		require_once 'forms/list.php';
		$this->add_form(new ReportForm());
	}
}
?>