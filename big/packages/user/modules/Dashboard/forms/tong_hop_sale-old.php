<?php
class ReportForm extends Form
{
    function __construct()
    {
        Form::Form('ReportForm');
    }
    function draw()
    {
        $zoma_modal = false;
        $account_type = Session::get('account_type');
        if(Session::get('master_group_id') or $account_type==TONG_CONG_TY){
            $zoma_modal = true;
        }
        $this->map = array();
        $date_type = DB::escape(Url::get('date_type'));
        $this->map['total'] = 0;
        //////////////////////////////////////////////////
        if(!Url::get('date_from')){
            $_REQUEST['date_from'] = date('d/m/Y');
        }
        if(!Url::get('date_to')){
            $month = date('m',Date_Time::to_time($_REQUEST['date_from']));
            $year = date('Y',Date_Time::to_time($_REQUEST['date_from']));
            $_REQUEST['date_to'] = date('d/m/Y');//date(''.Date_Time::get_last_day_of_month($month,$year)
        }
        $dates = array();
        $group_id = Session::get('group_id');
        $account_type = Session::get('account_type');
        $master_group_id = Session::get('$master_group_id');
        $start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
        $end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));
        if(strtotime($end_time) - strtotime($start_time) > 31*24*3600){
            die('<div class="alert alert-warning">Bạn vui lòng chọn tối đa 1 tháng!</div>');
        }
        $status = DashboardDB::get_statuses(false);//,'AND statuses_custom.level = 0'
        $users = DashboardDB::get_users('GANDON', Url::get('is_active'));

        $mkt_users = DashboardDB::get_users('MARKETING',Url::get('is_active'),true);
        $this->map['upsale_from_user_id_list'] = [''=>'Nguồn Up Sale (Tất cả)'] + MiString::get_list($mkt_users,'full_name');
        $reports = array();
        if(Url::get('view_report')){
            $reports['label']['id'] = 'label';
            $reports['label']['name'] = 'Nhân viên';
            $reports['label']['total_assigned'] = 'Số được chia';
            $reports['label']['total_reached'] = 'Tiếp cận';
            $reports['label']['name'] = 'Nhân viên';
            $reports['label']['sl'] = 'SL';
  
            foreach($users as $key=>$value){
                foreach($status as $k=>$v){
                    $reports['label'][$k][1] = [
                        'total_price'=>'Doanh thu',
                        'qty'=>$v['name'],
                        'name'=>$v['name'],
                        'total_delivered'=>'Số được chia',
                        'total_reached'=>'Số tiếp cận'
                    ];
                }
            }
            $reports['label']['total'] = 1000000000000000000000000;
            // 7: đã xác nhận,9: hủy, 6: chuyển hoàn, 5 thành công
            $no_revenue_status = DashboardDB::get_no_revenue_status(); // các trạng thái không doanh thu
            $not_reach_status = DashboardDB::get_no_reached_status(); // các trạng thái ko tiếp cận
            foreach($users as $key=>$value){
                $reports[$key]['id'] = $key;
                //$reports[$key]['name'] = $value['full_name'].'<div class="small" style="color:#999;">'.$value['id'].'</div>';
                $reports[$key]['name'] = $value['full_name'].' '.($value['rated_point']>0?'<span class="small text-warning">'.round($value['rated_point'],2).'<i class="fa fa-star"></i>('.$value['rated_quantity'].')</span>':'').' <div class="small" style="color:#999;font-style: italic;"> '.$value['id'].' </div>';
                $user_id = $value['user_id'];
                $total_ = 0;
                $total_total_price = 0;
                $total_qty = 0;
                $total_order = 0;
                $total_price = 0;
                $total_ = 0;
                $total_total_price = 0;
                $total_qty = 0;
                $total_price_cancel = 0;
                $total_price_confirm = 0;
                foreach($status as $k=>$v){
                    if($account_type == TONG_CONG_TY){ // tổng công ty
                        if(Url::iget('group_id')){
                            $cond = '(orders.group_id='.Url::iget('group_id').' and orders.master_group_id='.$group_id.')';
                        }else{
                            die('Bạn vui lòng chọn công ty để xem báo cáo!');
                        }
                    }else{
                        if($master_group_id){
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
                        $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.user_confirmed='.$value['user_id'].' and orders.status_id = '.$k;
                        $cf_type = true;
                    }
                    elseif($k==HUY or $k==CHUYEN_HOAN){//thanh cong, chuyển hoàn
                        $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.user_confirmed='.$value['user_id'].' and orders.status_id = '.$k;
                        $cf_type = true;
                    }elseif($k==CHUYEN_HANG){//chuyển hàng
                        $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.user_confirmed='.$value['user_id'].' and orders.status_id = '.$k;
                        $cf_type = true;
                    }elseif($k==KE_TOAN){// đóng hàng
                        $cond .= ' and orders_extra.accounting_confirmed>="'. $start_time . ' 00:00:00" and  orders_extra.accounting_confirmed<="' . $end_time . ' 23:59:59" and orders.user_confirmed=' . $value['user_id'] ;
                        if ($upsale_from_user_id = Url::get('upsale_from_user_id')) {
                            $cond .= ' AND orders_extra.upsale_from_user_id='. DB::escape($upsale_from_user_id);
                        }
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
                        if($upsale_from_user_id=Url::get('upsale_from_user_id')){
                            $cond .= ' AND orders_extra.upsale_from_user_id='.DB::escape($upsale_from_user_id);
                        }
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
                        if($upsale_from_user_id=Url::get('upsale_from_user_id')){
                            $cond .= ' AND orders_extra.upsale_from_user_id='.DB::escape($upsale_from_user_id);
                        }
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
                        $cond .= ' and orders.created>="'.$start_time.'" and  orders.created<="'.$end_time.' 23:59:59" and orders.user_confirmed='.$value['user_id'].' and orders.status_id='.$k.'';
                        $total_price = DB::fetch('select sum(total_price) as total from orders where '.$cond.' group by user_confirmed','total');
                        $total_order = DB::fetch('select count(*) as total from orders where '.$cond.' group by user_confirmed','total');
                    }
                    if($cf_type){
                        $amount_sql = 'select sum(total_price) as total from orders where '.$cond.' group by user_confirmed';
                        $total_sql = 'select count(*) as total from orders where '.$cond.' group by user_confirmed';
                        if($upsale_from_user_id=Url::get('upsale_from_user_id')){
                            $cond .= ' AND orders_extra.upsale_from_user_id='.DB::escape($upsale_from_user_id);
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
                    $reports[$key][$k] = array(
                        1 =>
                            [
                                'total_price' => System::display_number($total_price),
                                'qty' => System::display_number($total_order),
                                'name' => $v['name']
                            ]
                    );
                    if($k==XAC_NHAN or $k==HUY){
                        $total_total_price += $total_price;
                        $total_qty += $total_order;
                    }
                    $total_ += $total_price;
                    $status[$k]['total'] += $total_price;
                    $status[$k]['qty'] += $total_order;
                }

                $cond_common = [];
                if ($master_group_id) {
                    $cond_common[] = '(orders.master_group_id='.$master_group_id.')';
                } else {
                    $cond_common[] = '(orders.group_id='.$group_id.' '.($account_type?' or orders.master_group_id='.$group_id.'':'').')';
                }

                $cond_assigned = $cond_common;
                $cond_approach = $cond_common;
                $cond_sale = $cond_common;

                $cond_cancel= $cond_common;
                $cond_cancel[] = "AND orders.user_confirmed = $user_id";
                $cond_cancel[] = "AND DATE(orders.confirmed) >= '$start_time' AND DATE(orders.confirmed) <= '$end_time'";

                $cond_refund = $cond_common;
                $cond_refund[] = "AND orders.user_confirmed = $user_id";
                $cond_refund[] = "AND DATE(orders.confirmed) >= '$start_time' AND DATE(orders.confirmed) <= '$end_time'";

                $cond_assigned[] = "AND orders.user_assigned = $user_id";
                $cond_approach[] = "AND orders.user_assigned = $user_id";
                $cond_assigned[] = "AND DATE(orders.assigned) >= '$start_time' AND DATE(orders.assigned) <= '$end_time'";
                $cond_approach[] = "AND DATE(orders.assigned) >= '$start_time' AND DATE(orders.assigned) <= '$end_time'";
                $cond_sale[] = "AND DATE(orders.confirmed) >= '$start_time' AND DATE(orders.confirmed) <= '$end_time'";

                // Lấy ra tổng số đơn được chia theo user
                $total_order_assigned_by_user = DashboardDB::getTotalOrderByCond($cond_assigned);
                $reports[$key]['total_assigned'] = $total_order_assigned_by_user;

                // Lấy ra tổng số đơn tiếp cận theo user
                if ($not_reach_status) {
                    $cond_approach[] = "AND orders.status_id NOT IN ($not_reach_status)";
                }

                $cond_approach[] = "AND orders.status_id <> ". CHUA_XAC_NHAN;
                $reports[$key]['total_reached'] = DashboardDB::getTotalOrderByCond($cond_approach);

                // Lấy ra tổng số đơn chốt mới
                $cond_sale[] = "AND orders.user_confirmed = $user_id AND orders.status_id NOT IN ($no_revenue_status, ". CHUYEN_HOAN .", ". HUY .")";
                $cond_transport = $cond_care = $cond_toi_uu = $cond_reset = $cond_sale;

                $cond_sale[] = "AND orders.type = 1";
                $reports[$key]['total_sale'] = DashboardDB::getTotalOrderByCond($cond_sale);
                $reports[$key]['total_revenue_sale'] = DashboardDB::getTotalRevenueOrderByCond($cond_sale); // Tổng doanh thu

                // Lấy ra tổng số đơn chăm sóc
                $cond_care[] = "AND orders.type = 2";
                $reports[$key]['total_care'] = DashboardDB::getTotalOrderByCond($cond_care);
                $reports[$key]['total_revenue_care'] = DashboardDB::getTotalRevenueOrderByCond($cond_care); // Tổng doanh thu

                // Lấy ra tổng số đơn đặt lại
                $cond_reset[] = "AND orders.type IN (3, 4, 5, 6, 7, 8)";
                $reports[$key]['total_reset'] = DashboardDB::getTotalOrderByCond($cond_reset);
                $reports[$key]['total_revenue_reset'] = DashboardDB::getTotalRevenueOrderByCond($cond_reset); // Tổng doanh thu

                // Lấy ra tổng số đơn tối ưu
                $cond_toi_uu[] = "AND orders.type = 9";
                $reports[$key]['total_toi_uu'] = DashboardDB::getTotalOrderByCond($cond_toi_uu);
                $reports[$key]['total_revenue_toi_uu'] = DashboardDB::getTotalRevenueOrderByCond($cond_toi_uu); // Tổng doanh thu

                // Lấy ra tổng doanh thu đơn hủy
                $cond_cancel[] = "AND orders.status_id = " . HUY;
                $reports[$key]['total_cancel'] = DashboardDB::getTotalOrderByCond($cond_cancel);
                $reports[$key]['total_revenue_cancel'] = DashboardDB::getTotalRevenueOrderByCond($cond_cancel);


                // Lấy ra tổng doanh thu đơn chuyển hàng
                $cond_transport[] = "AND orders.status_id = " . CHUYEN_HANG;
                $reports[$key]['total_transport'] = DashboardDB::getTotalOrderByCond($cond_transport);
                $reports[$key]['total_revenue_transport'] = DashboardDB::getTotalRevenueOrderByCond($cond_transport);

                // Lấy ra tổng doanh thu đơn chuyển hoàn
                $cond_refund[] = "AND orders.status_id = " . CHUYEN_HOAN;
                $reports[$key]['total_refund'] = DashboardDB::getTotalOrderByCond($cond_refund);
                $reports[$key]['total_revenue_refund'] = DashboardDB::getTotalRevenueOrderByCond($cond_refund);

                /*$status[1000000000]['total'] += $total_total_price;
                $status[1000000000]['qty'] += 0;
                $reports[$key][1000000000] = array(1=>array('total_price'=>System::display_number($total_total_price),'qty'=>System::display_number($total_qty),'name'=>'Tổng'));*/
                //$status[1000000000]
                $reports[$key]['total'] = $total_;
                //
                /*if($master_group_id){
                    $cond = '(orders.master_group_id='.$master_group_id.')';
                }else{
                    $cond = '(orders.group_id='.$group_id.' '.($account_type?' or orders.master_group_id='.$group_id.'':'').')';
                }
                $cond1 = $cond.' and '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'>="'.$start_time.' 00:00:00" and  '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'<="'.$end_time.' 23:59:59" and orders.user_assigned='.$value['user_id'].'';
                $total_delivered = DB::fetch('select count(id) as total from orders where '.$cond1.' group by user_assigned','total');
                $reports[$key]['total_delivered'] = $total_delivered;
                ///
                if($not_reach_status){
                    $cond .= ' and orders.status_id NOT IN ('.$not_reach_status.')';
                }
                $cond1 = $cond.' and orders.status_id<> '.CHUA_XAC_NHAN.' and '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'>="'.$start_time.' 00:00:00" and  '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'<="'.$end_time.' 23:59:59" and orders.user_assigned='.$value['user_id'].'';
                $total_reached = DB::fetch('select count(id) as total from orders where '.$cond1.' group by user_assigned','total');
                $reports[$key]['total_reached'] = $total_reached; // tiếp cận*/
                //
            }
        }
        ///////////////////////////////////////////
        //System::Debug($reports);die;
        if(sizeof($reports)>2){
            //$this->sort_array_of_array();
            System::sksort($reports, 'total','DESC');

        }
        $this->map['reports'] = $reports;
        // System::debug($reports);
        $this->map['status'] = $status;
        // System::debug($status);
        $months = array();
        for($i=1;$i<=12;$i++){
            $months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
        }
        $this->map['month_list'] = $months;
        $this->map['year_list'] = array(2018=>2018,2019=>2019);
        $this->map['type_list'] = array(''=>'Tất cả đơn',1=>'SALE',2=>'CSKH','3'=>'Đặt lại',9=>'Tối ưu');
        $this->map['is_active_list'] = array(''=>'Tài khoản kích hoạt',1=>'Tài khoản chưa kích hoạt',2=>'Tất cả',3=>'Tất cả 1');
        $this->map['account_group_id_list'] = array(''=>'Xem theo nhóm tài khoản',''=>'Tất cả các nhóm tài khoản') + MiString::get_list(DashboardDB::get_account_groups());
        $this->map += DashboardDB::get_report_info();
        $this->map['group_id_list'] = array('' => 'Chọn công ty') + MiString::get_list(DashboardDB::get_groups());
        $this->parse_layout('tong_hop_sale',$this->map);
    }
}
?>
