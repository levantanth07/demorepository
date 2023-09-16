<?php
class ReasonFailForm extends Form
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
    public static $quyen_bc_doanh_thu_mkt;
    function __construct()
    {
        Dashboard::$is_account_group_manager = is_account_group_manager();
        Dashboard::$quyen_bc_doanh_thu_nv = check_user_privilege('BC_DOANH_THU_NV');
        Dashboard::$quyen_bc_doanh_thu_mkt = check_user_privilege('BC_DOANH_THU_MKT');
        Dashboard::$quyen_marketing = check_user_privilege('MARKETING');
        Dashboard::$quyen_admin_marketing = check_user_privilege('ADMIN_MARKETING');
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
        $group_id = Dashboard::$group_id;
        $account_type = Dashboard::$account_type;
        $master_group_id = Dashboard::$master_group_id;
        $is_account_group_department = is_account_group_department();

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
        $users = DashboardDB::getUserNew2('GANDON',DB::escape(Url::get('is_active')));

        $this->processDataSale($users,$start_time,$end_time,$account_type,$group_id,$master_group_id,$status);
        $this->map['is_active_list'] = array(2=>'Tất cả',''=>'Tài khoản kích hoạt',1=>'Tài khoản chưa kích hoạt');
        $this->map += DashboardDB::get_report_info();
        $dateType = [
            1=>'Ngày Xác nhận chốt đơn',
            2=>'Ngày Chuyển hàng'
        ];
        $statusType = [
            0=>'Tất cả',
            1=>'Giai đoạn Chuyển hàng',
            2=>'Giai đoạn Chuyển hoàn'
        ];
        $this->map['date_type_list'] = $dateType;
        $this->map['status_type_list'] = $statusType;
        $this->parse_layout('reason_fail',$this->map);
    }
    private function getDataConfirmed($condCfType, $start_time, $end_time, $strUserIds, $date_type,$status_type)
    {
        $condCfType .= ' and orders.user_confirmed IN ('.$strUserIds.')';
        $join = ' LEFT JOIN orders_extra on orders_extra.order_id = orders.id AND orders_extra.order_not_success IS NOT NULL';
        $conSql = $condCfType;
        if($date_type == 1 ) {
            $conSql .= ' and orders.confirmed >= "'.$start_time.' 00:00:00" 
                         and orders.confirmed <= "'.$end_time.' 23:59:59" ';

        } else {
            $conSql .= ' and orders.delivered >= "' . $start_time . ' 00:00:00" 
                         and orders.delivered <= "' . $end_time . ' 23:59:59" ';
        }
        if ($status_type == 1) {
            $conSql .= ' and orders.status_id in ( ' .implode(',', DashboardDB::getLevelStatuses([3])).' )';
        } else if ($status_type == 2) {
            $conSql .= ' and orders.status_id in ( ' . implode(',', DashboardDB::getLevelStatuses([4,5])) .') ';
        } else {
            $conSql .= ' and orders.status_id in (' .implode(',', DashboardDB::getLevelStatuses([3,4,5])).') ';
        }
        $sql = '  SELECT 
                        orders.id,
                        orders.user_confirmed as user_confirmed,
                        orders_extra.order_not_success,
                        COUNT(orders.id) as total_qty,
                        SUM(orders.total_price) as total_price
                    FROM 
                        orders
                         ' .$join. '
                    WHERE 
                        '.$conSql.'
                    GROUP BY 
                        orders.user_confirmed,
                        orders_extra.order_not_success
                ';
        $dataSql = DB::fetch_all($sql);
        $newArray = [];
        foreach ($dataSql as $key => $value) {
            $newArray[$key]  = [
                'user_id'=>$value['user_confirmed'],
                'reasons'=>$value['order_not_success'] ,
                'total_price'=>$value['total_price'] ?? 0,
                'total_qty'=>$value['total_qty'] ?? 0
            ];
        };
        return $newArray;
    }
    private function processDataSale($users,$start_time,$end_time,$account_type,$group_id,$master_group_id,$status){
        $dataStatus = [
            1 =>[
                'name'=> 'Đơn đang đi, chưa tới BCP',
                'total_price'=> 0,
                'total_qty'=>0,
                'percentage'=> 0
            ],
            2 =>[
                'name'=> 'Khách hẹn',
                'total_price'=> 0,
                'total_qty'=>0,
                'percentage'=> 0
            ],
            3 =>[
                'name'=> 'Không liên lạc được',
                'total_price'=> 0,
                'total_qty'=>0,
                'percentage'=> 0
            ],
            4 =>[
                'name'=> 'Chưa có tiền',
                'total_price'=> 0,
                'total_qty'=>0,
                'percentage'=> 0
            ],
            5 =>[
                'name'=> 'Khách đi vắng, không có nhà',
                'total_price'=> 0,
                'total_qty'=>0,
                'percentage'=> 0
            ],
            6 =>[
                'name'=> 'Chưa tin tưởng, người nhà không cho dùng',
                'total_price'=> 0,
                'total_qty'=>0,
                'percentage'=> 0
            ],
            7 =>[
                'name'=> 'Lý do khác',
                'total_price'=> 0,
                'total_qty'=>0,
                'percentage'=> 0
            ]
        ];
        $dataSales = [];
        $this->map['doanh_thu_sale_max'] = 0;
        $date_type = Url::get('date_type');
        $status_type = Url::get('status_type');
        if(Url::get('view_report')){
            $dataStatus[1000000000] = array('id'=>1000000000,'name'=>'Tổng','total_price'=>0,'total_qty'=>0,'percentage'=>0);
            $arrUserIds = [];
            foreach($users as $uK => $vK){
                $arrUserIds[] = $vK['user_id'];
                $dataSales[$uK]['id'] = $uK;
                $dataSales[$uK]['total'] = 0;
                $dataSales[$uK]['usename'] = $vK['full_name'].' <div class="small" style="color:#999;font-style: italic;"> '.$vK['username'].' </div>';
                foreach($dataStatus as $sK=>$sV){
                    $dataSales[$uK][$sK] = [
                        1 => [
                            'total_price' => 0,
                            'total_qty' => 0,

                        ]
                    ];
                }
            }

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
            $strUserIds = implode(',', $arrUserIds);
            $condCfType = $cond;
            $getDataConfirmed = $this->getDataConfirmed($condCfType, $start_time, $end_time, $strUserIds, $date_type,$status_type);
            foreach ($users as $userKey => $userValue) {
                $total_total_price_sale_total = 0;
                $total_total_qty_sale_total = 0;
                foreach ($dataStatus as $statusKey => $statusValue) {
                    $total_total_price_sale = 0;
                    $total_total_qty_sale = 0;
                    foreach ($getDataConfirmed as $confirmedKey => $confirmedValue) {
                        if($userValue['user_id'] == $confirmedValue['user_id'] && $confirmedValue['reasons'] == $statusKey){
                            $total_total_price_sale += $confirmedValue['total_price'];
                            $total_total_qty_sale += $confirmedValue['total_qty'];
                            $total_total_price_sale_total += $confirmedValue['total_price'];
                            $total_total_qty_sale_total += $confirmedValue['total_qty'];
                            $dataSales[$userKey][$statusKey] =  [
                                'total_price' => $total_total_price_sale,
                                'total_qty' => $total_total_qty_sale,

                            ];
                            break;
                        }
                    }
                    if (isset($dataStatus[$statusKey]['total_qty'])) {
                        $dataStatus[$statusKey]['total_qty'] += $total_total_qty_sale ? $total_total_qty_sale : 0;

                    }
                    if (isset($dataStatus[$statusKey]['total_price'])) {
                        $dataStatus[$statusKey]['total_price'] += $total_total_price_sale ? $total_total_price_sale : 0;
                    }
                }
                $dataStatus[1000000000]['total_qty'] += $total_total_qty_sale_total;
                $dataStatus[1000000000]['total_price'] += $total_total_price_sale_total;
                $dataSales[$userKey][8]['total_qty'] = $total_total_qty_sale_total;
                $dataSales[$userKey][8]['total_price'] = $total_total_price_sale_total;
                $dataSales[$userKey]['total'] += $total_total_price_sale_total;
            }
        }
        if(sizeof($dataSales)>2){
            System::sksort($dataSales, 'total','DESC');
            foreach ($dataSales as $key => $value) {
                unset($dataSales[$key]['total']);
            }
        }
        $this->map['reports'] = $dataSales;
        $this->map['status'] = $dataStatus;
    }
}
?>
