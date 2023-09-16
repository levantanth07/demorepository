<?php
require_once ROOT_PATH . 'packages/core/includes/common/Arr.php';
class ReportForm extends Form
{
    protected $map;

    private $timeFrom;
    private $timeTo;

    private $group_id;
    private $selectUsers;
    private $selectIsActive;
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
        Form::Form('ReportForm');
        // $this->link_js('assets/standard/js/multi.select.js');
        // $this->link_css('assets/standard/css/multi-select.css?v=18102021');
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');
        $this->group_id = Session::get('group_id');
        $this->isTeamLead = is_account_group_manager();
        $this->selectUsers = URL::getArray('users', [0]);
        $this->selectIsActive = URL::getUInt('is_active', 1);
        $this->unknowTeamID = 100000;
        $this->isOwner = is_group_owner();
        $this->isObd = isObd();

        self::$statusHuy = HUY;
        self::$statusThanhCong = THANH_CONG;
        self::$statusXacNhan = XAC_NHAN;
        self::$statusChuyenHang = CHUYEN_HANG;
        self::$statusChuyenHoan = CHUYEN_HOAN;
        self::$statusDaTraHangVeKho = TRA_VE_KHO;
        self::$statusDaThuTien = DA_THU_TIEN;
        self::$statusKeToan = KE_TOAN;
    }   
    function draw()
    {
        if(!checkPermissionAccess(['MARKETING','ADMIN_MARKETING','BC_DOANH_THU_MKT']) && !Dashboard::$xem_khoi_bc_marketing){
            Url::access_denied();
        }
        $current_account_id =Dashboard::$account_id;
        $tong_cong_ty = false;
        $account_type = Dashboard::$account_type;
        $master_group_id = Dashboard::$master_group_id;
        if($account_type==TONG_CONG_TY){
            $tong_cong_ty = true;
        }

        $date_type = Url::get('date_type');
        $this->map = array();
        $this->map['total'] = 0;
        $this->map['so_cap_max'] = 0;
        $this->map['doanh_thu_mkt_max'] = 0;
        $this->map['tong_so_cap'] = 0;
        $this->map['tong_so_chot_per_cap'] = 0;
        $this->map['tong_so_chot_per_chia'] = 0;
        $this->map['tong_so_chia'] = 0;
        $this->map['so_tiep_can_total'] = 0;
        $this->map['ket_noi'] = 0;
        if(!Url::get('date_from')){
            $_REQUEST['date_from'] = date('d/m/Y');
        }
        if(!Url::get('date_to')){
            $_REQUEST['date_to'] = date('d/m/Y');
        }
        
        $account_type = Session::get('account_type');

        $this->timeFrom = $start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
        $this->timeTo = $end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));

        if(strtotime($this->timeTo) - strtotime($this->timeFrom) > 31*24*3600){
            die('<div class="alert alert-warning">Bạn vui lòng chọn tối đa 1 tháng!</div>');
        }

        $status = DashboardDB::get_report_statuses();
        $users = DashboardDB::getUserNew(false, Url::get('is_active'));
        $userRequest = [];
        $this->map['users_ids_option'] = '';
        $userRequest = Url::get('users_ids');
        if(!empty($userRequest)){
            foreach($users as $key=>$val){
                if (in_array($key,$userRequest)) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $this->map['users_ids_option'] .= '<option value="'.$key.'"'.$selected.'>'.$val['username'].'</option>';
            }
        } else {
            foreach($users as $key=>$val){
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
        $reports = array();
        $no_revenue_status = DashboardDB::get_no_revenue_status();
        $this->processDataMKT($users,$no_revenue_status,$start_time,$end_time,$account_type,$this->group_id,$master_group_id,$status,$tong_cong_ty, $date_type, $userRequest, $current_account_id, $strBundleRequets);
        $months = array();
        for($i=1;$i<=12;$i++){
            $months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
        }
        $this->map['month_list'] = $months;
        $this->map['is_active_list'] = array(''=>'Kích hoạt',1=>'Chưa kích hoạt',2=>'Tất cả User');
        $this->map['users_id_list'] = [''=>'Tất cả Upsale'] + MiString::get_list($users,'full_name');
        //$account_groups = get_account_groups();
        $account_groups = DashboardDB::getAccountGroup();
        $this->map['account_group_id_list'] = array(''=>'Nhóm tài khoản') + MiString::get_list($account_groups);

        $this->map['type_list'] = Dashboard::$type;
        $this->map += DashboardDB::get_report_info();
        $this->map['group_id_list'] = array('' => 'Chọn công ty','0'=>'Tất cả') + MiString::get_list(DashboardDB::get_groups());
        // $this->map['date_type_list'] = Dashboard::$date_type;

        $dateType = [
            2=>'Ngày xác nhận',
            1=>'Ngày trạng thái'
        ];
        $this->map['date_type_list'] = $dateType;

        $this->parse_layout('doanh_thu_upsale',$this->map);
    }

    private function getDataConfirmed($condCfType, $start_time, $end_time, $strUserIds, $date_type)
    {
        $condCfType .= ' and orders_extra.upsale_from_user_id IN ('.$strUserIds.')';
        $join = ' LEFT JOIN 
                           orders_extra on orders_extra.order_id = orders.id ';
        $conConfirmed = ' and orders.confirmed>="'.$start_time.' 00:00:00" 
                         and orders.confirmed<="'.$end_time.' 23:59:59" ';
        $condChuyenHoan = $condCfType;
        $condDaTraHangVeKho = $condCfType;
        $condChuyenHang = $condCfType;
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
        } else {
            $condChuyenHoan .= $conConfirmed . ' and orders.status_id = '.self::$statusChuyenHoan.' ';
            $condDaTraHangVeKho .= $conConfirmed . ' and orders.status_id = '. self::$statusDaTraHangVeKho. '';
            $condChuyenHang .= $conConfirmed . ' and orders.status_id = '.self::$statusChuyenHang.' ';
        }
        $sqlXacNhan = '  SELECT 
                            orders_extra.upsale_from_user_id as id,
                            COUNT(orders.id) as total_qty_xac_nhan,
                            SUM(orders.total_price) as total_price_xac_nhan
                        FROM 
                            orders
                             ' .$join. '
                        WHERE 
                            '.$condXacNhan.' AND orders.status_id <> '. self::$statusHuy .'
                        GROUP BY 
                            orders_extra.upsale_from_user_id
                    ';
        $dataXacNhan = DB::fetch_all($sqlXacNhan);
        $sqlChuyenHang = '  SELECT 
                            orders_extra.upsale_from_user_id as id,
                            COUNT(orders.id) as total_qty_da_chuyen_hang,
                            SUM(orders.total_price) as total_price_da_chuyen_hang
                        FROM 
                            orders
                            ' .$join. '
                        WHERE 
                            '.$condChuyenHang.' 
                        GROUP BY 
                            orders_extra.upsale_from_user_id
                    ';
        $dataChuyenHang = DB::fetch_all($sqlChuyenHang);
        $sqlChuyenHoan = '  SELECT 
                            orders_extra.upsale_from_user_id as id,
                            COUNT(orders.id) as total_qty_chuyen_hoan,
                            SUM(orders.total_price) as total_price_chuyen_hoan
                        FROM 
                            orders
                            ' .$join. '
                        WHERE 
                            '.$condChuyenHoan.' 
                        GROUP BY 
                            orders_extra.upsale_from_user_id
                    ';
        $dataChuyenHoan = DB::fetch_all($sqlChuyenHoan);
        $sqlDaTraHangVeKho = '  SELECT 
                            orders_extra.upsale_from_user_id as id,
                            COUNT(orders.id) as total_qty_da_tra_hang_ve_kho,
                            SUM(orders.total_price) as total_price_da_tra_hang_ve_kho
                        FROM 
                            orders
                            ' .$join. '
                        WHERE 
                            '.$condDaTraHangVeKho.' 
                        GROUP BY 
                            orders_extra.upsale_from_user_id
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
    private function getDataHuy($condHuy, $start_time, $end_time, $strUserIds, $date_type){
        if($date_type==1){
            $condHuy .= ' and orders_extra.update_cancel_time>="'.$start_time.' 00:00:00" and orders_extra.update_cancel_time<="'.$end_time.' 23:59:59"';
            $condHuy .= ' and orders.confirmed IS NOT NULL and orders.user_confirmed IS NOT NULL' ;
            $condHuy .= ' and orders.status_id='.self::$statusHuy;
        } else {
            $condHuy .= ' and orders.confirmed>="'. $start_time . ' 00:00:00" and  orders.confirmed<="' . $end_time . ' 23:59:59" ' ;
            $condHuy .= ' and orders.status_id='.self::$statusHuy;        
        }
        $condHuy .= ' and orders_extra.upsale_from_user_id IN ('.$strUserIds.') ';  
        $sqlHuy = 'SELECT 
                            orders_extra.upsale_from_user_id as id,
                            COUNT(orders.id) as total_qty_huy, 
                            SUM(orders.total_price) as total_price_huy 
                        FROM 
                            orders 
                        LEFT JOIN 
                           orders_extra on orders_extra.order_id = orders.id
                        WHERE 
                            ' .$condHuy. ' 
                        GROUP BY 
                           orders_extra.upsale_from_user_id';
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
    private function getDataKeToan($condKeToan, $start_time, $end_time, $strUserIds, $date_type){
        if($date_type==1){
            $condKeToan .= ' and orders_extra.accounting_confirmed>="'.$start_time.' 00:00:00" and orders_extra.accounting_confirmed<="'.$end_time.' 23:59:59"';
        } else {
            $condKeToan .= ' and orders.confirmed>="'. $start_time . ' 00:00:00" and  orders.confirmed<="' . $end_time . ' 23:59:59" ' ;
        }
        $condKeToan .= ' and orders_extra.upsale_from_user_id IN (' . $strUserIds . ')';
        $condKeToan .= ' AND orders_extra.accounting_confirmed IS NOT NULL 
                        AND orders_extra.accounting_confirmed  >= orders.confirmed';
        $sqlKeToan = 'SELECT 
                            orders_extra.upsale_from_user_id as id,
                            SUM(orders.total_price) as total_price_ke_toan,
                            COUNT(orders.id) as total_qty_ke_toan
                        FROM 
                            orders
                        LEFT JOIN 
                           orders_extra on orders_extra.order_id = orders.id 
                        WHERE 
                            '.$condKeToan.'
                        GROUP BY
                            orders_extra.upsale_from_user_id
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
    private function getDataThanhCong($condThanhCong,$start_time, $end_time, $strUserIds, $date_type){
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

        
        $condThanhCong .= ' AND orders_extra.upsale_from_user_id IN ('.$strUserIds.')';
        $sqlThanhCong = '
                        SELECT
                            orders_extra.upsale_from_user_id as id,
                            SUM(orders.total_price) as total_price_thanh_cong, 
                            COUNT(orders.id) as total_qty_thanh_cong
                        FROM
                            orders
                        LEFT JOIN 
                            orders_extra ON orders_extra.order_id = orders.id
                        WHERE
                            '.$condThanhCong.'
                        GROUP BY
                            orders_extra.upsale_from_user_id';
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
    private function getDataDaThuTien($condDaThuTien, $start_time, $end_time, $strUserIds,$date_type){
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
        $condDaThuTien .= ' AND orders_extra.upsale_from_user_id IN ('.$strUserIds.')';

        
        $sqlDaThuTien = '
            SELECT
                orders_extra.upsale_from_user_id as id,
                SUM(orders.total_price) as total_price_da_thu_tien, 
                COUNT(orders.id) as total_qty_da_thu_tien
            FROM
                orders
            LEFT JOIN 
                orders_extra ON orders_extra.order_id = orders.id
            WHERE
                '.$condDaThuTien.'
            GROUP BY
                orders_extra.upsale_from_user_id';
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
    private function getDataSoTiepCan($conSoTiepCan){
        if($type=Url::iget('type')){
            $conSoTiepCan .= ' AND orders.type='.$type.'';
        }
        $sqlSoTiepCan = 'SELECT 
                            orders_extra.upsale_from_user_id as id, 
                            COUNT(orders.id) as total 
                        FROM 
                            orders 
                        JOIN 
                            statuses ON orders.status_id = statuses.id
                        LEFT JOIN
                            groups on groups.id=orders.group_id
                        LEFT JOIN 
                            orders_extra on orders_extra.order_id=orders.id
                        WHERE 
                            '.$conSoTiepCan.' 
                        GROUP BY 
                            orders_extra.upsale_from_user_id';
        $dataSoTiepCan= DB::fetch_all($sqlSoTiepCan);
        return $dataSoTiepCan;
    }
    private function getDataSoChia($cond, $start_time, $end_time, $strUserIds){
        $cond_so_chia = $cond. ' and orders.assigned>="'.$start_time.' 00:00:00" 
                    and orders.assigned<="'.$end_time.' 23:59:59" 
                    and orders_extra.upsale_from_user_id IN ('.$strUserIds.')';
        $sql_so_chia = '
                SELECT
                    orders.user_created as id,
                    count(orders.id) as total
                FROM 
                    orders
                JOIN 
                    groups on groups.id=orders.group_id
                LEFT JOIN 
                    orders_extra on orders_extra.order_id=orders.id
                WHERE 
                    '.$cond_so_chia.'
                GROUP BY 
                    orders.user_created
            ';
        $query_so_chia = DB::fetch_all($sql_so_chia);
        return $query_so_chia;
    }
    private function getDataSoCap($cond, $start_time, $end_time, $strUserIds){
        $cond_so_cap = $cond. ' and orders.created>="'.$start_time.' 00:00:00" 
                    and orders.created<="'.$end_time.' 23:59:59" 
                    and orders_extra.upsale_from_user_id IN ('.$strUserIds.')';
        $sql = '
            SELECT
                orders_extra.upsale_from_user_id as id,
                count(orders.id) as total
            FROM 
                orders
            JOIN
                groups on groups.id=orders.group_id
            LEFT JOIN 
                orders_extra on orders_extra.order_id=orders.id
            WHERE 
                '.$cond_so_cap.'
            GROUP BY
                orders_extra.upsale_from_user_id
        ';
        $total = DB::fetch_all($sql);
        return $total;
    }
    private function getDataChuyenHang($cond_chuyen_hang, $group_id, $start_time, $end_time, $strUserIds){
        $cond_chuyen_hang = '
                                (orders.group_id='.$group_id.') 
                                and orders.confirmed>="'.$start_time.' 00:00:00" 
                                and  orders.confirmed<="'.$end_time.' 23:59:59" 
                                and orders.user_created IN ('.$strUserIds.') 
                                and orders.delivered>=orders.confirmed';
        $sql_chuyen_hang = 'SELECT user_created as id,COUNT(*) as total_qty , SUM(orders.total_price) as total_price
                                FROM 
                                    orders 
                                WHERE 
                                    '.$cond_chuyen_hang.' 
                                GROUP BY 
                                    user_created';
        $query_chuyen_hang = DB::fetch_all($sql_chuyen_hang);
        return $query_chuyen_hang;
    }
    private function processDataMKT($users,$no_revenue_status,$start_time,$end_time,$account_type,$group_id,$master_group_id,$dataStatus,$tong_cong_ty,$date_type,$userRequest, $current_account_id, $strBundleRequets){
        $dataSales = [];
        $total_transport_total = 0;
        $this->map['doanh_thu_sale_max'] = 0;
        if(Url::get('view_report')){
            $dataStatus[1000000000] = array('id'=>1000000000,'name'=>'Tổng','total'=>0,'qty'=>0);
            $dataSales['label']['total'] = 99999999999999;
            $dataSales['label']['id'] = 'label';
            $dataSales['label']['name'] = 'Nhân viên MKT';
            $dataSales['label']['sl'] = 'SL';
            $dataSales['label']['so_chia'] = 'Số chia';
            $dataSales['label']['so_cap'] = 'Số cấp';
            $dataSales['label']['so_tiep_can'] = 'Số tiếp cận';
            $dataSales['label']['don_chot'] = 'Đơn chốt';

            if(!empty($userRequest)){
                $userIds = $userRequest;
                $request = [];
                foreach ($users as $key => $value) {
                    foreach ($userRequest as $k => $v) {
                        if($key == $v){
                            $request[$key] = $value;
                        }
                    }
                }
                $users = $request;
            } else {
                $userIds = array_keys($users);
            }

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
            $strUserIds = implode(',', $userIds);
            $cond_chuyen_hang = '';
            $getDataChuyenHang = $this->getDataChuyenHang($cond_chuyen_hang, $group_id, $start_time, $end_time, $strUserIds);
            
            

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
            if($type=Url::iget('type')){
                $cond .= ' AND orders.type='.$type.'';
            }

            $_strBundleRequets = '';
            if($strBundleRequets) {
                $_strBundleRequets = $this->isObd 
                    ? DashboardDB::getIncludeBundleIds($strBundleRequets, $this->group_id)
                    : $strBundleRequets;
            }//end if

            if($strBundleRequets){
                $cond .= ' AND orders.bundle_id IN ('.$_strBundleRequets.')';
            }
            if($source_id=Url::iget('source_id')){
                $cond .= ' AND orders.source_id='.$source_id.'';
            }

            $getDataSoCap = $this->getDataSoCap($cond, $start_time, $end_time, $strUserIds);
            $con_tiep_can = '
                    (orders.group_id='.$this->group_id.'
                    '.($master_group_id?' or groups.master_group_id='.$master_group_id:'').'
                    '.($tong_cong_ty?' or groups.master_group_id='.$this->group_id:'').'
                    )
                    and orders.created>="'.$start_time.' 00:00:00" and  orders.created<="'.$end_time.' 23:59:59" 
                    and orders_extra.upsale_from_user_id IN ('.$strUserIds.') and IFNULL(statuses.not_reach,0) <> 1
                    ';

            if($strBundleRequets){
                $con_tiep_can .= ' AND orders.bundle_id IN ('.$_strBundleRequets.')';
            }
            $getDataSoTiepCan = $this->getDataSoTiepCan($con_tiep_can);
            $getDataSoChia = $this->getDataSoChia($cond, $start_time, $end_time, $strUserIds);

            $condHuy = $cond;
            if($no_revenue_status){
                if($date_type != 1){
                    $cond .= ' and orders.status_id NOT IN ('.$no_revenue_status.')';
                }
            }
            $condDaThuTien = $cond;
            $getDataDaThuTien = $this->getDataDaThuTien($condDaThuTien, $start_time, $end_time, $strUserIds, $date_type);

            $condThanhCong = $cond;
            $getDataThanhCong = $this->getDataThanhCong($condThanhCong, $start_time, $end_time, $strUserIds, $date_type);

            $condCfType = $cond;
            
            $getDataConfirmed = $this->getDataConfirmed($condCfType, $start_time, $end_time, $strUserIds, $date_type);

            // END DATA XAC NHAN
            $getDataHuy = $this->getDataHuy($condHuy, $start_time, $end_time, $strUserIds, $date_type);

            $condKeToan = $cond;
            $getDataKeToan = $this->getDataKeToan($condKeToan, $start_time, $end_time, $strUserIds, $date_type);
            $total_transport_total = 0;
            foreach ($users as $userKey => $userValue) {
                $total_total_price_sale_total = 0;
                $total_total_qty_sale_total = 0;
                $soChia = $dataSales[$userKey]['so_chia'] = $getDataSoChia[$userValue['user_id']]['total'] ?? 0;
                $soTiepCan = $dataSales[$userKey]['so_tiep_can'] = $getDataSoTiepCan[$userValue['user_id']]['total'] ?? 0;
                $soCap = $dataSales[$userKey]['so_cap'] = $getDataSoCap[$userValue['user_id']]['total'] ?? 0;
                $this->map['tong_so_cap'] += $soCap;
                $this->map['tong_so_chia'] += $soChia;
                $this->map['so_tiep_can_total'] += $soTiepCan;
                if($this->map['so_cap_max']<=$soCap){
                    $this->map['so_cap_max'] = $soCap;
                }
                if($dataSales[$userKey]['so_chia']>0){
                    $dataSales[$userKey]['ty_le_ket_noi'] = $ty_le_ket_noi = (round($dataSales[$userKey]['so_tiep_can']/$dataSales[$userKey]['so_chia'],2)*100).'';
                }else{
                    $dataSales[$userKey]['ty_le_ket_noi'] = '';
                    $ty_le_ket_noi = 0;
                }
                $dataSales[$userKey]['ty_le_ket_noi_color'] = DashboardDB::get_color_by_rate($ty_le_ket_noi);
                $this->map['ket_noi'] = $this->map['tong_so_chia'] > 0 ?  (round($this->map['so_tiep_can_total']/$this->map['tong_so_chia'],2)*100) : 0;
                if($dataSales[$userKey]['so_chia']>0){
                    $dataSales[$userKey]['ty_le_ket_noi'] = $ty_le_ket_noi = (round($dataSales[$userKey]['so_tiep_can']/$dataSales[$userKey]['so_chia'],2)*100).'';
                }else{
                    $dataSales[$userKey]['ty_le_ket_noi'] = '';
                    $ty_le_ket_noi = 0;
                }
                $dataSales[$userKey]['ty_le_ket_noi_color'] = DashboardDB::get_color_by_rate($ty_le_ket_noi);
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
        if(sizeof($dataSales)>=2){
            System::sksort($dataSales, 'total','DESC');
        }
        $this->map['tong_so_chot_per_cap'] = (($this->map['tong_so_cap']>0)?round($dataStatus[XAC_NHAN]['qty']/$this->map['tong_so_cap'],3)*100:0);
        $this->map['tong_so_chot_per_chia'] = (($this->map['tong_so_chia']>0)?round($dataStatus[XAC_NHAN]['qty']/$this->map['tong_so_chia'],3)*100:0);
        $this->map['reports'] = $dataSales;
        $this->map['mkt_status'] = $dataStatus;
    }
}
?>
