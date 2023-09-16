<?php
class ReportForm extends Form
{
    protected $map;
    function __construct()
    {
        Form::Form('ReportForm');
        Portal::$extra_header .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');
    }
    function draw(){
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
        $this->map['status_id_list'] = [XAC_NHAN=>'DT xác nhận',THANH_CONG=>'DT thành công'];
        $this->parse_layout('rank',$this->map);
    }
    function get_groups($system_group_id){
        $sql = '
            select 
                groups.id,groups.name
            from `groups`
            join groups_system on groups_system.id = groups.system_group_id
            where '.IDStructure::child_cond(DB::structure_id('groups_system',$system_group_id)).'
            order by groups_system.structure_id
        ';
        return DB::fetch_all($sql);
    }
    function get_systems($system_group_id){
        $sql = '
            select id,name,icon_url,structure_id
            from groups_system
            where '.IDStructure::child_cond(DB::structure_id('groups_system',$system_group_id)).'
            order by structure_id
        ';
        return DB::fetch_all($sql);
    }
    function get_group_statistic($system_group_id, $start_time,$end_time){
        //$no_revenue_status = DashboardDB::get_no_revenue_status();
        $sql = '
            select 
                groups.id,groups.name,groups.address,groups.image_url,
                "-" as position_exchange
            from 
                `groups`
                join groups_system on groups_system.id = system_group_id
            where 
                groups.expired_date > "'.$start_time.'"
                AND '.(Url::get('system_group_id')?'groups_system.id='.Url::get('system_group_id'):IDStructure::child_cond(DB::structure_id('groups_system',$system_group_id))).'
        ';
        if($group_ids = DB::escapeArray(Url::post('group_ids')) and implode(',',$group_ids)){
            $sql = '
                select 
                    groups.id,groups.name,groups.address,groups.image_url,
                    "-" as position_exchange
                from 
                    `groups`
                where 
                    groups.id IN ('.implode(',',$group_ids).')
            ';
        }
        $groups = DB::fetch_all($sql);
        foreach($groups as $k=>$v){
            $no_revenue_status = DashboardDB::get_no_revenue_status($k);        
            $cond = 'orders.group_id=' . $k . '';
            $cond .= ' and orders.status_id NOT IN (' . $no_revenue_status . ')';
            if($status_id = Url::get('status_id')){
                if($status_id==THANH_CONG){
                    $cond .= ' and orders.confirmed>="' . $start_time . ' 00:00:00" and  orders.confirmed<="' . $end_time . ' 23:59:59" and orders_extra.update_successed_time<>"0000-00-00 00:00:00"';
                    $sql = '
                        SELECT
                                total_price AS total
                        FROM
                                orders
                                JOIN orders_extra ON orders_extra.order_id=orders.id
                        WHERE
                        ' . $cond;
                }else{
                    $cond .= ' and orders.confirmed>="' . $start_time . ' 00:00:00" and  orders.confirmed<="' . $end_time . ' 23:59:59"';
                    $sql = 'select total_price as total from orders where ' . $cond;   
                }
            }else{
                $cond .= ' and orders.confirmed>="' . $start_time . ' 00:00:00" and  orders.confirmed<="' . $end_time . ' 23:59:59"';
                $sql = 'select total_price as total from orders where ' . $cond;
            }
            $total_price = DashboardDB::get_total_amount($sql);
            $groups[$k]['total'] = $total_price ? $total_price/1000000 : 0;
        }
        if(!empty($groups)){
            $group_ids = implode(',',MiString::get_list($groups,'id'));
            if (sizeof($groups) > 1) {
                System::sksort($groups, 'total', 'DESC');
            }
            //////////////////////////////////////////////////
            $tong_nhan_su = DB::fetch('select count(*) as total from account where group_id IN ('.$group_ids.') and is_active=1','total');
            $this->map['tong_nhan_su'] = $tong_nhan_su;
        }else{
            $this->map['tong_nhan_su'] = 0;
        }
        return $groups;
    }
}
?>