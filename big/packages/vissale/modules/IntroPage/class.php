<?php
class IntroPage extends Module
{
    public static $group_id;
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
    function __construct($row)
    {
        Module::Module($row);
        self::$group_id = Session::get('group_id');
        self::$is_account_group_manager = is_account_group_manager();
        self::$quyen_bc_doanh_thu_nv = check_user_privilege('BC_DOANH_THU_NV');
        self::$quyen_bc_doanh_thu_mkt = check_user_privilege('BC_DOANH_THU_MKT');
        self::$quyen_marketing = check_user_privilege('MARKETING');
        self::$quyen_admin_marketing = check_user_privilege('ADMIN_MARKETING');
        self::$quyen_chia_don = check_user_privilege('CHIADON');
        switch (Url::get('do')) {
            default:
                require_once "forms/list.php";
                $this->add_form(new IntroPageForm());
                break;
        }
    }
}