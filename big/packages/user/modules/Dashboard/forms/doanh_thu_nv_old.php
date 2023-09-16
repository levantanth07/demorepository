<?php
class ReportForm extends Form
{   
    const ALL_UPSALE_USER_ID = 0;
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

    private $reqUpSale;

    function __construct()
    {
        Dashboard::$is_account_group_manager = is_account_group_manager();
        Dashboard::$quyen_bc_doanh_thu_nv = check_user_privilege('BC_DOANH_THU_NV');
        Dashboard::$quyen_bc_doanh_thu_mkt = check_user_privilege('BC_DOANH_THU_MKT');
        Dashboard::$quyen_marketing = check_user_privilege('MARKETING');
        Dashboard::$quyen_admin_marketing = check_user_privilege('ADMIN_MARKETING');
        $this->link_css('assets/lib/DataTables/datatables.min.css');
        $this->link_js('assets/lib/DataTables/datatables.min.js');
        Portal::$extra_header .= '<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0">';
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');
        Form::Form('ReportForm');
    }
    function draw()
    {
        $staff_code = Url::get('code')? DB::escape(Url::get('code')):'GANDON';// MAC DINH SALE
        $group_id = Dashboard::$group_id;
        $tong_cong_ty = false;
        $account_type = Dashboard::$account_type;
        $master_group_id = Dashboard::$master_group_id;
        $date_type = Url::get('date_type');
        if($account_type==TONG_CONG_TY){
            $tong_cong_ty = true;
        }
        $this->map = array();
        $this->map['total'] = 0;
        //////////////////////////////////////////////////
        if(!Url::get('date_from')){
            $_REQUEST['date_from'] = date('d/m/Y');
        }
        if(!Url::get('date_to')){
            $_REQUEST['date_to'] = date('d/m/Y');//date(''.Date_Time::get_last_day_of_month($month,$year)
        }
        $start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
        $end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));
        if(strtotime($end_time) - strtotime($start_time) > 4*31*24*3600){
            die('<div class="alert alert-warning">Bạn vui lòng chọn tối đa 4 tháng!</div>');
        }
        $status = DashboardDB::get_report_statuses();
        $users = DashboardDB::get_users($staff_code,Url::iget('is_active'));
        $mkt_users = DashboardDB::get_users('MARKETING',Url::iget('is_active'),true);
        $this->allUsers = DashboardDB::get_users(false,Url::iget('is_active'),true);
        $this->map['upsale_from_user_id[]_list'] = MiString::get_list($this->allUsers,'full_name');

        $this->reqUpSale = $this->filterUser(URL::getArray('upsale_from_user_id'));
        $this->map['req_upsales'] = $this->reqUpSale;
        $this->map['upsale'] = $this->getUpsaleName();
        
        $reports = array();
        if(Url::get('view_report')){
            $reports['label']['id'] = 'label';
            $reports['label']['name'] = 'Nhân viên';
            $reports['label']['sl'] = 'SL';
            $reports['label']['so_chia'] = 'Số chia';
            $reports['label']['don_chot'] = 'Đơn chốt';
            $status[1000000000] = array('id'=>1000000000,'name'=>'Tổng','total'=>0,'qty'=>0);
            foreach($users as $key=>$value){
                foreach($status as $k=>$v){
                    $reports['label'][$k][1] = array('total_price'=>'Doanh thu','qty'=>'Số Lượng','qty_ch'=>'','name'=>$v['name']);
                }
            }
            $reports['label']['total'] = 1000000000000000000000000;
            // 7: đã xác nhận,9: hủy, 6: chuyển hoàn, 5 thành công
            $no_revenue_status = DashboardDB::get_no_revenue_status();
            foreach($users as $key=>$value){
                $reports[$key]['id'] = $key;
                $reports[$key]['name'] = $value['full_name'].' '.($value['rated_point']>0?'<span class="small text-warning">'.round($value['rated_point'],2).'<i class="fa fa-star"></i>('.$value['rated_quantity'].')</span>':'').' <div class="small" style="color:#999;font-style: italic;"> '.$value['id'].' </div>';
                ///
                $cond_ch = '(orders.group_id='.$group_id.') and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.user_confirmed='.$value['user_id'].' and orders.delivered>=orders.confirmed';
                $total_sql_ch = 'select count(*) as total from orders where '.$cond_ch.' group by user_confirmed';
                $total_order_ch = DB::fetch($total_sql_ch,'total');
                $reports[$key]['transport'] = $total_order_ch;
                $total_sql_ch = 'select sum(orders.total_price) as total from orders where '.$cond_ch.' group by user_confirmed';
                $total_price_ch = DB::fetch($total_sql_ch,'total');
                $reports[$key]['transport_total_price'] = $total_price_ch;
                ///
                $cond_so_chia = '
                    (orders.group_id='.$group_id.'
                    '.($master_group_id?' or groups.master_group_id='.$master_group_id:'').'
                    '.($tong_cong_ty?' or groups.master_group_id='.$group_id:'').'
                    )
                    and orders.assigned>="'.$start_time.' 00:00:00" 
                    and  orders.assigned<="'.$end_time.' 23:59:59" 
                    and orders.user_assigned='.$value['user_id'].'
                ';
                if($type=Url::iget('type')){
                    $cond_so_chia .= ' AND orders.type='.$type.'';
                }
                $total_sql_so_chia = '
                    select 
                        count(orders.id) as total
                    from 
                        orders
                        join `groups` on groups.id=orders.group_id
                    where 
                        '.$cond_so_chia.'
                    group by 
                        orders.user_assigned
                ';
                $total_order_so_chia = DB::fetch($total_sql_so_chia,'total');
                $reports[$key]['so_chia'] = $total_order_so_chia;
                ///
                $total_ = 0;
                $total_total_price = 0;
                $total_qty = 0;
                $total_price_cancel = 0;
                $total_price_confirm = 0;
                foreach($status as $k=>$v){
                    if($account_type){
                        $cond = '(orders.group_id='.$group_id.' or orders.master_group_id='.$group_id.')';
                    }else{
                        if($master_group_id = Dashboard::$master_group_id){
                            $cond = '(orders.group_id='.$group_id.' or orders.master_group_id='.$master_group_id.')';
                        }else{
                            $cond = '(orders.group_id='.$group_id.')';
                        }
                    }
                    if($k!=HUY and $no_revenue_status){
                        $cond .= ' and orders.status_id NOT IN ('.$no_revenue_status.')';
                    }
                    if($type=Url::iget('type')){
                        $cond .= ' AND orders.type='.$type.'';
                    }
                    if($source_id=Url::iget('source_id')){
                        $cond .= ' AND orders.source_id='.$source_id.'';
                    }
                    $cf_type=false;
                    if($k==XAC_NHAN){//xn
                        $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.user_confirmed='.$value['user_id'];
                        $cf_type = true;
                    }
                    elseif($k==HUY or $k==CHUYEN_HOAN){//thanh cong, chuyển hoàn
                        $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.user_confirmed='.$value['user_id'].' and orders.status_id = '.$k.'';
                        $cf_type = true;
                    }elseif($k==CHUYEN_HANG){//chuyển hàng
                        $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.user_confirmed='.$value['user_id'].' and orders.status_id = '.$k.'';
                        $cf_type = true;
                    }elseif($k==KE_TOAN){// đóng hàng
                        $cond .= ' and orders_extra.accounting_confirmed>="'. $start_time . ' 00:00:00" and  orders_extra.accounting_confirmed<="' . $end_time . ' 23:59:59" and orders.user_confirmed=' . $value['user_id'] ;
                        $cond .= $this->getUpsaleCondition();
                        $total_price = DB::fetch('
								select 
									sum(orders.total_price) as total 
								from 
									orders
									join orders_extra on orders_extra.order_id = orders.id
								where 
									' . $cond . ' 
								group by 
									orders.user_confirmed', 'total');
                        $total_order = DB::fetch('
								select 
									count(orders.id) as total 
								from 
									orders 
									join orders_extra on orders_extra.order_id = orders.id
								where 
									' . $cond . ' 
								group by 
									orders.user_confirmed', 'total');
                    }elseif($k==THANH_CONG){
                        if($date_type==1){//theo ngày chuyển trang
                            $cond .= '
                                and orders_extra.update_successed_time>="'.$start_time.' 00:00:00" and  orders_extra.update_successed_time<="'.$end_time.' 23:59:59"'
                            ;
                        }else{
                            $cond .= '
                                and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59"'
                            ;
                        }
                        $cond .= ' 
                                and orders.user_confirmed='.$value['user_id'].'
                                AND orders_extra.update_successed_time>=orders.confirmed'
                        ;
                        $cond .= $this->getUpsaleCondition();
                        $cond .= ' AND orders.status_id NOT IN (' . implode(',', [CHUYEN_HOAN, TRA_VE_KHO]) . ')';
                        $total_price = DB::fetch('
								select
									sum(orders.total_price) as total
								from
									orders
									join orders_extra on orders_extra.order_id = orders.id
								where
									'.$cond.'
								group by
									orders.user_confirmed','total');
                        $total_order = DB::fetch('
								select
									count(orders.id) as total
								from
									orders
									join orders_extra on orders_extra.order_id = orders.id
								where
									'.$cond.'
								group by
									orders.user_confirmed','total');

                    }elseif($k==DA_THU_TIEN){
                        if($date_type==1){//theo ngày chuyển trang thái
                            $cond .= ' and orders_extra.update_paid_time>="'.$start_time.' 00:00:00" and orders_extra.update_paid_time<="'.$end_time.' 23:59:59" and orders.user_confirmed='.$value['user_id'].'';
                        }else{
                            $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders_extra.update_paid_time>="'.$start_time.' 00:00:00" and orders.user_confirmed='.$value['user_id'].'';
                        }
                        $cond .= $this->getUpsaleCondition();
                        $cond .= ' AND orders.status_id IN (' . implode(',', [DA_THU_TIEN, CHUYEN_HANG]) . ')';
                        $total_price = DB::fetch('
								select 
									sum(orders.total_price) as total 
								from 
									orders
									join orders_extra on orders_extra.order_id = orders.id
								where 
									'.$cond.'
								group by
									orders.user_confirmed','total');
                        $total_order = DB::fetch('
								select 
									count(orders.id) as total 
								from 
									orders
									join orders_extra on orders_extra.order_id = orders.id
								where 
									'.$cond.' 
								group by 
									orders.user_confirmed','total');
                    }else{
                        $cond .= ' and orders.created>="'.$start_time.'" and  orders.created<="'.$end_time.' 23:59:59" and orders.user_assigned='.$value['user_id'].' and orders.status_id='.$k.'';
                        $total_price = DB::fetch('select sum(total_price) as total from orders where '.$cond.' group by user_assigned','total');
                        $total_order = DB::fetch('select count(*) as total from orders where '.$cond.' group by user_assigned','total');
                    }
                    if($cf_type){
                        $amount_sql = 'select sum(total_price) as total from orders where '.$cond.' group by user_confirmed';
                        $total_sql = 'select count(*) as total from orders where '.$cond.' group by user_confirmed';
                        if($this->reqUpSale){
                            $cond .= $this->getUpsaleCondition();
                            $amount_sql = '
								select 
									sum(orders.total_price) as total 
								from 
									orders
									join orders_extra on orders_extra.order_id = orders.id
								where 
									'.$cond.' 
								group by 
									user_confirmed';
                            $total_sql = '
								select 
									count(orders.id) as total 
								from 
									orders 
									join orders_extra on orders_extra.order_id = orders.id
								where 
									'.$cond.' 
								group by 
									user_confirmed';
                        }
                        $total_price = DB::fetch($amount_sql,'total');
                        $total_order = DB::fetch($total_sql,'total');
                        if($k==HUY){
                            $total_price_cancel += $total_price;
                        }
                        if($k==XAC_NHAN){
                            $total_price_confirm += $total_price;
                        }
                    }
                    $reports[$key][$k] = array(1=>array('total_price'=>System::display_number($total_price),'qty'=>System::display_number($total_order),'name'=>$v['name']));
                    if($k==XAC_NHAN or $k==HUY){
                        $total_total_price += $total_price;
                        $total_qty += $total_order;
                    }
                    $total_ += $total_price;
                    $status[$k]['total'] += $total_price;
                    $status[$k]['qty'] += $total_order;
                }
                $cancel_rate = $total_price_confirm?(round($total_price_cancel/$total_price_confirm,2)*100):0;
                $reports[$key]['cancel_rate'] = $cancel_rate;
                $status[1000000000]['total'] += $total_total_price;
                $status[1000000000]['qty'] += $total_qty;
                $reports[$key][1000000000] = array(1=>array('total_price'=>System::display_number($total_total_price),'qty'=>System::display_number($total_qty),'name'=>'Tổng'));
                //$status[1000000000]
                $reports[$key]['total'] = floatval($total_total_price);
            }
        }
        ///////////////////////////////////////////
        if(sizeof($reports)>2){
            System::kksort($reports, 'total');
            //System::sksort($reports, 'total','DESC');
        }
        $this->map['reports'] = $reports;
        $this->map['status'] = $status;
        $months = array();
        for($i=1;$i<=12;$i++){
            $months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
        }
        $this->map['month_list'] = $months;
        $this->map['year_list'] = array(2018=>2018,2019=>2019,2020=>2020);
        $this->map['type_list'] = Dashboard::$type;
        $this->map['is_active_list'] = array(''=>'Tài khoản kích hoạt',1=>'Tài khoản chưa kích hoạt',2=>'Tất cả');
        $account_groups = get_account_groups();
        $this->map['account_group_id_list'] = array(''=>'Nhóm tài khoản') + MiString::get_list($account_groups);
        $this->map += DashboardDB::get_report_info();
        $this->map['group_id_list'] = array('' => 'Chọn công ty','0'=>'Tất cả') + MiString::get_list(DashboardDB::get_groups());
        $this->map['source_id_list'] = array('' => 'Đơn từ nguồn','0'=>'Tất cả') + MiString::get_list(DashboardDB::get_source());
        $this->map['date_type_list'] = Dashboard::$date_type;
        $this->map['code_list'] = ['GANDON'=>'Nhân viên Kinh doanh','CSKH'=>'Chăm sóc khách hàng'];
        $this->parse_layout('doanh_thu_nv',$this->map);
    }

    /**
     * { function_description }
     */
    private function getUpsaleName()
    {   
        if(count($this->reqUpSale) === 1){
            $sql = sprintf(
                'select name from users where group_id = %d AND id = %d',
                Dashboard::$group_id,
                $this->reqUpSale[0]
            );

            $upsaleName = DB::fetch($sql, 'name');

            return $upsaleName ? '<div>Upsale: ' . $upsaleName . '</div>' : '';
        }
    }

    /**
     * { function_description }
     *
     * @param      array  $userID  The user id
     */
    private function filterUser(array $userIDs)
    {   
        $userIDs = Arr::of($userIDs);

        if($userIDs->exists(self::ALL_UPSALE_USER_ID)){
            return array_keys($this->allUsers);
        }

        return $userIDs->filter(function($userID){
            return isset($this->allUsers[$userID]);
        })
        ->toArray();
    }

    /**
     * Gets the upsale condition.
     *
     * @return     string  The upsale condition.
     */
    private function getUpsaleCondition()
    {
        if ($this->reqUpSale) {
            return ' AND orders_extra.upsale_from_user_id IN ('. implode(',', $this->reqUpSale) . ')';
        }
    }
}
?>
