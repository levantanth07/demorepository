<?php
class ReportForm extends Form
{
    protected $map;
    protected $kho_so_months;
    function __construct()
    {
        $this->kho_so_months = [];
        Form::Form('ReportForm');
    }
    function draw(){
        $this->map = array();
        for($i=2018;$i<=date('Y');$i++){
            $this->map['year_list'][$i] =  $i;
        }
        if(!Url::get('year')){
            $_REQUEST['year'] = date('Y');
        }
        $year = Url::iget('year');
        $group_id = Session::get('group_id');
        $account_type = Session::get('account_type');
        $master_group_id = Session::get('master_group_id');

        $this->map['need_rate'] = 0;
        $this->map['overdue'] = 0;
        $this->map['rated'] = 0;
        $this->map['total_order'] = 0;

        //////////////////////////////////////////////////
        if(!Url::get('date_from')){
            $_REQUEST['date_from'] = date('d/m/'.$year);
        }
        if(!Url::get('date_to')){
            $_REQUEST['date_to'] = date('d/m/'.$year);//Date_Time::get_last_day_of_month($month,$year)
        }
        $start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
        $end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));
        if(strtotime($end_time) - strtotime($start_time) > 31*24*3600){
            die('<div class="alert alert-warning">Bạn vui lòng chọn tối đa 1 tháng!</div>');
        }else{
        }
        $status = RatingReportDB::get_statuses();
        $reports = array();
        $this->map['sale'] = $sale = Url::get('sale')?true:false;
        if(Url::get('view_report')){
            $users = RatingReportDB::get_users('CS',Url::get('is_active'),true);
            $this->map['user_id_list'] = [''=>'Tất cả các tài khoản marketing'] + MiString::get_list($users,'full_name');
            $reports['label']['id'] = 'label';
            $reports['label']['username'] = 'Tên tài khoản';
            $reports['label']['name'] = 'Họ và tên';
            $reports['label']['need_rate'] = 'Chưa xử lý';
            $reports['label']['overdue'] = 'Quá hạn';
            $reports['label']['rated'] = 'Đã xử lý';
            $reports['label']['ty_le_xu_ly'] = '% xử lý';
            $reports['label']['ty_le_chua_xu_ly'] = '% chưa xử lý';
            $reports['label']['total_order'] = 1000000000000000000000000;
            foreach($users as $key=>$value){
                if($master_group_id){
                    $cond = '(orders.master_group_id='.$master_group_id.')';
                }else{
                    $cond = '(orders.group_id='.$group_id.' '.($account_type?' or orders.master_group_id='.$group_id.'':'').')';
                }
                $cond .= ' AND orders.status_id='.THANH_CONG;
                $cond .= ' AND order_rating.assigned_user_id='.$key;
                $need_rate_cond = $cond.' 
                    AND orders_extra.update_successed_time>=date_sub(NOW(), INTERVAL 1 day)
                    AND (select count(id) from order_rating where order_id=orders.id) = 0
                ';
                $need_rate_sql = '
                    SELECT
                        COUNT(orders.id) as total
                    FROM
                        orders
                        JOIN orders_extra on orders_extra.order_id = orders.id
                        JOIN order_rating on order_rating.order_id=orders.id
                    WHERE
                        '.$need_rate_cond.'
                ';
                $overdue_cond = $cond.' 
                    AND orders_extra.update_successed_time<date_sub(NOW(), INTERVAL 1 day)
				    AND (order_rating.rating_time > UNIX_TIMESTAMP(orders_extra.update_successed_time))
                ';
                $overdue_sql = '
                    SELECT
                        COUNT(orders.id) as total
                    FROM
                        orders
                        JOIN orders_extra on orders_extra.order_id = orders.id
                        JOIN order_rating on order_rating.order_id=orders.id
                    WHERE
                        '.$overdue_cond.'
                ';
                $rated_cond = $cond.' 
                    AND order_rating.rating_time>0
                ';
                $rated_sql = '
                    SELECT
                        COUNT(orders.id) as total
                    FROM
                        orders
                        JOIN orders_extra on orders_extra.order_id = orders.id
                        JOIN order_rating on order_rating.order_id=orders.id
                    WHERE
                        '.$rated_cond.'
                ';
                $total_cond = $cond.' 
                    
                ';
                $total_sql = '
                    SELECT
                        COUNT(orders.id) as total
                    FROM
                        orders
                        JOIN orders_extra on orders_extra.order_id = orders.id
                        JOIN order_rating on order_rating.order_id=orders.id
                    WHERE
                        '.$total_cond.'
                ';
                $reports[$key]['id'] = $key;
                $reports[$key]['name'] = $value['full_name'].' '.($value['rated_point']>0?'<span class="small text-warning">'.round($value['rated_point'],2).'<i class="fa fa-star"></i>('.$value['rated_quantity'].')</span>':'').' <div class="small" style="color:#999;font-style: italic;"> '.$value['username'].' </div>';
                $kho_so_mkt_users[] = $reports[$key]['name'];
                $reports[$key]['need_rate'] = DB::fetch($need_rate_sql,'total');
                $reports[$key]['overdue'] = DB::fetch($overdue_sql,'total');
                $reports[$key]['rated'] = DB::fetch($rated_sql,'total');
                $reports[$key]['total_order'] = DB::fetch($total_sql,'total');;
                $this->map['need_rate'] += $reports[$key]['need_rate'];
                $this->map['overdue'] += $reports[$key]['overdue'];
                $this->map['rated'] += $reports[$key]['rated'];
                $this->map['total_order'] +=$reports[$key]['total_order'];
                $reports[$key]['ty_le_xu_ly'] = round($reports[$key]['rated']/$reports[$key]['total_order'],3)*100;
                $reports[$key]['ty_le_chua_xu_ly'] = round(($reports[$key]['need_rate'] + $reports[$key]['overdue'])/$reports[$key]['total_order'],3)*100;
                $reports[$key]['total'] = 0;
            }
        }
        if(sizeof($reports)>2){
            System::sksort($reports, 'total_order','DESC');
        }
        $this->map['reports'] = $reports;
        $this->map['status'] = $status;
        $months = array();
        for($i=1;$i<=12;$i++){
            $months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
        }
        $this->map += RatingReportDB::get_report_info();
        $this->map['group_id_list'] = array('' => 'Chọn công ty','0'=>'Tất cả') + MiString::get_list(RatingReportDB::get_groups());
        $layout = 'list';
        $this->map['account_group_id_list'] = array(''=>'Xem theo nhóm tài khoản',''=>'Tất cả các nhóm tài khoản') + MiString::get_list(RatingReportDB::get_account_groups());
        $this->parse_layout($layout,$this->map);
    }
}
?>