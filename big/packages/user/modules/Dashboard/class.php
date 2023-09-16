<?php
class Dashboard extends Module
{
    public static $group_id;
    public static $master_group_id;
    public static $user_id;
    public static $account_id;
    public static $admin_group;
    public static $quyen_xuat_kho;
    public static $quyen_marketing;
    public static $quyen_sale;
    public static $quyen_admin_marketing;
    public static $quyen_bung_don;
    public static $quyen_cskh;
    public static $quyen_xuat_excel;
    public static $quyen_ke_toan;
    public static $quyen_van_don;
    public static $quyen_admin_ke_toan;
    public static $quyen_bc_doanh_thu_nv;
    public static $is_account_group_manager;
    public static $is_account_group_department;
    public static $quyen_bc_doanh_thu_mkt;
    public static $quyen_chia_don;
    public static $type;
    public static $account_type;
    public static $account_privilege_code;
    public static $mkt_cost_per_revenue_danger;
    public static $mkt_cost_per_revenue_warning;
    public static $date_init_value;
    public static $date_type;
    public static $quyen_xem_bc_doi_nhom;
    public static $bdh;

    public static $xem_khoi_bc_marketing;
    public static $xem_khoi_bc_sale;
    public static $xem_khoi_bc_chung;
    public static $xem_khoi_bc_truc_page;
    public function __construct($row)
    {
        if (User::is_login()) {
            Module::Module($row);
            if (System::is_local()) {
                self::$date_init_value = 'NULL'; //'0000-00-00 00:00:00';
            } else {
                self::$date_init_value = '0000-00-00 00:00:00';
            }
            require_once 'db.php';
            require_once 'packages/vissale/lib/php/vissale.php';
            Dashboard::$group_id                    = Session::get('group_id');
            Dashboard::$account_id                  = Session::get('user_id');
            Dashboard::$master_group_id             = Session::get('master_group_id');
            Dashboard::$user_id                     = get_user_id();
            Dashboard::$admin_group                 = Session::get('admin_group');
            Dashboard::$account_type                = Session::get('account_type');
            Dashboard::$is_account_group_manager    = is_account_group_manager();
            Dashboard::$is_account_group_department = is_account_group_department();
            Dashboard::$quyen_bc_doanh_thu_nv       = check_user_privilege('BC_DOANH_THU_NV');
            Dashboard::$quyen_bc_doanh_thu_mkt      = check_user_privilege('BC_DOANH_THU_MKT');
            Dashboard::$quyen_marketing             = check_user_privilege('MARKETING');
            Dashboard::$quyen_sale                  = check_user_privilege('GANDON');
            Dashboard::$quyen_admin_marketing       = check_user_privilege('ADMIN_MARKETING');
            Dashboard::$quyen_chia_don              = check_user_privilege('CHIADON');
            Dashboard::$quyen_cskh                  = check_user_privilege('CSKH');
            Dashboard::$quyen_xem_bc_doi_nhom       = check_user_privilege('QUYEN_XEM_BC_DOI_NHOM');
            Dashboard::$account_privilege_code      = '';
            Dashboard::$quyen_ke_toan               = check_user_privilege('KE_TOAN');
            Dashboard::$quyen_van_don               = check_user_privilege('VAN_DON');

            Dashboard::$xem_khoi_bc_marketing       = check_user_privilege('NHOM_BC_MKT');
            Dashboard::$xem_khoi_bc_sale            = check_user_privilege('NHOM_BC_SALE');
            Dashboard::$xem_khoi_bc_chung           = check_user_privilege('NHOM_BC_CHUNG');
            Dashboard::$xem_khoi_bc_truc_page       = check_user_privilege('NHOM_BC_TRUC_PAGE_TINH_TRANG_DON');
            self::$type = [
                ''=>'Tất cả số',
                1=>'SALE (Số mới)',
                2=>'CSKH',
                9=>'Tối ưu',
                3=>'Đặt lại',
                4=>'Đặt lại lần 1',
                5=>'Đặt lại lần 2',
                6=>'Đặt lại lần 3',
                7=>'Đặt lại lần 4',
                8=>'Đặt lại lần 5',
                10=>'Đặt lại lần 6',
                11=>'Đặt lại lần 7',
                12=>'Đặt lại lần 8',
                13=>'Đặt lại lần 9',
                14=>'Đặt lại lần 10',
                15=>'Đặt lại lần 11',
                16=>'Đặt lại lần 12',
                17=>'Đặt lại lần 13',
                18=>'Đặt lại lần 14',
                19=>'Đặt lại lần 15',
                20=>'Đặt lại lần 16',
                21=>'Đặt lại lần 17',
                22=>'Đặt lại lần 18',
                23=>'Đặt lại lần 19',
                24=>'Đặt lại lần 20',
                25=>'Đặt lại trên lần 20',
                26 => 'Cross sale',
                27 => 'Afiliate',
            ];
            Dashboard::$date_type = [
                '' => 'Ngày xác nhận',
                1  => 'Ngày trạng thái',
            ];
            self::$mkt_cost_per_revenue_danger  = get_group_options('mkt_cost_per_revenue_danger');
            self::$mkt_cost_per_revenue_warning = get_group_options('mkt_cost_per_revenue_warning');
            self::$bdh                          = false;
            if ('PAL.khoand' == self::$account_id or 'ongtrum' == self::$account_id or 'Sale0001' == self::$account_id or 'phamvanquang' == self::$account_id or 'obd.quangpv' == self::$account_id) {
                self::$bdh = true;
            }
            switch (Url::get('do')) {
                case 'get_user_rank':
                    if ($date_from = Url::get('date_from') and $date_to = Url::get('date_to')) {
                        $code       = Url::get('code');
                        $start_time = date('Y-m-d', Date_Time::to_time($date_from));
                        $end_time   = date('Y-m-d', Date_Time::to_time($date_to));
                        $users      = DashboardDB::get_user_rank($start_time, $end_time, $code);
                        $str        = '<table class="table">
                                        <tr>
                                            <th width="1%">#</th>
                                            <th width="34%">Họ và tên</th>
                                            <th width="20%" class="text-right">Điểm</th>
                                            <th width="20%" class="text-right">% chốt</th>
                                            <th width="25%">Đội nhóm</th>
                                        </tr>
                                ';
                        $i = 0;
                        foreach ($users as $k => $v) {
                            $i++;
                            $ty_le_chot = ($v['qty'] > 0) ? (round($v['total'] / $v['qty'], 2) * 100) : 0;
                            $str .= '
                                <tr class="row-' . $i . '">
                                    <td width="1%">' . (($i > 1) ? $i : '<i class="fa fa-trophy"></i>') . '</td>
                                    <td ><img src="' . $v['avatar'] . '" width="30" alt="" onerror="this.src=\'assets/standard/images/no_avatar.webp\';"> ' . $v['name'] . '</td>
                                    <td class="text-right"><span class="money">' . System::display_number($v['total']) . '</span></td>
                                    <td class="text-right"><span class="money">' . $v['ty_le_chot'] . '%</span></td>
                                    <td class="small">' . $v['account_group_name'] . '</td>
                                </tr>
                            ';
                        }
                        $str .= '</table>';
                        echo $str;
                    } else {
                        echo 'Bạn vui lòng chọn khoảng thời gian để xem báo cáo!';
                    }
                    exit();
                    break;
                case 'get_users_by_rank':
                    return $this->get_users_by_rank();
                case 'kho_so':
                    require_once 'forms/kho_so.php';
                    $this->add_form(new ReportForm());
                    break;
                case 'kho_so_sale':
                    require_once 'forms/kho_so_sale.php';
                    $this->add_form(new ReportForm());
                    break;
                case 'kho_so_mkt':
                    if(Dashboard::$quyen_marketing or Dashboard::$quyen_admin_marketing or Dashboard::$quyen_bc_doanh_thu_mkt or Dashboard::$xem_khoi_bc_marketing){
                        require_once 'forms/kho_so_mkt.php';
                        $this->add_form(new ReportForm());
                        break;
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('report') . '";</script>';
                        die;
                    }  
                case 'product_rating':
                    require_once 'forms/product_rating.php';
                    $this->add_form(new ReportForm());
                    break;
                case 'cskh':
                    if(Dashboard::$xem_khoi_bc_sale or Dashboard::$quyen_bc_doanh_thu_nv){
                        require_once 'forms/cskh.php';
                        $this->add_form(new CSKHForm());
                        break; 
                    }
                    return Url::js_redirect(true, 'Bạn không có quyền truy cập!');
                case 'order_status':
                    if (Dashboard::$admin_group) {
                        require_once 'forms/order_status.php';
                        $this->add_form(new ReportForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('report') . '";</script>';
                        die;
                    }
                    break;
                case 'not_action':
                    if (Dashboard::$quyen_chia_don or Dashboard::$xem_khoi_bc_truc_page) {
                        require_once 'forms/not_action_orders.php';
                        $this->add_form(new ReportForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('report') . '";</script>';
                        die;
                    }
                    break;
                case 'order_product':
                    if (Session::get('admin_group') or Dashboard::$xem_khoi_bc_chung) {
                        require_once 'forms/order_product.php';
                        $this->add_form(new OrderProductForm());
                        break;
                    }

                    return Url::js_redirect(true, 'Bạn không có quyền truy cập!');
                    
                case 'transport':
                    if (Session::get('admin_group') or Dashboard::$xem_khoi_bc_chung) {
                        require_once 'forms/transport.php';
                        $this->add_form(new TransportForm());
                        break;
                    }

                    return Url::js_redirect(true, 'Bạn không có quyền truy cập!');
                    
                case 'ty_le_chot_don':
                    if(Dashboard::$xem_khoi_bc_sale or Dashboard::$quyen_bc_doanh_thu_nv){
                        require_once 'forms/confirmed_rate_report.php';
                        $this->add_form(new ConfirmedRateReportForm());
                        break;
                    }
                    return Url::js_redirect(true, 'Bạn không có quyền truy cập!');

                case 'load_company_chart':
                case 'system_dashboard':
                    if (is_system_user() || check_system_user_permission('xembaocao')) {
                        require_once 'forms/system_dashboard.php';
                        $this->add_form(new ReportCharForm());
                    } else {
                        Url::js_redirect(true, 'Bạn không có quyền truy cập!');
                    }
                    break;
                case 'system_revenue':
                    if (Privilege::xemBcDoanhThuHeThong()) {
                        require_once 'forms/system_revenue.php';
                        $this->add_form(new SystemRevenueForm());
                    } else {
                        Url::js_redirect(true, 'Bạn không có quyền truy cập!');
                    }
                    break;

                case 'rank':
                    require_once 'forms/rank.php';
                    $this->add_form(new ReportForm());
                    break;
                case 'chart':
                    require_once 'forms/chart.php';
                    $this->add_form(new ReportCharForm());
                    break;
                case 'adv_money':
                    if(Dashboard::$quyen_admin_marketing or Dashboard::$quyen_marketing or Dashboard::$xem_khoi_bc_marketing){
                        require_once 'forms/adv_money.php';
                        $this->add_form(new AdvDashboardForm());
                        break;
                    } else {
                        Url::js_redirect(true, 'Bạn không có quyền truy cập!');
                    }
                    
                case 'adv_money_new':
                    require_once 'forms/adv_money_new.php';
                    $this->add_form(new AdvNewDashboardForm());
                    break;
                case 'adv_money_day':
                    require_once 'forms/adv_money_day.php';
                    $this->add_form(new AdvDayDashboardForm());
                    break;
                case 'hitcount':
                    require_once 'forms/hitcount.php';
                    $this->add_form(new HitcountForm());
                    break;
                case 'user':
                    require_once 'forms/user.php';
                    $this->add_form(new UserDashboardForm());
                    break;
                case 'by_date':
                    if ((User::is_login() and Session::get('admin_group')) or Dashboard::$xem_khoi_bc_chung) {
                        require_once 'forms/report_by_date.php';
                        $this->add_form(new ReportByDateForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('report') . '";</script>';
                        die;
                    }
                    break;
                case 'doanh_thu_mkt_old':
                    if (User::is_login() and (Dashboard::$quyen_marketing or Dashboard::$quyen_admin_marketing or Dashboard::$quyen_bc_doanh_thu_mkt)) {
                        require_once 'forms/doanh_thu_mkt_old.php';
                        $this->add_form(new ReportForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('report') . '";</script>';
                        die;
                    }
                    break;
                case 'doanh_thu_mkt':
                    if (User::is_login() and (Dashboard::$quyen_marketing or Dashboard::$quyen_admin_marketing or Dashboard::$quyen_bc_doanh_thu_mkt or Dashboard::$xem_khoi_bc_marketing)) {
                        require_once 'forms/doanh_thu_mkt.php';
                        $this->add_form(new ReportForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('report') . '";</script>';
                        die;
                    }
                    break;
                case 'doanh_thu_upsale':
                    if (User::is_login() and (Dashboard::$quyen_marketing or Dashboard::$quyen_admin_marketing or Dashboard::$quyen_bc_doanh_thu_mkt or Dashboard::$xem_khoi_bc_marketing)) {
                        require_once 'forms/doanh_thu_upsale.php';
                        $this->add_form(new ReportForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('report') . '";</script>';
                        die;
                    }
                    break;
                case 'mkt_by_source':
                    if (User::is_login() and (Dashboard::$quyen_admin_marketing or Dashboard::$quyen_bc_doanh_thu_mkt or Dashboard::$xem_khoi_bc_marketing)) {
                        require_once 'forms/mkt_by_source.php';
                        $this->add_form(new ReportForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('report') . '";</script>';
                        die;
                    }
                    break;
                case 'doanh_thu_tong':
                    if (User::is_login() and Session::get('account_type') == 3) {
                        require_once 'forms/doanh_thu_tong.php';
                        $this->add_form(new ReportForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('report') . '";</script>';
                        die;
                    }
                    break;
                case 'doanh_thu_nv':
                    if (Dashboard::$quyen_xem_bc_doi_nhom or Dashboard::$is_account_group_manager or Dashboard::$quyen_bc_doanh_thu_nv or Dashboard::$is_account_group_department or Dashboard::$quyen_cskh or Dashboard::$xem_khoi_bc_sale) {
                        require_once 'forms/doanh_thu_nv.php';
                        $this->add_form(new ReportForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('admin_orders') . '";</script>';
                        die;
                    }
                    break;
                case 'ceo':
                    if (Dashboard::$admin_group) {
                        require_once 'forms/ceo.php';
                        $this->add_form(new ReportForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('admin_orders') . '";</script>';
                        die;
                    }
                    break;
                case 'tong_hop_sale':
                    if (User::is_login() and (Dashboard::$quyen_bc_doanh_thu_nv or Dashboard::$quyen_cskh or Dashboard::$xem_khoi_bc_sale)) {
                        require_once 'forms/tong_hop_sale.php';
                        $this->add_form(new ReportForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('report') . '";</script>';
                        die;
                    }
                    break;
                case 'tong_hop_mkt':
                    if (User::is_login() and (Dashboard::$quyen_bc_doanh_thu_mkt)) {
                        require_once 'forms/tong_hop_mkt.php';
                        $this->add_form(new ReportForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="";</script>';
                        die;
                    }
                    break;
                case 'order_action':
                    if (User::is_login() and (Session::get('admin_group') or Dashboard::$quyen_chia_don or Dashboard::$xem_khoi_bc_truc_page)) {
                        require_once 'forms/order_action.php';
                        $this->add_form(new ReportForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('admin_orders') . '";</script>';
                        die;
                    }
                    break;
                case 'report':
                    if (User::is_login() and (Session::get('admin_group') or Dashboard::$quyen_cskh or Dashboard::$quyen_bc_doanh_thu_nv or Dashboard::$xem_khoi_bc_sale)) {
                        require_once 'forms/report.php';
                        $this->add_form(new ReportForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('report') . '";</script>';
                        die;
                    }
                    break;
                case 'post':
                    if ((User::is_login() and check_user_privilege('ADMIN_MARKETING')) or Dashboard::$xem_khoi_bc_marketing) {
                        require_once 'forms/post.php';
                        $this->add_form(new PostForm());
                    } else {
                        echo '<script>alert("Bạn không có quyền truy cập tính năng này. Hệ thống sẽ quay lại trang danh sách đơn hàng.");window.location="' . Url::build('report') . '";</script>';
                        die;
                    }
                    break;

                case 'cost':
                    if (is_system_user()|| check_system_user_permission('xembaocao')) {
                        require_once 'forms/cost.php';
                        return $this->add_form(new CostForm());
                    }
                    
                    return Url::js_redirect(true, 'Bạn không có quyền truy cập!');

                case 'cost_hkd':
                    if (User::is_login() and (Session::get('admin_group') || check_user_privilege('ADMIN_KETOAN') || check_user_privilege('KE_TOAN') || is_group_owner())) {
                        require_once 'forms/cost_hkd.php';
                        return $this->add_form(new CostHkdForm());
                    }
                    return Url::js_redirect(true, 'Bạn không có quyền truy cập!');

                case 'cost_mkt':
                    if (is_system_user() && check_system_user_permission('xembaocao')) {
                        require_once 'forms/cost_mkt.php';
                        return $this->add_form(new CostMktForm());
                    }
                    return Url::js_redirect(true, 'Bạn không có quyền truy cập!');

                case 'vaccination':
                case 'vaccination_chart':
                    if (is_system_user() && check_system_user_permission('xembaocao')) {
                        require_once 'forms/vaccination.php';
                        return $this->add_form(new VaccinationForm());
                    }

                    return Url::js_redirect(true, 'Bạn không có quyền truy cập!');

                case 'pie':
                    if (Session::get('admin_group') or Dashboard::$xem_khoi_bc_chung) {
                        require_once 'forms/list.php';
                        $this->add_form(new PieForm());
                        break;
                    }

                    return Url::js_redirect(true, 'Bạn không có quyền truy cập!');
                    
                case 'zone':
                    if (Session::get('admin_group') or Dashboard::$xem_khoi_bc_chung) {
                        require_once 'forms/zone.php';
                        $this->add_form(new ZoneForm());
                        break;
                    }

                    return Url::js_redirect(true, 'Bạn không có quyền truy cập!');
                    
                case 'report_day':
                    require_once 'forms/report_day.php';
                    $this->add_form(new ReportDayForm());
                    break;
                case 'report_month':
                    require_once 'forms/report_month.php';
                    $this->add_form(new ReportMonthForm());
                    break;
                case 'get_data_report_month':
                    require_once 'forms/view_report_month.php';
                    $this->add_form(new ViewReportMonthForm());
                    break;
                case 'get_data_report_day':
                    require_once 'forms/view_report_day.php';
                    $this->add_form(new ViewReportDayForm());
                    break;
                case 'reason_fail':
                    if(Dashboard::$xem_khoi_bc_sale or Dashboard::$quyen_van_don or check_user_privilege('GANDON') or Dashboard::$quyen_ke_toan or Dashboard::$is_account_group_manager){
                        require_once 'forms/reason_fail.php';
                        $this->add_form(new ReasonFailForm());
                        break;
                    }
                    return Url::js_redirect(true, 'Bạn không có quyền truy cập!');
                default:
                    require_once 'forms/chart.php';
                    $this->add_form(new ReportCharForm());
                    break;
            }
        }
    }

    /**
     * Gets the users by rank.
     */
    private function get_users_by_rank()
    {   
        $date_to = Url::get('date_to');
        $date_from = Url::get('date_from');
        $system_group_id = Url::iget('system_group_id');
        $code       = Url::get('code');
        $group_ids       = DB::escapeArray(Url::get('group_ids'));
        
        $start_time = date('Y-m-d', Date_Time::to_time($date_from));
        $end_time   = date('Y-m-d', Date_Time::to_time($date_to));

        if (!$date_from || !$date_to) {
            die('Bạn vui lòng chọn khoảng thời gian để xem báo cáo!');
        }

        $users      = DashboardDB::get_user_statistic($system_group_id, $start_time, $end_time, 
            $code, Url::get('view_type'), $group_ids);
        
        $str        = '<table class="table">
                        <thead>
                            <tr>
                                <th width="1%">#</th>
                                <th width="34%">Họ và tên</th>
                                <th width="20%" class="text-right">Điểm</th>
                                <th width="45%">Công ty</th>
                            </tr>
                        </thead>
                        <tbody>
                ';
        $i = 1;
        foreach ($users as $k => $v) {
            $name = $v['name'];
            if($v['rated_point'] > 0){
                $fmt = '<span class="badge badge-warning small">%s<i class="fa fa-star"></i>(%s)</span>';
                $name .= sprintf($fmt, round($v['rated_point'], 2), $v['rated_quantity'] );
            }
            
            if(strtotime($v['expired_date']) < time()){
                $name .= '<p class="group-expired">(Hết hạn)</p>';
            }
            
            $str .= '
                <tr class="row-' . $i . '">
                    <td width="1%">' . ($i > 1 ? $i : '<i class="fa fa-trophy"></i>') . '</td>
                    <td width="34%">' . $name . '</td>
                    <td width="20" class="text-right">
                        <span class="money">' . System::display_number($v['total']) . '</span><br>
                        <span class="qty text-gray small">(' . System::display_number($v['qty']) . ' đơn)</span>
                    </td>
                    <td width="45%" class="small">' . $v['group_name'] . '</td>
                </tr>
            ';
            $i++;
        }
        $str .= '</tbody>';
        $str .= '</table>';
        echo $str;
    }
}
