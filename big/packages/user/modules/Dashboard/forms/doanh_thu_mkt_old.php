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

    function __construct()
    {
        Form::Form('ReportForm');
        $this->link_js('/packages/core/includes/js/helper.js?v=101020201');
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');

        $this->group_id = Session::get('group_id');
        $this->isTeamLead = is_account_group_manager();
        $this->selectUsers = URL::getArray('users', [0]);
        $this->selectIsActive = URL::getUInt('is_active', 1);
        $this->unknowTeamID = 100000;
        $this->isOwner = is_group_owner();

        // Lấy danh sách tất cả team, user của shop
        ['teams' => $this->allTeams, 'users' => $this->allUsers, 'users_teams' => $this->allUsersTeams] = $this->getAllTeamsAndUser();
        ksort($this->allUsersTeams);
    }   

    /**
     * Determines if upsale action.
     *
     * @return     bool  True if upsale action, False otherwise.
     */
    private function isUpsaleAction()
    {
        return URL::getString('act') == 'upsale';
    }

    function draw()
    {
        // if (DashboardDB::checkPermissionMktReport() == false) {
        //     Url::js_redirect(false,'Bạn không có quyền truy cập!');
        // }
        //error_reporting(0);
        $isUpsaleAction = $this->isUpsaleAction();
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
        $this->map['is_upsale'] =  $isUpsaleAction;
        //////////////////////////////////////////////////
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
        $users = $this->getUsers();

        $reports = array();
         if(Url::get('view_report')){
            $reports['label']['id'] = 'label';
            $reports['label']['name'] =  $isUpsaleAction ?'Nhân viên Upsale':'Nhân viên marketing';
            $reports['label']['sl'] = 'SL';
            $reports['label']['so_cap'] = 'Số cấp';
            $reports['label']['so_chia'] = 'Số chia';
            $status[1000000000] = array('id'=>1000000000,'name'=>'Tổng','total'=>0,'qty'=>0);
            foreach($users as $user){
                foreach($status as $statusID => $_status){
                    $reports['label'][$statusID][1] = ['total_price'=>'Doanh thu','qty'=>'Số Lượng','name'=>$_status['name']];
                }
            }


            $reports['label']['total'] = 1000000000000000000000000;
            if($master_group_id){
                $cond = '(orders.master_group_id='.$master_group_id.')';
            }else{
                $cond = '(orders.group_id='.$this->group_id.' '.($account_type?' or orders.master_group_id='.$this->group_id.'':'').')';
            }
            if($type = Url::iget('type')){
                $cond .= ' AND orders.type = '.$type;
            }
            foreach($users as $userID=>$value){
                $key = $value['username'];

                if(!is_account_group_manager()){
                    if(!Dashboard::$quyen_admin_marketing && !Dashboard::$quyen_bc_doanh_thu_mkt && !Dashboard::$quyen_marketing){
                        if($key!=$current_account_id){
                            unset($users[$key]);
                            continue;
                        }
                    }
                }
                $reports[$key]['id'] = $key;
                $reports[$key]['name'] = $value['user_name'].' <div class="small" style="color:#999;font-style: italic;"> '.$value['username'].' </div>';
                $total_ = 0;
                $total_total_price = 0;
                $total_qty = 0;
                $no_revenue_status = DashboardDB::get_no_revenue_status();
                /////////////////////////////start the phone mkt created/////////////////////////////
                $cond_so_cap = '
                    (orders.group_id='.$this->group_id.'
                    '.($master_group_id?' or groups.master_group_id='.$master_group_id:'').'
                    '.($tong_cong_ty?' or groups.master_group_id='.$this->group_id:'').'
                    )
                    and orders.created>="'.$start_time.' 00:00:00" 
                    and  orders.created<="'.$end_time.' 23:59:59" 
                    and '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created').'='.$value['id'].'
                ';
                $total_sql_so_cap = '
                    select 
                        count(orders.id) as total
                    from 
                        orders
                        left join orders_extra on orders_extra.order_id=orders.id
                        join `groups` on groups.id=orders.group_id
                    where 
                        '.$cond_so_cap.'
                    group by 
                        '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created').'
                ';
                $total_order_so_cap = DB::fetch($total_sql_so_cap,'total');
                $this->map['tong_so_cap'] += $total_order_so_cap;
                if($this->map['so_cap_max']<=$total_order_so_cap){
                    $this->map['so_cap_max'] = $total_order_so_cap;
                }
                $reports[$key]['so_cap'] = $total_order_so_cap;
                //////////////////////////end///////////////////////////////////////
                //////////////////////////start the phone assigned to sale//////////
                $cond2 = $cond.' and orders.assigned>="'.$start_time.' 00:00:00" and  orders.assigned<="'.$end_time.' 23:59:59" and '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created').'='.$value['id'].'';
                $this->map['tong_so_chia'] += $reports[$key]['so_chia'] = DB::fetch('select count(orders.id) as total from orders left join orders_extra on orders_extra.order_id=orders.id where '.$cond2.' group by orders.user_created','total');
                //////////////////////////end///////////////////////////////////////
                $cond2 = '
                    (orders.group_id='.$this->group_id.'
                    '.($master_group_id?' or groups.master_group_id='.$master_group_id:'').'
                    '.($tong_cong_ty?' or groups.master_group_id='.$this->group_id:'').'
                    )
                    and orders.created>="'.$start_time.' 00:00:00" and  orders.created<="'.$end_time.' 23:59:59" 
                    and '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created').'='.$value['id'].' and IFNULL(s.not_reach,0) <> 1
                    ';
                $sql = 'select count(orders.id) as total from orders left join orders_extra on orders_extra.order_id=orders.id join statuses s on orders.status_id = s.id LEFT JOIN groups ON orders.group_id = groups.id where '
                    .$cond2.' group by '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created');
                $this->map['so_tiep_can_total'] += $reports[$key]['so_tiep_can'] = DB::fetch($sql,'total');
                /////////////////////////////////////
                $this->map['ket_noi'] = $this->map['tong_so_chia'] > 0 ?  (round($this->map['so_tiep_can_total']/$this->map['tong_so_chia'],2)*100) : 0;
                if($reports[$key]['so_chia']>0){
                    $reports[$key]['ty_le_ket_noi'] = $ty_le_ket_noi = (round($reports[$key]['so_tiep_can']/$reports[$key]['so_chia'],2)*100).'';
                }else{
                    $reports[$key]['ty_le_ket_noi'] = '';
                    $ty_le_ket_noi = 0;
                }
                $reports[$key]['ty_le_ket_noi_color'] = DashboardDB::get_color_by_rate($ty_le_ket_noi);
                /////////////////////////////////////
                foreach($status as $k=>$v){
                    if($account_type){
                        $cond = '(orders.group_id='.$this->group_id.' or orders.master_group_id='.$this->group_id.')';
                    }else{
                        if($master_group_id){
                            $cond = '(orders.group_id='.$this->group_id.' or orders.master_group_id='.$master_group_id.')';
                        }else{
                            $cond = '(orders.group_id='.$this->group_id.')';
                        }
                    }
                    if($type=Url::iget('type')){
                        $cond .= ' AND orders.type='.$type.'';
                    }
                    if($k!=HUY and $no_revenue_status){
                        $cond .= ' and orders.status_id NOT IN ('.$no_revenue_status.')';
                    }
                    if($k==XAC_NHAN){//xn
                        $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created').'='.$value['id'].' and orders.status_id <> '.HUY;
                        $total_price = DB::fetch('select sum(total_price) as total from orders join orders_extra on orders_extra.order_id = orders.id where '.$cond.' group by '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created'),'total');
                        $total_order = DB::fetch('select count(*) as total from orders join orders_extra on orders_extra.order_id = orders.id where '.$cond.' group by '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created'),'total');
                    }
                    elseif($k==HUY or $k==CHUYEN_HOAN or $k==CHUYEN_HANG or $k==TRA_VE_KHO){
                        $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created').'='.$value['id'].' and orders.status_id = '.$k.'';
                        $total_price = DB::fetch('select sum(total_price) as total from orders join orders_extra on orders_extra.order_id = orders.id where '.$cond.' group by '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created'),'total');
                        $total_order = DB::fetch('select count(*) as total from orders join orders_extra on orders_extra.order_id = orders.id where '.$cond.' group by '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created'),'total');
                    }
                    elseif($k==KE_TOAN)
                    {// đóng hàng
                        $cond .= ' and orders_extra.accounting_confirmed>="'.$start_time.' 00:00:00" and  orders_extra.accounting_confirmed<="'.$end_time.' 23:59:59" and '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created').'='.$value['id'].'';
                        $total_price = DB::fetch('
                                select 
                                    sum(orders.total_price) as total 
                                from 
                                    orders
                                    join orders_extra on orders_extra.order_id = orders.id
                                where 
                                    '.$cond.' 
                                group by 
                                    '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created'),'total');
                        $total_order = DB::fetch('
                                select 
                                    count(orders.id) as total 
                                from 
                                    orders 
                                    join orders_extra on orders_extra.order_id = orders.id
                                where 
                                    '.$cond.' 
                                group by 
                                    '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created'),'total');
                    }
                    elseif($k==THANH_CONG){
                        // Lọc đơn theo ngày chuyển trạng thái 
                        if($date_type==1){
                            $cond .= ' AND orders_extra.update_successed_time>="'.$start_time.' 00:00:00"';
                            $cond .= ' AND orders_extra.update_successed_time<="'.$end_time.' 23:59:59"';
                        }
                        // Lọc theo ngày xác nhận (mạc đinh)
                        else{
                            $cond .= ' AND orders.confirmed>="'.$start_time.' 00:00:00"';
                            $cond .= ' AND  orders.confirmed<="'.$end_time.' 23:59:59"';
                        }

                        // Lọc theo upsale 
                        if( $isUpsaleAction ){
                            $cond .= ' AND orders_extra.upsale_from_user_id = '.$value['id'];
                        }
                        // Lọc theo mkt (mặc định)
                        else{
                            $cond .= ' AND orders.user_created = '.$value['id'];
                        }

                        // Đơn đã qua trạng thái đã thu tiền
                        $cond .= ' AND orders_extra.update_successed_time IS NOT NULL';
                        $cond .= ' AND orders_extra.update_successed_user IS NOT NULL';

                        // Thời gian thanh cong phải lớn hơn thời gian xác nhận
                        $cond .= ' AND orders_extra.update_successed_time  >= orders.confirmed';

                        // Loại bỏ các đơn có status id thuộc các trạng thái chuyển hoản và trả hàng về kho
                        $cond .= ' AND orders.status_id NOT IN (' . implode(',', [CHUYEN_HOAN, TRA_VE_KHO]) . ')';

                        $result = DB::fetch('
                                    SELECT 
                                        sum(orders.total_price) AS total_price, count(orders.id) AS total_order  
                                    FROM 
                                        orders
                                        JOIN orders_extra ON orders_extra.order_id = orders.id
                                    WHERE 
                                        '.$cond.'
                                    GROUP BY
                                        '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created'));
                        ['total_price' => $total_price, 'total_order' => $total_order] = $result;

                    }
                    elseif($k==DA_THU_TIEN){   
                        // Lọc đơn theo ngày chuyển trạng thái 
                        if($date_type==1){
                            $cond .= ' AND orders_extra.update_paid_time>="'.$start_time.' 00:00:00"';
                            $cond .= ' AND orders_extra.update_paid_time<="'.$end_time.' 23:59:59"';
                        }
                        // Lọc theo ngày xác nhận (mạc đinh)
                        else{
                            $cond .= ' AND orders.confirmed>="'.$start_time.' 00:00:00"';
                            $cond .= ' AND  orders.confirmed<="'.$end_time.' 23:59:59"';
                        }

                        // Lọc theo upsale 
                        if( $isUpsaleAction ){
                            $cond .= ' AND orders_extra.upsale_from_user_id = '.$value['id'];
                        }
                        // Lọc theo mkt (mặc định)
                        else{
                            $cond .= ' AND orders.user_created = '.$value['id'];
                        }

                        // Đơn đã qua trạng thái đã thu tiền
                        $cond .= ' AND orders_extra.update_paid_time IS NOT NULL';
                        $cond .= ' AND orders_extra.update_paid_user IS NOT NULL';

                        // Thời gian thu tiền phải lớn hơn thời gian xác nhận
                        $cond .= ' AND orders_extra.update_paid_time  >= orders.confirmed';

                        // Chỉ lấy đơn ở trạng thái đã thu tiền 
                        $cond .= ' AND orders.status_id IN (' . implode(',', [DA_THU_TIEN, CHUYEN_HANG]) . ')';

                        $result = DB::fetch('
                                    SELECT 
                                        sum(orders.total_price) AS total_price, count(orders.id) AS total_order  
                                    FROM 
                                        orders
                                        JOIN orders_extra ON orders_extra.order_id = orders.id
                                    WHERE 
                                        '.$cond.'
                                    GROUP BY
                                        '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created'));
                        ['total_price' => $total_price, 'total_order' => $total_order] = $result;
                    }
                    else{
                        $cond .= ' and orders.created>="'.$start_time.'" and  orders.created<="'.$end_time.' 23:59:59" and '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created').'='.$value['id'].' and orders.status_id='.$k.'';
                        $total_price = DB::fetch('SELECT sum(total_price) as total 
                                                 FROM orders 
                                                 LEFT JOIN orders_extra on orders_extra.order_id = orders.id 
                                                 WHERE '.$cond.' 
                                                 GROUP BY '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created'),
                                                 'total');
                        $total_order = DB::fetch('SELECT count(*) as total 
                                                 FROM orders 
                                                 LEFT JOIN orders_extra on orders_extra.order_id = orders.id
                                                 WHERE '.$cond.' 
                                                 GROUP BY '.( $isUpsaleAction ?'orders_extra.upsale_from_user_id':'orders.user_created'),
                                                 'total');
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
                $status[1000000000]['total'] += $total_total_price;
                $status[1000000000]['qty'] += $total_qty;
                $reports[$key][1000000000] = array(1=>array('total_price'=>System::display_number($total_total_price),'qty'=>System::display_number($total_qty),'name'=>'Tổng'));
                $reports[$key]['total'] = $total_;
            }

            if(sizeof($reports)>2){
                System::sksort($reports, 'total','DESC');

            }
        }

        $this->map['reports'] = $reports;
        $this->map['mkt_status'] = $status;
        
        $this->map['tong_so_chot_per_cap'] = (($this->map['tong_so_cap']>0)?round($status[XAC_NHAN]['qty']/$this->map['tong_so_cap'],3)*100:0);
        $this->map['tong_so_chot_per_chia'] = (($this->map['tong_so_chia']>0)?round($status[XAC_NHAN]['qty']/$this->map['tong_so_chia'],3)*100:0);
        $months = array();
        for($i=1;$i<=12;$i++){
            $months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
        }
        $this->map['month_list'] = $months;
        $this->map['is_active_list'] = array(''=>'Tài khoản kích hoạt',1=>'Tài khoản chưa kích hoạt',2=>'Tất cả');
        
        // all
        $this->map['users'] = $this->allUsers;
        $this->map['teams'] = $this->allTeams;
        $this->map['users_teams'] = $this->allUsersTeams;

        $this->map['type_list'] = Dashboard::$type;
        $this->map += DashboardDB::get_report_info();
        $this->map['group_id_list'] = array('' => 'Chọn công ty','0'=>'Tất cả') + MiString::get_list(DashboardDB::get_groups());
        $this->map['date_type_list'] = Dashboard::$date_type;
        $this->parse_layout('doanh_thu_mkt_old',$this->map);
    }

    /**
     * Determines ability to user access.
     *
     * @return     bool  True if able to user access, False otherwise.
     */
    private function canUserAccess(){
        return is_account_group_manager()
           || Dashboard::$nhom_bc_mkt
           || Dashboard::$quyen_admin_marketing
           || Dashboard::$quyen_bc_doanh_thu_mkt;
    }

    /**
     * Gets all teams and user.
     *
     * @return     <type>  All teams and user.
     */
    private function getAllTeamsAndUser()
    {   

        $reduceInit = [
            'teams' => [], 
            'users' => [],
            'users_teams' => [
                $this->unknowTeamID => [] // đây là team mặc định cho các user đơn lẻ
            ]
        ];

        if($this->isOwner || Dashboard::$quyen_admin_marketing || Dashboard::$admin_group || Dashboard::$quyen_bc_doanh_thu_mkt){
            // xem  hết cuar shop 
            $canViewCondition = '';
        }else if($this->isTeamLead){
            // xem cua team
            $canViewCondition = ' AND account_group.admin_user_id = ' . get_user_id();
        }else if(Dashboard::$quyen_marketing){
            // xem của nó
            $canViewCondition = ' AND account.id = "' . Session::get('user_id') . '"';
        }else{
            return $reduceInit;
        }

        $isActiveCondition = '';
        switch($this->selectIsActive){
            case 1:
                $isActiveCondition = ' AND account.is_active = 1';
                break;

            case 2:
                $isActiveCondition = ' AND (account.is_active = 0 OR account.is_active IS NULL)';
                break;
        }

        $isUpsaleCondition = $this->isUpsaleAction() ? '' : ' AND roles_to_privilege.privilege_code IN ("MARKETING") ';

        $sql = '
            SELECT account.id as username,
                users.id as user_id,
                users.name as user_name,
                account_group.name as team_name, 
                account_group.id as team_id, 
                account.is_active
            FROM `account` 
            LEFT JOIN account_group ON account_group_id = account_group.id 
            JOIN users ON users.username = account.id
            LEFT JOIN users_roles ON  users_roles.user_id = users.id
            JOIN roles ON roles.id = users_roles.role_id
            JOIN roles_to_privilege ON roles_to_privilege.role_id = roles.id
            WHERE 
                account.group_id = ' . $this->group_id
                . $isUpsaleCondition
                . $isActiveCondition
                . $canViewCondition;
        $results = DB::fetch_all_array($sql);

        return Arr::of($results)->reduce(function($output, $row) {
            //Xây dựng danh sách nhóm
            $teamID = intval($row['team_id']);
            if($teamID && !isset($output['teams'][$teamID])){
                $output['teams'][$teamID] = ['id' => $row['team_id'], 'name' => $row['team_name']];
                $output['users_teams'][$teamID] = [];
            }

            // xây dựng danh sách người dùng
            $userID = $row['user_id'];
            if(!isset($output['users'][$userID])){
                $output['users'][$userID] = ['id' => $userID, 'username' => $row['username'],'user_name'=>$row['user_name'], 'is_active' => $row['is_active'], 'team_id' => $teamID];
            }

            $output['users_teams'][$teamID ? $teamID : $this->unknowTeamID][] = $userID;

            return $output;
        }, $reduceInit)
        ->toArray();
    }

    /**
     * Lọc user hợp lệ để tính toán số liệu báo cáo. Bởi vì user khi được submit lên là 1 danh sách có thể bị can thiệp 
     * bởi người dùng nên ta sẽ lọc các user phù hợp điều kiện lọc như active, thuộc nhóm, ... và phải tồn tại trong shop
     *
     * @return     <type>  The users.
     */
    private function getUsers()
    {   
        return in_array(0, $this->selectUsers) 
            ? $this->allUsers 
            : Arr::of($this->allUsers)->filter(function($e){
                return !!in_array($e['id'], $this->selectUsers);
            })->toArray();
    }
}
?>
