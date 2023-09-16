<?php
class ReportForm extends Form
{
    protected $map;
    function __construct()
    {
        Form::Form('ReportForm');
    }   
    function draw()
    {       
        if(!checkPermissionAccess(['BC_DOANH_THU_NV','CSKH']) && !Dashboard::$xem_khoi_bc_sale){
            Url::access_denied();
        }
        $this->map = array();
        $this->map['total'] = 0;
        $group_id = Session::get('group_id');
        //////////////////////////////////////////////////
        if(!Url::get('date_from')){
            $_REQUEST['date_from'] = date('01/m/Y');
        }
        if(!Url::get('date_to')){
            $_REQUEST['date_to'] = date('d/m/Y');//Date_Time::get_last_day_of_month($month,$year)
        }
        
        $start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
        $end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));
        if(strtotime($start_time)>strtotime($end_time)){
            die('Thời gian bắt đầu và thời gian kết thúc không hợp lệ!');
        }
        if(Date_Time::count_day(strtotime($start_time),strtotime($end_time)) > 31){
            die('Bạn vui lòng chọn thời gian không quá 1 tháng!');
        }
        $status = DashboardDB::get_statuses();
        $users = DashboardDB::getUserNew('GANDON');
        $reports = [];
        $theads = [];

        $arrViewTypeTime = array(1=>'assigned',2=>'confirmed',3=>'delivered');
        $arrViewTypeUser = array(1=>'user_assigned',2=>'user_confirmed',3=>'user_confirmed');
        $columnTime = $arrViewTypeTime[URL::getUInt('view_type', 1)];
        $columnUser = $arrViewTypeUser[URL::getUInt('view_type', 1)];

        $this->map['status_arr'] = $status;
        if(Url::get('status_ids')){
            foreach($status as $k=>$v){
                if (!in_array($k, $_REQUEST['status_ids'])) {
                    unset($status[$k]);
                }
            }
        }

        if(Url::get('view_report')){
            $revenue_status = DashboardDB::get_revenue_status();
            $theads['id'] = 'label';
            $theads['username'] = 'Tên tài khoản';
            $theads['name'] = 'Nhân viên';
            foreach($users as $key=>$value){
                foreach($status as $k=>$v){
                    $theads[$k] = $v['name'];
                    $status[$k]['total'] = 0;
                    $status[$k]['orders_qty'] = 0;
                }
            }
            $theads['total'] = 1000000000000000000000000;

            $this->map['_total'] = 0;
            $this->map['_orders_qty'] = 0;
            foreach($users as $key=>$value){
                $reports[$key]['id'] = $key;
                $reports[$key]['username'] = $value['id'];
                $reports[$key]['name'] = $value['full_name'];

                // Thống kê số liệu
                $cond = 'orders.group_id='.$group_id.'';
                if ($revenue_status) {
                    $cond .= ' and orders.status_id IN ('.$revenue_status.') and orders.' . $columnUser . ' IS NOT NULL';
                }
                $cond .= ' and orders.' . $columnTime . '>="'.$start_time.'"';
                $cond .= ' and orders.' . $columnTime . '<="'.$end_time.' 23:59:59"';
                $cond .= ' and orders.' . $columnUser . '='.$value['user_id'];
                $statusStatistics = DB::fetch_all_key('select status_id, sum(total_price) as total, count(*) as orders_qty from orders where '.$cond.' group by status_id', 'status_id');
                // Tổng tiền và tống đơn của user
                $totalPriceOfUser = 0;
                $ordersQtyOfUser = 0;

                // Tính thống kê của user cho từng trạng thái
                foreach($status as $k=>$v){

                    // Tổng tiền và tổng đơn tại trạng thái đang xét 
                    $total_price = isset($statusStatistics[$k]['total']) ? $statusStatistics[$k]['total'] : 0;
                    $orders_qty = isset($statusStatistics[$k]['orders_qty']) ? $statusStatistics[$k]['orders_qty'] : 0;

                    $reports[$key][$k] = [
                        'total_num' => $total_price,
                        'total' => System::display_number($total_price),
                        'orders_qty' => System::display_number($orders_qty)
                    ];

                    $totalPriceOfUser += $total_price;
                    $ordersQtyOfUser += $orders_qty;
                    
                    $status[$k]['total'] += $total_price;
                    $status[$k]['orders_qty'] += $orders_qty;
                }

                $this->map['_total'] += $totalPriceOfUser;
                $this->map['_orders_qty'] += $ordersQtyOfUser;
                
                $reports[$key]['total'] = $totalPriceOfUser;
                $reports[$key]['orders_qty'] = $ordersQtyOfUser;
            }
        }
        $this->map['total'] = System::display_number($this->map['total']);
        ///////////////////////////////////////////
        if(sizeof($reports)>2){
            System::sksort($reports, 'total','DESC');
        }
        $this->map['reports'] = $reports;
        $this->map['status'] = $status;
        $this->map['theads'] = $theads;

        $months = array();
        for($i=1;$i<=12;$i++){
            $months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
        }
        $this->map['month_list'] = $months;
        $this->map += DashboardDB::get_report_info();
        $this->map['group_id_list'] = array('' => 'Chọn công ty') + MiString::get_list(DashboardDB::get_groups());
        $this->parse_layout('report',$this->map);
    }
}
?>
