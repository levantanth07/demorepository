<?php
class ReportForm extends Form
{
    protected $map;
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
    public  static $quyen_bc_doanh_thu_mkt;
    public static $statusHuy;
    public static $statusThanhCong;
    public static $statusXacNhan;
    public static $statusChuyenHang;
    public static $statusChuyenHoan;
    public static $statusDaTraHangVeKho;
    public static $statusDaThuTien;
    public static $statusKeToan;
    protected $isObd;
    function __construct()
    {
        Dashboard::$is_account_group_manager = is_account_group_manager();
        Dashboard::$quyen_bc_doanh_thu_nv = check_user_privilege('BC_DOANH_THU_NV');
        Dashboard::$quyen_bc_doanh_thu_mkt = check_user_privilege('BC_DOANH_THU_MKT');
        Dashboard::$quyen_marketing = check_user_privilege('MARKETING');
        Dashboard::$quyen_admin_marketing = check_user_privilege('ADMIN_MARKETING');
        $this->isObd = isObd();
        self::$statusHuy = HUY;
        self::$statusThanhCong = THANH_CONG;
        self::$statusXacNhan = XAC_NHAN;
        self::$statusChuyenHang = CHUYEN_HANG;
        self::$statusChuyenHoan = CHUYEN_HOAN;
        self::$statusDaTraHangVeKho = TRA_VE_KHO;
        self::$statusDaThuTien = DA_THU_TIEN;
        self::$statusKeToan = KE_TOAN;
        // $this->link_js('assets/standard/js/multi.select.js');
        // $this->link_css('assets/standard/css/multi-select.css?v=18102021');
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');
        $this->link_css('assets/lib/DataTables/datatables.min.css');
        $this->link_js('assets/lib/DataTables/datatables.min.js');
        Form::Form('ReportForm');
    }
    function draw()
    {
        if(!checkPermissionAccess(['BC_DOANH_THU_NV','CSKH']) && !is_account_group_manager() && !Dashboard::$xem_khoi_bc_sale && !is_account_group_department()){
            Url::access_denied();
        }
        $staff_code = 'GANDON';
        $group_id = Dashboard::$group_id;
        $tong_cong_ty = false;
        $account_type = Dashboard::$account_type;
        $master_group_id = Dashboard::$master_group_id;
        $is_account_group_department = is_account_group_department();
        $date_type = Url::get('date_type');
        if($account_type==TONG_CONG_TY){
            $tong_cong_ty = true;
        }
        $this->map['doanh_thu_sale_max'] = 0;
        $this->map = array();
        $this->map['total'] = 0;
        //////////////////////////////////////////////////
        if(!Url::get('date_from')){
            $_REQUEST['date_from'] = date('d/m/Y');
        }
        if(!Url::get('date_to')){
            $_REQUEST['date_to'] = date('d/m/Y');
        }
        $start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
        $end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));
        if(strtotime($end_time) - strtotime($start_time) > 4*31*24*3600){
            die('<div class="alert alert-warning">Bạn vui lòng chọn tối đa 4 tháng!</div>');
        }
        $status = DashboardDB::get_report_statuses();
        $users = DashboardDB::getUserNew('GANDON',DB::escape(Url::get('is_active')));
        $account_groups = DashboardDB::getAccountGroup();
        if(!is_group_owner() && !Dashboard::$admin_group && is_account_group_department() && $account_group_ids = get_account_group_ids() && (Dashboard::$quyen_admin_marketing || Dashboard::$quyen_bc_doanh_thu_mkt)){
            $users = DashboardDB::getUserSale('GANDON',DB::escape(Url::get('is_active')));
            $account_groups = DashboardDB::getAccountSale();
        }
        $mkt_users = DashboardDB::get_users('MARKETING',Url::iget('is_active'),true);
        $all_users = DashboardDB::get_users(false,Url::iget('is_active'),true);
        if(!get_account_id() && DashboardDB::checkUserOnlyRole('QUYEN_XEM_BC_DOI_NHOM') && !is_account_group_manager() && !is_account_group_department() && !Session::get('admin_group') && !is_group_owner()){
            $users = [];
            $account_groups = [];
        }
        $this->map['users_ids_option'] = '';
        $userSaleRequest = [];
        $userSaleRequest = Url::get('users_ids');
        $this->map['upsale_from_user_id'] = '';

        $userRequest = [];
        $userRequest = Url::get('upsale_from_user_id');
        $strUserRequets = '';
        $getUser = [];
        if(Dashboard::$quyen_xem_bc_doi_nhom && !Session::get('admin_group') && !is_group_owner() && DashboardDB::checkMarketing()){
            if(!get_account_id()){
                $users = [];
                $account_groups = [];
            } else {
                $account_groups = DashboardDB::getGroup();
                $getUser = DashboardDB::getUser();
            }
        }
        if($getUser){
            foreach ($users as $k => $v) {
                if (!in_array($k,$getUser)) {
                    unset($users[$k]);
                }
            }
        }
        if(!empty($userRequest)){
            foreach($all_users as $key=>$val){
                if (in_array($key,$userRequest)) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $this->map['upsale_from_user_id'] .= '<option value="'.$key.'"'.$selected.'>'.$val['username'].'</option>';
            }
            $strUserRequets = implode(',', $userRequest);
        } else {
            foreach($all_users as $key=>$val){
                $selected = '';
                $this->map['upsale_from_user_id'] .= '<option value="'.$key.'"'.$selected.'>'.$val['username'].'</option>';
            }
            
        }
        $upsale_from_user_id = Url::getUInt('upsale_from_user_id');
        $upsale_user = DB::fetch('select id,name from users where group_id = ' . Dashboard::$group_id . ' AND id = ' . $upsale_from_user_id);
        $this->map['upsale'] = $upsale_user ? '<div>Upsale: '.$upsale_user['name'].'</div>' : '';
        
        $reports = array();
        
        $no_revenue_status = DashboardDB::get_no_revenue_status();
        $current_account_id = Dashboard::$account_id;
        if(!empty($userSaleRequest)){
            $userIds = $userRequest;
            $request = [];
            foreach($users as $key=>$val){
                if (in_array($key,$userSaleRequest)) {
                    $request[$key] = $val;
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                if(!Dashboard::$is_account_group_manager &&  !Dashboard::$quyen_bc_doanh_thu_nv && !$is_account_group_department && !Dashboard::$quyen_xem_bc_doi_nhom && !Dashboard::$xem_khoi_bc_sale){
                    if($value['username']!=$current_account_id){
                        unset($users[$key]);
                        continue;
                    }
                }
                $this->map['users_ids_option'] .= '<option value="'.$key.'"'.$selected.'>'.$val['username'].'</option>';
            }
            $users = $request;
        } else {
            foreach($users as $key=>$val){
                if(!Dashboard::$is_account_group_manager &&  !Dashboard::$quyen_bc_doanh_thu_nv && !$is_account_group_department && !Dashboard::$quyen_xem_bc_doi_nhom && !Dashboard::$xem_khoi_bc_sale){
                    if($val['username']!=$current_account_id){
                        unset($users[$key]);
                        continue;
                    }
                }
                $selected = '';
                $this->map['users_ids_option'] .= '<option value="'.$key.'"'.$selected.'>'.$val['username'].'</option>';
            }
        }

        $this->map['bundle_id_list'] = '';
        $bundleRequest = [];
        $bundleRequest = Url::get('bundle_ids');
        $strBundleRequets = '';
        if ($this->isObd) {
            $bundles = DashboardDB::getBundles();
        } else {
            $bundles = DashboardDB::get_bundles();
        }//end if
        if(!empty($bundleRequest)){
            foreach($bundles as $key=>$val){
                if (in_array($key,$bundleRequest)) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $this->map['bundle_id_list'] .= '<option value="'.$key.'"'.$selected.'>'.$val['name'].'</option>';
            }
            $strBundleRequets = implode(',', $bundleRequest);
        } else {
            foreach($bundles as $key=>$val){
                $selected = '';
                $this->map['bundle_id_list'] .= '<option value="'.$key.'"'.$selected.'>'.$val['name'].'</option>';
            }

        }

        $this->processDataSale($users,$no_revenue_status,$start_time,$end_time,$account_type,$group_id,$master_group_id,$status,$tong_cong_ty, $date_type, $strUserRequets, $strBundleRequets);

        $months = array();
        for($i=1;$i<=12;$i++){
            $months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
        }
        $this->map['month_list'] = $months;
        $this->map['year_list'] = array(2018=>2018,2019=>2019,2020=>2020);
        $this->map['type_list'] = Dashboard::$type;
        $this->map['is_active_list'] = array(''=>'Tài khoản kích hoạt',1=>'Tài khoản chưa kích hoạt',2=>'Tất cả');
        
        $this->map['account_group_id_list'] = array(''=>'Nhóm tài khoản') + MiString::get_list($account_groups);
        $this->map += DashboardDB::get_report_info();
        $sources = $this->isObd ? DashboardDB::getSource() : DashboardDB::get_source();
        $this->map['group_id_list'] = array('' => 'Chọn công ty','0'=>'Tất cả') + MiString::get_list(DashboardDB::get_groups());
        $this->map['source_id_list'] = array('' => 'Đơn từ nguồn','0'=>'Tất cả') + MiString::get_list($sources);
        // $this->map['date_type_list'] = Dashboard::$date_type;
        $dateType = [
            2=>'Ngày xác nhận',
            1=>'Ngày trạng thái'
        ];
        $this->map['date_type_list'] = $dateType;
        $this->map['code_list'] = ['GANDON'=>'Nhân viên Kinh doanh','CSKH'=>'Chăm sóc khách hàng'];
        $this->parse_layout('doanh_thu_nv',$this->map);
    }
    private function getDataConfirmed($condCfType, $start_time, $end_time, $strUserIds, $strUserRequets, $date_type, $conLv3)
    {
        $statusCustomer = DashboardDB::get_status_customer();
       
        $statusLevel3 = array_filter($statusCustomer,function($value){
            return ($value['level'] == 3 && $value['no_revenue'] != 1);
        });
        $statusOrther = array_filter($statusCustomer,function($value){
            return ($value['no_revenue'] == 1 && $value['level'] != 3);
        });

        $strStatuslv3 = implode(',', array_keys($statusLevel3));
        $strStatusOrther = implode(',', array_keys($statusOrther));

        $condCfType .= ' and orders.user_confirmed IN ('.$strUserIds.')';
        $conLv3 .= ' and orders.user_confirmed IN ('.$strUserIds.')';
        $join = ' LEFT JOIN 
                           orders_extra on orders_extra.order_id = orders.id ';
        if($strUserRequets){
            $condCfType .= ' AND orders_extra.upsale_from_user_id IN ('. $strUserRequets.')';
            $conLv3 .= ' AND orders_extra.upsale_from_user_id IN ('. $strUserRequets.')';
        }
        $conConfirmed = ' and orders.confirmed>="'.$start_time.' 00:00:00" 
                         and orders.confirmed<="'.$end_time.' 23:59:59" ';
        $condChuyenHoan = $condCfType;
        $condDaTraHangVeKho = $condCfType;
        $condChuyenHang = $conLv3;
        $condXacNhan = $condCfType . $conConfirmed;

        if($date_type==1){
            $condChuyenHoan .= ' 
                            and orders_extra.update_returned_time>="'.$start_time.' 00:00:00" 
                            and orders_extra.update_returned_time<="'.$end_time.' 23:59:59"
                            ';
            $condDaTraHangVeKho .= ' 
                                and orders_extra.update_returned_to_warehouse_time>="'.$start_time.' 00:00:00" 
                                and orders_extra.update_returned_to_warehouse_time<="'.$end_time.' 23:59:59" 
                                ';
            $condChuyenHang .= ' 
                                and orders.delivered>="'.$start_time.' 00:00:00" 
                                and orders.delivered<="'.$end_time.' 23:59:59" 
                                ';
            $condChuyenHang .= ' 
                                and orders.status_id IN ('.$strStatuslv3.') and orders.status_id NOT IN ('. $strStatusOrther .')
                                ';

        } else {
            $condChuyenHoan .= $conConfirmed . ' and orders.status_id = ' .self::$statusChuyenHoan.' ';
            $condDaTraHangVeKho .= $conConfirmed . ' and orders.status_id = ' .self::$statusDaTraHangVeKho.' ';
            $condChuyenHang .= $conConfirmed . ' and orders.status_id IN (' .$strStatuslv3. ') AND orders.delivered <> "0000-00-00 00:00:00"  and orders.status_id NOT IN ('. $strStatusOrther .')';
        }
        $sqlXacNhan = '  SELECT 
                            orders.user_confirmed as id,
                            COUNT(orders.id) as total_qty_xac_nhan,
                            SUM(orders.total_price) as total_price_xac_nhan
                        FROM 
                            orders
                             ' .$join. '
                        WHERE 
                            '.$condXacNhan.' AND orders.status_id <> '. self::$statusHuy .'
                        GROUP BY 
                            user_confirmed
                    ';
        $dataXacNhan = DB::fetch_all($sqlXacNhan);
        $sqlChuyenHang = '  SELECT 
                            orders.user_confirmed as id,
                            COUNT(orders.id) as total_qty_da_chuyen_hang,
                            SUM(orders.total_price) as total_price_da_chuyen_hang
                        FROM 
                            orders
                            ' .$join. '
                        WHERE 
                            '.$condChuyenHang.' 
                        GROUP BY 
                            user_confirmed
                    ';
        $dataChuyenHang = DB::fetch_all($sqlChuyenHang);
        $sqlChuyenHoan = '  SELECT 
                            orders.user_confirmed as id,
                            COUNT(orders.id) as total_qty_chuyen_hoan,
                            SUM(orders.total_price) as total_price_chuyen_hoan
                        FROM 
                            orders
                            ' .$join. '
                        WHERE 
                            '.$condChuyenHoan.' 
                        GROUP BY 
                            user_confirmed
                    ';
        $dataChuyenHoan = DB::fetch_all($sqlChuyenHoan);
        $sqlDaTraHangVeKho = '  SELECT 
                            orders.user_confirmed as id,
                            COUNT(orders.id) as total_qty_da_tra_hang_ve_kho,
                            SUM(orders.total_price) as total_price_da_tra_hang_ve_kho
                        FROM 
                            orders
                            ' .$join. '
                        WHERE 
                            '.$condDaTraHangVeKho.' 
                        GROUP BY 
                            user_confirmed
                    ';
        $dataDaTraHangVeKho = DB::fetch_all($sqlDaTraHangVeKho); 
        $newArray = [];
        foreach ($dataXacNhan as $key => $value) {
            $newArray[$key][self::$statusXacNhan] = [
                'user_id'=>$key,
                'status_id'=>self::$statusXacNhan,
                'total_price'=>$value['total_price_xac_nhan'] ?? 0,
                'total_qty'=>$value['total_qty_xac_nhan'] ?? 0
            ];
        }
        foreach ($dataChuyenHang as $key => $value) {
            $newArray[$key][self::$statusChuyenHang] = [
                'user_id'=>$key,
                'status_id'=>self::$statusChuyenHang,
                'total_price'=>$value['total_price_da_chuyen_hang'] ?? 0,
                'total_qty'=>$value['total_qty_da_chuyen_hang'] ?? 0
            ];
        }
        foreach ($dataChuyenHoan as $key => $value) {
            $newArray[$key][self::$statusChuyenHoan] = [
                'user_id'=>$key,
                'status_id'=>self::$statusChuyenHoan,
                'total_price'=>$value['total_price_chuyen_hoan'] ?? 0,
                'total_qty'=>$value['total_qty_chuyen_hoan'] ?? 0
            ];
        }
        foreach ($dataDaTraHangVeKho as $key => $value) {
            $newArray[$key][self::$statusDaTraHangVeKho] = [
                'user_id'=>$key,
                'status_id'=>self::$statusDaTraHangVeKho,
                'total_price'=>$value['total_price_da_tra_hang_ve_kho'] ?? 0,
                'total_qty'=>$value['total_qty_da_tra_hang_ve_kho'] ?? 0
            ];
        }
        return $newArray;
    }
    private function getDataHuy($condHuy, $start_time, $end_time, $strUserIds, $strUserRequets, $date_type){
        $join = ' LEFT JOIN 
                           orders_extra on orders_extra.order_id = orders.id ';
        if($date_type==1){
            $condHuy .= ' and orders_extra.update_cancel_time>="'.$start_time.' 00:00:00" and orders_extra.update_cancel_time<="'.$end_time.' 23:59:59"';
        } else {
            $condHuy .= ' and orders.confirmed>="'. $start_time . ' 00:00:00" and  orders.confirmed<="' . $end_time . ' 23:59:59" ' ;
        }
        $condHuy .= ' and orders.user_confirmed IN ('.$strUserIds.')';
        $condHuy .= ' and orders.status_id='.self::$statusHuy;
        if($strUserRequets){
            $condHuy .= ' AND orders_extra.upsale_from_user_id IN ('. $strUserRequets .')'; 
            
        }  
        $sqlHuy = 'SELECT 
                            orders.user_confirmed as id,
                            COUNT(orders.id) as total_qty_huy, 
                            SUM(orders.total_price) as total_price_huy 
                        FROM 
                            orders 
                            '.$join.'
                        WHERE 
                            ' .$condHuy. ' 
                        GROUP BY 
                            user_confirmed';
        $queryHuy = DB::fetch_all($sqlHuy);
        $newArray = [];
        foreach ($queryHuy as $key => $value) {
            $newArray[$key][self::$statusHuy] = [
                'user_id'=>$key,
                'status_id'=>self::$statusHuy,
                'total_price'=>$value['total_price_huy'] ?? 0,
                'total_qty'=>$value['total_qty_huy'] ?? 0
            ];
        }
        return $newArray;
    }
    private function getDataKeToan($condKeToan, $start_time, $end_time, $strUserIds, $strUserRequets, $date_type){
        if($date_type==1){
            $condKeToan .= ' and orders_extra.accounting_confirmed>="'.$start_time.' 00:00:00" and orders_extra.accounting_confirmed<="'.$end_time.' 23:59:59"';
        } else {
            $condKeToan .= ' and orders.confirmed>="'. $start_time . ' 00:00:00" and  orders.confirmed<="' . $end_time . ' 23:59:59" ' ;
        }
        
        if ($strUserRequets) {
            $condKeToan .= ' AND orders_extra.upsale_from_user_id IN ('. $strUserRequets .')';
        }
        $condKeToan .= ' AND orders_extra.accounting_confirmed IS NOT NULL 
                        AND orders_extra.accounting_confirmed  >= orders.confirmed';
        $condKeToan .= ' and orders.user_confirmed IN (' . $strUserIds . ')';
        $sqlKeToan = 'SELECT 
                            orders.user_confirmed as id,
                            SUM(orders.total_price) as total_price_ke_toan,
                            COUNT(orders.id) as total_qty_ke_toan
                        FROM 
                            orders
                        LEFT JOIN 
                           orders_extra on orders_extra.order_id = orders.id 
                        WHERE 
                            '.$condKeToan.'
                        GROUP BY
                            orders.user_confirmed
                        ';
        $queryKeToan = DB::fetch_all($sqlKeToan);
        $newArray = [];
        foreach ($queryKeToan as $key => $value) {
            $newArray[$key][self::$statusKeToan] = [
                'user_id'=>$key,
                'status_id'=>self::$statusKeToan,
                'total_price'=>$value['total_price_ke_toan'] ?? 0,
                'total_qty'=>$value['total_qty_ke_toan'] ?? 0
            ];
        }
        return $newArray;
    }
    private function getDataThanhCong($condThanhCong,$start_time, $end_time, $strUserIds, $date_type, $strUserRequets){
        if($date_type==1){
            $condThanhCong .= ' and orders_extra.update_successed_time>="'.$start_time.' 00:00:00" and orders_extra.update_successed_time<="'.$end_time.' 23:59:59"';
        }else{
            $condThanhCong .= ' AND orders.confirmed>="'.$start_time.' 00:00:00"';
            $condThanhCong .= ' AND  orders.confirmed<="'.$end_time.' 23:59:59"';
            $condThanhCong .= ' AND orders.status_id NOT IN (' . implode(',', [self::$statusChuyenHoan, self::$statusDaTraHangVeKho]) . ')';
        }
        $condThanhCong .= ' AND orders_extra.update_successed_time IS NOT NULL 
                            AND orders_extra.update_successed_user IS NOT NULL  
                            AND orders_extra.update_successed_time  >= orders.confirmed';
        if($strUserRequets){
            $condThanhCong .= ' AND orders_extra.upsale_from_user_id IN ('. $strUserRequets. ')';
        }
        
        
        $condThanhCong .= ' AND orders.user_confirmed IN ('.$strUserIds.')';
        $sqlThanhCong = '
                        SELECT
                            orders.user_confirmed as id,
                            SUM(orders.total_price) as total_price_thanh_cong, 
                            COUNT(orders.id) as total_qty_thanh_cong
                        FROM
                            orders
                        LEFT JOIN 
                            orders_extra ON orders_extra.order_id = orders.id
                        WHERE
                            '.$condThanhCong.'
                        GROUP BY
                            orders.user_confirmed';
        $queryThanhCong = DB::fetch_all($sqlThanhCong);
        $newArray = [];
        foreach ($queryThanhCong as $key => $value) {
            $newArray[$key][self::$statusThanhCong] = [
                'user_id'=>$key,
                'status_id'=>self::$statusThanhCong,
                'total_price'=>$value['total_price_thanh_cong'] ?? 0,
                'total_qty'=>$value['total_qty_thanh_cong'] ?? 0
            ];
        }
        return $newArray;
    }
    private function getDataDaThuTien($condDaThuTien, $start_time, $end_time, $strUserIds,$date_type, $strUserRequets){
        if($date_type==1){
            $condDaThuTien .= ' and orders_extra.update_paid_time>="'.$start_time.' 00:00:00" and orders_extra.update_paid_time<="'.$end_time.' 23:59:59" ';
            //$condDaThuTien .= ' AND orders.status_id IN (' . implode(',', [self::$statusDaThuTien, self::$statusChuyenHang, self::$statusHuy]) . ')';
        }else{
            $condDaThuTien .= ' AND orders.confirmed>="'.$start_time.' 00:00:00"';
            $condDaThuTien .= ' AND orders.confirmed<="'.$end_time.' 23:59:59"';
            $condDaThuTien .= ' AND orders.status_id IN (' . implode(',', [self::$statusDaThuTien, self::$statusChuyenHang]) . ')';
        }
        $condDaThuTien .= ' AND orders_extra.update_paid_time IS NOT NULL 
                            AND orders_extra.update_paid_user IS NOT NULL 
                            AND orders_extra.update_paid_time  >= orders.confirmed';
        $condDaThuTien .= ' AND orders.user_confirmed IN ('.$strUserIds.')';
        if($strUserRequets){
            $condDaThuTien .= ' AND orders_extra.upsale_from_user_id IN ('. $strUserRequets .')';
        }
        
        $sqlDaThuTien = '
            SELECT
                orders.user_confirmed as id,
                SUM(orders.total_price) as total_price_da_thu_tien, 
                COUNT(orders.id) as total_qty_da_thu_tien
            FROM
                orders
            LEFT JOIN 
                orders_extra ON orders_extra.order_id = orders.id
            WHERE
                '.$condDaThuTien.'
            GROUP BY
                orders.user_confirmed';
        $queryDaThuTien = DB::fetch_all($sqlDaThuTien);
        $newArray = [];
        foreach ($queryDaThuTien as $key => $value) {
            $newArray[$key][self::$statusDaThuTien] = [
                'user_id'=>$key,
                'status_id'=>self::$statusDaThuTien,
                'total_price'=>$value['total_price_da_thu_tien'] ?? 0,
                'total_qty'=>$value['total_qty_da_thu_tien'] ?? 0
            ];
        }
        return $newArray;
    }
    private function getDataSoChia($cond_so_chia){
        if($type=Url::iget('type')){
            $cond_so_chia .= ' AND orders.type='.$type.'';
        }
        if($source_id=Url::iget('source_id')){
                $cond_so_chia .= ' AND orders.source_id='.$source_id.'';
            }
        $sql_so_chia = '
                SELECT
                    orders.user_assigned as id,
                    count(orders.id) as total
                FROM 
                    orders
                JOIN 
                    groups on groups.id=orders.group_id
                WHERE 
                    '.$cond_so_chia.'
                GROUP BY 
                    orders.user_assigned
            ';
        $query_so_chia = DB::fetch_all($sql_so_chia);
        return $query_so_chia;
    }
    private function getDataChuyenHang($cond_chuyen_hang, $group_id, $start_time, $end_time, $strUserIds, $date_type, $strBundleRequets){

        $cond_chuyen_hang = '
                                (orders.group_id='.$group_id.') 
                                and orders.user_confirmed IN ('.$strUserIds.') 
                                and orders.delivered>=orders.confirmed';
        $cond_chuyen_hang .= ' and orders.confirmed>="'.$start_time.' 00:00:00" 
                                and  orders.confirmed<="'.$end_time.' 23:59:59" ';
        if($source_id=Url::iget('source_id')){
                $cond_chuyen_hang .= ' AND orders.source_id='.$source_id.'';
            }
        if($type=Url::iget('type')){
                $cond_chuyen_hang .= ' AND orders.type='.$type.'';
            }
        if($strBundleRequets){
                $cond_chuyen_hang .= ' AND orders.bundle_id IN ('.$strBundleRequets.')';
            }
        $sql_chuyen_hang = 'SELECT user_confirmed as id,COUNT(*) as total_qty , SUM(orders.total_price) as total_price
                                FROM 
                                    orders 
                                WHERE 
                                    '.$cond_chuyen_hang.' 
                                GROUP BY 
                                    user_confirmed';
        $query_chuyen_hang = DB::fetch_all($sql_chuyen_hang);
        return $query_chuyen_hang;
    }
    private function processDataSale($users,$no_revenue_status,$start_time,$end_time,$account_type,$group_id,$master_group_id,$dataStatus,$tong_cong_ty,$date_type, $strUserRequets, $strBundleRequets){
        $dataSales = [];
        $total_transport_total = 0;
        $this->map['doanh_thu_sale_max'] = 0;
        if(Url::get('view_report')){
            $dataStatus[1000000000] = array('id'=>1000000000,'name'=>'Tổng','total'=>0,'qty'=>0);
            $dataSales['label']['total'] = 99999999999999;
            $dataSales['label']['id'] = 'label';
            $dataSales['label']['name'] = 'Nhân viên';
            $dataSales['label']['sl'] = 'SL';
            $dataSales['label']['so_chia'] = 'Số chia';
            $dataSales['label']['don_chot'] = 'Đơn chốt';
            $arrUserIds = [];
            foreach($users as $uK => $vK){
                $arrUserIds[] = $vK['user_id'];
                $dataSales[$uK]['id'] = $uK;
                $dataSales[$uK]['name'] = $vK['full_name'].' <div class="small" style="color:#999;font-style: italic;"> '.$vK['username'].' </div>';
                foreach($dataStatus as $sK=>$sV){
                    $statusArray[] = $sK;
                    $dataSales['label'][$sK][1] = array('total_price'=>'Doanh Thu','qty'=>'Số Lượng','qty_ch'=>'','name'=>$sV['name']);
                    $dataSales[$uK][$sK] = [
                        1 => [
                            'total_price' => 0,
                            'qty' => 0,
                            'name'=>$sV['name']
                        ]
                    ];
                }
            }
            $strUserIds = implode(',', $arrUserIds);
            $cond_chuyen_hang = '';
            $getDataChuyenHang = $this->getDataChuyenHang($cond_chuyen_hang, $group_id, $start_time, $end_time, $strUserIds, $date_type, $strBundleRequets);
            $cond_so_chia = '
                    (orders.group_id='.$group_id.'
                    '.($master_group_id?' or groups.master_group_id='.$master_group_id:'').'
                    '.($tong_cong_ty?' or groups.master_group_id='.$group_id:'').'
                    )
                    and orders.assigned>="'.$start_time.' 00:00:00" 
                    and  orders.assigned<="'.$end_time.' 23:59:59" 
                    and orders.user_assigned IN ('.$strUserIds.')
                ';

            $_strBundleRequets = '';
            if($strBundleRequets) {
                $_strBundleRequets = $this->isObd 
                    ? DashboardDB::getIncludeBundleIds($strBundleRequets, Dashboard::$group_id)
                    : $strBundleRequets;
            
            }//end if

            if($strBundleRequets){
                $cond_so_chia .= ' AND orders.bundle_id IN ('.$_strBundleRequets.')';
            }//end if
            $getDataSoChia = $this->getDataSoChia($cond_so_chia);

            // STATUS DATA
            if($account_type){
                $cond = '(orders.group_id='.$group_id.' or orders.master_group_id='.$group_id.')';
            }else{
                if($master_group_id = Dashboard::$master_group_id){
                    $cond = '(orders.group_id='.$group_id.' or orders.master_group_id='.$master_group_id.')';
                }else{
                    $cond = '(orders.group_id='.$group_id.')';
                }
            }
            if($strBundleRequets){
                $cond .= ' AND orders.bundle_id IN ('.$_strBundleRequets.')';
            }
            if($type=Url::iget('type')){
                $cond .= ' AND orders.type='.$type.'';
            }
            
            if($source_id=Url::iget('source_id')){
                $_strSourceRequets = $this->isObd 
                    ? DashboardDB::getIncludeSourceIds($source_id, Dashboard::$group_id)
                    : $source_id;
                $cond .= " AND orders.source_id IN ($_strSourceRequets)";
            }
            $condHuy = $cond;
            $conLv3 = $cond;
            if($no_revenue_status){
                if($date_type != 1){
                    $cond .= ' and orders.status_id NOT IN ('.$no_revenue_status.')';
                }
            }
            $condDaThuTien = $cond;
            $getDataDaThuTien = $this->getDataDaThuTien($condDaThuTien, $start_time, $end_time, $strUserIds, $date_type, $strUserRequets);

            $condThanhCong = $cond;
            $getDataThanhCong = $this->getDataThanhCong($condThanhCong, $start_time, $end_time, $strUserIds, $date_type, $strUserRequets);

            $condCfType = $cond;
            
            $getDataConfirmed = $this->getDataConfirmed($condCfType, $start_time, $end_time, $strUserIds, $strUserRequets, $date_type, $conLv3);

            // END DATA XAC NHAN
            $getDataHuy = $this->getDataHuy($condHuy, $start_time, $end_time, $strUserIds, $strUserRequets, $date_type);

            $condKeToan = $cond;
            $getDataKeToan = $this->getDataKeToan($condKeToan, $start_time, $end_time, $strUserIds, $strUserRequets, $date_type);
            $total_transport_total = 0;
            foreach ($users as $userKey => $userValue) {
                $total_total_price_sale_total = 0;
                $total_total_qty_sale_total = 0;
                $dataSales[$userKey]['transport'] = $getDataChuyenHang[$userValue['user_id']]['total_qty'] ?? 0;
                $dataSales[$userKey]['transport_total_price'] = $getDataChuyenHang[$userValue['user_id']]['total_price'] ?? 0;
                $dataSales[$userKey]['so_chia'] = $getDataSoChia[$userValue['user_id']]['total'] ?? 0;
                $total_transport_total += $dataSales[$userKey]['transport'];

                $total_ = 0;
                $total_total_price = 0;
                $total_qty = 0;
                $total_price_cancel = 0;
                $total_price_confirm = 0;

                foreach ($dataStatus as $statusKey => $statusValue) {
                    $total_total_price_sale = 0;
                    $total_total_qty_sale = 0;
                    foreach ($getDataConfirmed as $confirmedKey => $confirmedValue) {
                        if($userValue['user_id']==$confirmedKey && isset($confirmedValue[$statusKey]) ? $confirmedValue[$statusKey] : ''){
                            $dataSales[$userKey][$statusKey] = [
                                1 => [
                                    'total_price' => System::display_number($confirmedValue[$statusKey]['total_price']),
                                    'qty' => System::display_number($confirmedValue[$statusKey]['total_qty']),
                                    'name'=>$statusValue['name']
                                ]
                            ];
                            $total_total_price_sale += $confirmedValue[$statusKey]['total_price'];
                            $total_total_qty_sale += $confirmedValue[$statusKey]['total_qty'];
                            if($statusKey==XAC_NHAN or $statusKey==HUY){
                                $total_total_price_sale_total += $confirmedValue[$statusKey]['total_price'];
                                $total_total_qty_sale_total += $confirmedValue[$statusKey]['total_qty'];
                            }
                            break;
                        }
                    }
                    foreach ($getDataThanhCong as $successKey => $successValue) {
                        if($userValue['user_id']==$successKey && isset($successValue[$statusKey]) ? $successValue[$statusKey] : ''){
                            $dataSales[$userKey][$statusKey] = [
                                1 => [
                                    'total_price' => System::display_number($successValue[$statusKey]['total_price']),
                                    'qty' => System::display_number($successValue[$statusKey]['total_qty']),
                                    'name'=>$statusValue['name']
                                ]
                            ];
                            $total_total_price_sale += $successValue[$statusKey]['total_price'];
                            $total_total_qty_sale += $successValue[$statusKey]['total_qty'];
                            break;
                        }
                    }
                    foreach ($getDataDaThuTien as $dathutienKey => $dathutienValue) {
                        if($userValue['user_id']==$dathutienKey && isset($dathutienValue[$statusKey]) ? $dathutienValue[$statusKey] :'' ){
                            $dataSales[$userKey][$statusKey] = [
                                1 => [
                                    'total_price' => System::display_number($dathutienValue[$statusKey]['total_price']),
                                    'qty' => System::display_number($dathutienValue[$statusKey]['total_qty']),
                                    'name'=>$statusValue['name']
                                ]
                            ];
                            $total_total_price_sale += $dathutienValue[$statusKey]['total_price'];
                            $total_total_qty_sale += $dathutienValue[$statusKey]['total_qty'];
                            break;
                        }
                    }
                    foreach ($getDataHuy as $huyKey => $huyValue) {
                        if($userValue['user_id']==$huyKey && isset($huyValue[$statusKey]) ? $huyValue[$statusKey] : ''){
                            $dataSales[$userKey][$statusKey] = [
                                1 => [
                                    'total_price' => System::display_number($huyValue[$statusKey]['total_price']),
                                    'qty' => System::display_number($huyValue[$statusKey]['total_qty']),
                                    'name'=>$statusValue['name']
                                ]
                            ];
                            $total_total_price_sale += $huyValue[$statusKey]['total_price'];
                            $total_total_qty_sale += $huyValue[$statusKey]['total_qty'];
                            if($statusKey==XAC_NHAN or $statusKey==HUY){
                                $total_total_price_sale_total += $huyValue[$statusKey]['total_price'];
                                $total_total_qty_sale_total += $huyValue[$statusKey]['total_qty'];
                            }
                            break;
                        }
                    }
                    foreach ($getDataKeToan as $ketoanKey => $ketoanValue) {
                        if($userValue['user_id']==$ketoanKey && isset($ketoanValue[$statusKey]) ? $ketoanValue[$statusKey] : ''){
                            $dataSales[$userKey][$statusKey] = [
                                1 => [
                                    'total_price' => System::display_number($ketoanValue[$statusKey]['total_price']),
                                    'qty' => System::display_number($ketoanValue[$statusKey]['total_qty']),
                                    'name'=>$statusValue['name']
                                ]
                            ];
                            $total_total_price_sale += $ketoanValue[$statusKey]['total_price'];
                            $total_total_qty_sale += $ketoanValue[$statusKey]['total_qty'];
                            break;
                        }
                    }
                    $dataStatus[$statusKey]['total'] += $total_total_price_sale;
                    $dataStatus[$statusKey]['qty'] += $total_total_qty_sale;
                }
                $dataStatus[1000000000]['total'] += $total_total_price_sale_total;
                $dataStatus[1000000000]['qty'] += $total_total_qty_sale_total;
                $dataSales[$userKey]['sl'] = floatval($total_total_qty_sale_total);
                $dataSales[$userKey]['total'] = floatval($total_total_price_sale_total);
                $dataSales[$userKey][1000000000] = array(
                    1=>array(
                        'total_price'=>System::display_number($total_total_price_sale_total),
                        'qty'=>System::display_number($total_total_qty_sale_total),
                        'name'=>'Tổng'
                    )
                );
            }
        }
        if(sizeof($dataSales)>2){
            System::sksort($dataSales, 'total','DESC');
        }
        $this->map['reports'] = $dataSales;
        $this->map['status'] = $dataStatus;
    }
}
?>
