<?php
class ReportCharForm extends Form
{
    protected $map;
    protected $refresh;
    function __construct()
    {
        Form::Form('ReportForm');
        Portal::$extra_header .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $this->link_css('assets/lib/DataTables/datatables.min.css');
        $this->link_js('assets/lib/DataTables/datatables.min.js');
        $this->refresh = (Url::get('refresh') or System::is_local())?true:false;
    }
    function draw(){
        if(Dashboard::$group_id!=1279){
            //die('<div class="alert alert-warning">Tính năng đang trong quá trình bảo trì. Qúy khách vui lòng quay lại tính năng này sau. Xin lỗi quý khách vì sự bất tiện!</div>');
        }
        $this->map = array();
        $current_user_id = Dashboard::$user_id;
        $group_id = Dashboard::$group_id;
        $account_type = Dashboard::$account_type;
        $this->map['admin_group'] = Dashboard::$admin_group?true:false;
        $this->map['total'] = 0;
        //////////////////////////////////////////////////
        if(!Url::get('date_from')){
            $_REQUEST['date_from'] = date('01/m/Y');
        }
        if(!Url::get('date_to')){
            $_REQUEST['date_to'] = date('d/m/Y');
        }

        $start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
        $end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));

        if(strtotime($end_time) - strtotime($start_time) > 31*24*3600){
            die('<div class="alert alert-warning">Bạn vui lòng chọn tối đa 1 tháng!</div>');
        }

        $status = DashboardDB::get_report_statuses();
        $no_revenue_status = DashboardDB::get_no_revenue_status();
        $reports = array();
        $this->map['reports'] = $reports;
        ///// bxh cho mkt
        $reports = array();
        $this->map['mkt_reports'] = $reports;
        ///
        if($account_type == TONG_CONG_TY){
            $cond = 'orders.group_id='.$group_id.'';
            if(!Dashboard::$admin_group && !Dashboard::$xem_khoi_bc_chung){
                $cond .= ' AND orders.user_confirmed = '.get_user_id();
            }
            $cond .= ' and orders.status_id NOT IN ('.$no_revenue_status.')';
            $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59"';
            $doanh_so_xuat_di = DashboardDB::get_total_amount('select (total_price) as total from orders where '.$cond);
            $doanh_so_tru_hoan = DashboardDB::get_total_amount('select (total_price) as total from orders where '.$cond.' and orders.status_id<>'.CHUYEN_HOAN);
            $tong_don_van_chuyen = DashboardDB::get_total_item('select (id) as total from orders where '.$cond.' and orders.status_id='.CHUYEN_HANG);
            ///
            $cond = 'orders.master_group_id='.$group_id.'';
            if(!Dashboard::$admin_group && !Dashboard::$xem_khoi_bc_chung){
                $cond .= ' AND orders.user_confirmed = '.get_user_id();
            }
            $cond .= ' and orders.status_id NOT IN ('.$no_revenue_status.')';
            $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59"';

            ///
            $m_key = md5('doanh_so_xuat_di_'.$cond);
            if($m_key and !System::is_local()){
                if($this->refresh){
                    MC::delete_item($m_key);
                }
                $doanh_so_xuat_di_  = MC::get_items($m_key);
                if(!$doanh_so_xuat_di_){
                    $doanh_so_xuat_di_ = DashboardDB::get_total_amount('select (total_price) as total from orders where '.$cond);

                    if($m_key){
                        MC::set_items($m_key, $doanh_so_xuat_di_,time() + 600);
                    }
                }
                $doanh_so_xuat_di += $doanh_so_xuat_di_;
            }else{
                $doanh_so_xuat_di += DashboardDB::get_total_amount('select (total_price) as total from orders where '.$cond);
            }
            ///
            $m_key = md5('doanh_so_tru_hoan_'.$cond);
            if($m_key and !System::is_local()){
                if($this->refresh){
                    MC::delete_item($m_key);
                }
                $doanh_so_tru_hoan_  = MC::get_items($m_key);
                if(!$doanh_so_tru_hoan_){
                    $doanh_so_tru_hoan_ = DashboardDB::get_total_amount('select (total_price) as total from orders where '.$cond.' and orders.status_id<>'.CHUYEN_HOAN);

                    if($m_key){
                        MC::set_items($m_key, $doanh_so_tru_hoan_,time() + 600);
                    }
                }
                $doanh_so_tru_hoan += $doanh_so_tru_hoan_;
            }else{
                $doanh_so_tru_hoan += DashboardDB::get_total_amount('select (total_price) as total from orders where '.$cond.' and orders.status_id<>'.CHUYEN_HOAN);
            }
            $m_key = md5('tong_don_van_chuyen_'.$cond);
            if($m_key and !System::is_local()){
                $tong_don_van_chuyen_  = MC::get_items($m_key);
                if(!$tong_don_van_chuyen_){
                    $tong_don_van_chuyen_ = DashboardDB::get_total_item('select (id) as total from orders where '.$cond.' and orders.status_id='.CHUYEN_HANG);

                    if($m_key){
                        MC::set_items($m_key, $tong_don_van_chuyen_,time() + 600);
                    }
                }
                $tong_don_van_chuyen += $tong_don_van_chuyen_;
            }else{
                $tong_don_van_chuyen += DashboardDB::get_total_item('select (id) as total from orders where '.$cond.' and orders.status_id='.CHUYEN_HANG);
            }
        }else{
            $cond = '(orders.group_id='.$group_id.')';
            if(!Dashboard::$admin_group && !Dashboard::$xem_khoi_bc_chung){
                $cond .= ' AND orders.user_confirmed = '.get_user_id();
            }
            $cond .= ' and orders.status_id NOT IN ('.$no_revenue_status.')';
            $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59"';

            $doanh_so_xuat_di = 0;
            $doanh_so_tru_hoan = 0;
            $tong_don_van_chuyen = 0;
            ////
            $m_key = md5('doanh_so_xuat_di_'.$cond);
            if($m_key and !System::is_local()){
                if($this->refresh){
                    MC::delete_item($m_key);
                }
                $doanh_so_xuat_di_  = MC::get_items($m_key);
                if(!$doanh_so_xuat_di_){
                    $doanh_so_xuat_di_ = DashboardDB::get_total_amount('select (total_price) as total from orders where '.$cond);

                    if($m_key){
                        MC::set_items($m_key, $doanh_so_xuat_di_,time() + 60*10);
                    }
                }
                $doanh_so_xuat_di += $doanh_so_xuat_di_;
            }else{
                $doanh_so_xuat_di += DashboardDB::get_total_amount('select (total_price) as total from orders where '.$cond);
            }
            ///
            $m_key = md5('doanh_so_tru_hoan_'.$cond);
            if($m_key and !System::is_local()){
                if($this->refresh){
                    MC::delete_item($m_key);
                }
                $doanh_so_tru_hoan_  = MC::get_items($m_key);
                if(!$doanh_so_tru_hoan_){
                    $doanh_so_tru_hoan_ = DashboardDB::get_total_amount('select (total_price) as total from orders where '.$cond.' and orders.status_id<>'.CHUYEN_HOAN);

                    if($m_key){
                        MC::set_items($m_key, $doanh_so_tru_hoan_,time() + 60*10);
                    }
                }
                $doanh_so_tru_hoan += $doanh_so_tru_hoan_;
            }else{
                $doanh_so_tru_hoan += DashboardDB::get_total_amount('select (total_price) as total from orders where '.$cond.' and orders.status_id<>'.CHUYEN_HOAN);
            }
            $m_key = md5('tong_don_van_chuyen_'.$cond);
            if($m_key and !System::is_local()){
                if($this->refresh){
                    MC::delete_item($m_key);
                }
                $tong_don_van_chuyen_  = MC::get_items($m_key);
                if(!$tong_don_van_chuyen_){
                    $tong_don_van_chuyen_ = DashboardDB::get_total_item('select (id) as total from orders where '.$cond.' and orders.status_id='.CHUYEN_HANG);

                    if($m_key){
                        MC::set_items($m_key, $tong_don_van_chuyen_,time() + 60*10);
                    }
                }
                $tong_don_van_chuyen += $tong_don_van_chuyen_;
            }else{
                $tong_don_van_chuyen += DashboardDB::get_total_item('select (id) as total from orders where '.$cond.' and orders.status_id='.CHUYEN_HANG);
            }
            ////
        }
        $this->map['doanh_so_xuat_di'] = System::display_number($doanh_so_xuat_di/1000000);
        $this->map['doanh_so_tru_hoan'] = System::display_number($doanh_so_tru_hoan/1000000);
        $this->map['tong_don_van_chuyen'] = System::display_number($tong_don_van_chuyen);
        /////sản phẩm hàng hóa/////
        if(Dashboard::$admin_group or Dashboard::$xem_khoi_bc_chung){
            $cond = ' (orders.group_id='.$group_id.''.($account_type?' or orders.master_group_id='.$group_id.'':'').') and orders.confirmed>="'.$start_time.' 00:00:00 " and  orders.confirmed<="'.$end_time.' 23:59:59"  and orders.status_id<>9 ';
        }
        else{
            $cond = ' (orders.group_id='.$group_id.''.($account_type?' or orders.master_group_id='.$group_id.'':'').') and orders.assigned>="'.$start_time.' 00:00:00 " and  orders.assigned<="'.$end_time.' 23:59:59"  and orders.status_id<>9 and user_confirmed='.$current_user_id.'';
        }

        $sql = '
            select
                orders_products.id,orders_products.order_id,
                orders_products.product_id,
                orders_products.product_price,
                sum(orders_products.qty*orders_products.product_price) as total_price,
                sum(orders_products.qty) as qty,
                products.name,products.color,products.size,products.code,
                orders.status_id,
                units.name as unit_name,
                (select sum(o.discount_price/o.total_qty) from orders as o where o.id=orders.id) as total_discount
            from
                orders_products
              
                INNER JOIN products ON products.id = orders_products.product_id
                LEFT OUTER JOIN units ON units.id = products.unit_id
                INNER JOIN orders ON orders.id = orders_products.order_id
            WHERE
                '.$cond.'
            GROUP BY 
                products.id
        ';
        // memcached
        $m_key = md5('dashboard_products_'.$cond);
        if($m_key and !System::is_local()){
            if($this->refresh){
                MC::delete_item($m_key);
            }
            $get_mc  = MC::get_items($m_key);
            if($get_mc){
                $products = $get_mc;
            }else{
                $products = DB::fetch_all($sql);
                if($m_key){
                    MC::set_items($m_key, $products,time() + 60*10);
                }
            }
        }else{
            $products = DB::fetch_all($sql);
        }
        //
        foreach($products as $key=>$value){
            $products[$key]['total_price'] = round($value['total_price']/1000000,2);
        }
        $this->map['products'] = $products;
        ////bieu do cua tai khoan dang login
        $user =  DB::fetch('
            SELECT
                account.id,party.full_name,account.group_id,users.id as user_id
            FROM
                account
                INNER JOIN party ON party.user_id = account.id
                INNER JOIN users ON users.username = account.id
            WHERE
                account.id = "'.Session::get('user_id').'" 
        ');
        $user_id = $user['user_id'];
        $chart_per = array();
        $chart_tong_hop = [
            'xac_nhan' => [
                'type'=> 'area',
                'name' => 'Xác nhận',
                'color' => 'rgb(91, 192, 222)'
            ],
        ];

        $chart_tong_hop_date = [];
        {
            if(strtotime($end_time) - strtotime($start_time)>=0){
                for($i=strtotime($start_time);$i<=strtotime($end_time);$i=$i+24*3600){
                    $end_time_1 = date('Y-m-d', $i);
                    if(Dashboard::$quyen_marketing or Dashboard::$xem_khoi_bc_chung){
                        $cond_confirm = ' orders.confirmed>="'.$end_time_1.' 00:00:00" and  orders.confirmed<="'.$end_time_1.' 23:59:59" and orders.user_created='.$user_id.' and orders.status_id <> 9';
                        $cond_cancel = ' orders.created>="'.$end_time_1.' 00:00:00" and  orders.created<="'.$end_time_1.' 23:59:59" and orders.user_created="'.$user_id.'" and orders.status_id=9';
                    }else{
                        $cond_confirm = ' orders.confirmed>="'.$end_time_1.' 00:00:00" and  orders.confirmed<="'.$end_time_1.' 23:59:59" and orders.user_confirmed='.$user_id.' and orders.status_id <> 9';
                        $cond_cancel = ' orders.created>="'.$end_time_1.' 00:00:00" and  orders.created<="'.$end_time_1.' 23:59:59" and orders.user_assigned="'.$user_id.'" and orders.status_id=9';
                    }

                    $doanh_so_huy = DashboardDB::get_total_amount('select total_price as total from orders where '.$cond_cancel);
                    $doanh_so_xn = DashboardDB::get_total_amount('select total_price as total from orders where '.$cond_confirm);
                    $doanh_thu = ($doanh_so_xn + $doanh_so_huy)/1000000;
                    if(!Dashboard::$admin_group && !Dashboard::$xem_khoi_bc_chung){
                        $chart_per[$i]['turnover'] = $doanh_thu;
                        $chart_per[$i]['qty'] = DashboardDB::get_total_item('select orders.id from orders where '.$cond_confirm) + DashboardDB::get_total_item('select count(*) as total from orders where '.$cond_cancel);
                        $chart_per[$i]['date'] =  date('d-m', strtotime($end_time_1));
                    }
                    $chart_tong_hop_date[] = date('d-m', strtotime($end_time_1));


                    $cond_xac_nhan = 'orders.group_id=' . $group_id. '';
                    $cond_xac_nhan .= ' and orders.status_id NOT IN (' . $no_revenue_status . ')';
                    $cond_xac_nhan .= ' and orders.confirmed>="' . $end_time_1 . ' 00:00:00" and  orders.confirmed<="' . $end_time_1 . ' 23:59:59"';
                    $total_price_xac_nhan = DashboardDB::get_total_amount('select total_price as total from orders where ' . $cond_xac_nhan);
                    DB::check_query('113.160.234.156','select total_price as total from orders where ' . $cond_xac_nhan);
                    $chart_tong_hop['xac_nhan']['data'][] = (float) $total_price_xac_nhan;
                }
            }
        }
        $this->map['revenue_by_month'] = [];
        if(Dashboard::$admin_group or Dashboard::$xem_khoi_bc_chung){
            $this->map['revenue_by_month'] = DashboardDB::get_revenue_by_month($group_id);
        }
        $this->map['chart_tong_hop'] = json_encode(array_values($chart_tong_hop), JSON_UNESCAPED_UNICODE);
        $this->map['chart_tong_hop_date'] = json_encode($chart_tong_hop_date);

        $this->map['chart_per'] = $chart_per;
        $this->map['date_from'] = date('d/m/Y',Date_Time::to_time($_REQUEST['date_from']));
        $this->map['date_to'] = date('d/m/Y',Date_Time::to_time($_REQUEST['date_to']));
        $this->map['status'] = $status;
        $months = array();
        for($i=1;$i<=12;$i++){
            $months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
        }
        $this->map['month_list'] = $months;
        $this->map['year_list'] = array(2016=>2016,2017=>2017);
        $this->map += DashboardDB::get_report_info();
        //statistics
        $cond = 'group_id='.$group_id.' and type="1" ';
        $this->map['tt_cmt'] = 0;
        $cond = 'group_id='.$group_id.' and type="0" ';
        $this->map['tt_inbox']= 0;

        if(Dashboard::$admin_group or Dashboard::$xem_khoi_bc_chung){
            $cond = ' group_id='.$group_id.' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59"  and orders.status_id <> 9';
        }
        else{
            $cond = ' group_id='.$group_id.' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59"  and orders.status_id <> 9 and user_confirmed="'.$user['user_id'].'"';
        }
        $don_xac_nhan = $this->map['don_xac_nhan'] = DashboardDB::get_total_item('select id as total from orders where '.$cond);
        //$doanh_so_xuat_di = DB::fetch('select sum(total_price) as total from orders where '.$cond.' ','total');
        //$this->map['tt_ds'] = System::display_number($doanh_so_xuat_di);
        //ds huy
        if(Dashboard::$admin_group or Dashboard::$xem_khoi_bc_chung){
            $cond = ' group_id='.$group_id.' and orders.assigned>="'.$start_time.' 00:00:00 " and  orders.assigned<="'.$end_time.' 23:59:59"  and orders.status_id<>9 ';
        }
        else{
            $cond = ' group_id='.$group_id.' and orders.assigned>="'.$start_time.' 00:00:00 " and  orders.assigned<="'.$end_time.' 23:59:59"  and orders.status_id<>9 and user_assigned="'.$user['user_id'].'"';
        }

        //$tt_cancel = DB::fetch('select sum(total_price) as total from orders where '.$cond.'','total');
        $tong_so_duoc_chia = DashboardDB::get_total_item('select (id) as total from orders where '.$cond);
        $this->map['ty_le_chot']='0%';
        $this->map['tong_so_duoc_chia'] = System::display_number($tong_so_duoc_chia);
        if($tong_so_duoc_chia>0){
            $this->map['ty_le_chot']=number_format((float)($don_xac_nhan*100)/($tong_so_duoc_chia), 2, '.', '').'%';// xu ly chia cho 0
        }
        if(Dashboard::$admin_group or Dashboard::$xem_khoi_bc_chung){
            $cond = ' group_id='.$group_id.' and orders.created>="'.$start_time.' 00:00:00" and  orders.created<="'.$end_time.' 23:59:59"  and orders.status_id =10';
        }
        else{
            $cond = ' group_id='.$group_id.' and orders.created>="'.$start_time.' 00:00:00" and  orders.created<="'.$end_time.' 23:59:59"  and orders.status_id =10 and user_assigned="'.$user['user_id'].'"';
        }

        $don_chua_xu_ly = DashboardDB::get_total_item('select (id) as total from orders where '.$cond);
        if($don_chua_xu_ly==''){
            $this->map['don_chua_xu_ly'] = 0;
        }
        else{
            $this->map['don_chua_xu_ly'] = $don_chua_xu_ly;
        }

        if(!Dashboard::$admin_group && !Dashboard::$xem_khoi_bc_chung){
            $this->map['name_user'] = $user['full_name'];
        }
        $this->map['tong_nhan_su'] = DashboardDB::get_total_item('select (id) as total from account where group_id = '.$group_id.' and is_active=1');
        $this->map['tong_admin'] = DashboardDB::get_total_item('select (id) as total from account where group_id = '.$group_id.' and is_active=1 and admin_group=1');
        /*if($_SERVER['REMOTE_ADDR']=='118.70.131.194'){
            $this->map['logs'] = [];
            $this->map['logins'] = [];
            $this->map['notes'] = [];
        }else{*/
        $this->map['logs'] = DashboardDB::get_logs();
        $this->map['logins'] = DashboardDB::get_log_ins();
        $this->map['notes'] = DashboardDB::getNotesDashboard($current_user_id);
        $this->map['schedules'] = get_schedules();
        $this->parse_layout('chart',$this->map);
    }
}
?>