<?php

require_once ROOT_PATH . 'packages/core/includes/common/SystemsTree.php';

class ReportCharForm extends Form
{   
    const LOC_THEO_HE_THONG = 0;
    const LOC_THEO_NGAY_THANH_LAP_SHOP = 1;
    const LOC_THEO_NGAY_TAO_SHOP = 2;

    protected $map;
    public static $system_group_id;
    public static $system_group;
    public static $user_id;
    function __construct()
    {
        Form::Form('ReportForm');
        Portal::$extra_header .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');
        self::$user_id = get_user_id();
        self::$system_group_id = DB::fetch('select id,system_group_id from groups_system_account where user_id='.self::$user_id.' limit 0,1','system_group_id');
        self::$system_group = DB::fetch('select id,name,icon_url,structure_id from groups_system where id='.self::$system_group_id);
        if(URL::get('act') == 'get-group-statistic'){
            RequestHandler::sendJson($this->get_group_statistic());
        }
    }
    function draw(){
        $this->map['admin_group'] = !!Dashboard::$admin_group;
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

        if(strtotime($end_time) - strtotime($start_time) > 31*24*3600){
            RequestHandler::sendJsonError('Bạn vui lòng chọn tối đa 1 tháng!');
        }

        if(strtotime($end_time) - strtotime($start_time) < 0){
            RequestHandler::sendJsonError('Thời gian xem báo cáo không hợp lệ.');
        }


        
        // $user_id = get_user_id();
        // $system_group_id= DB::fetch('select id,system_group_id from groups_system_account where user_id='.$user_id.' limit 0,1','system_group_id');
        // $system_group = DB::fetch('select id,name,icon_url,structure_id from groups_system where id='.$system_group_id);

        $this->map['group_system_name'] = self::$system_group['name'];
        $this->map['group_system_icon_url'] = self::$system_group['icon_url'];
        if(URL::get('do') === 'load_company_chart'){
            return $this->render_company_chart(self::$system_group_id,$start_time,$end_time);
        }

        require_once 'packages/core/includes/utils/category.php';
        $this->map['system_group'] = SystemsTree::selectBox(
            self::$system_group['structure_id'], 
            [   
                'selected' => URL::iget('system_group_id'), 
                'selectedType' => SystemsTree::SELECTED_CURRENT,
                'props' => [
                    'name' =>"system_group_id",
                    'id' =>"system_group_id",
                    'class' =>"form-control"
                ],
                'default' => '<option value="0">Tất cả hệ thống</option>'
            ]);

        $this->map['status_id_list'] = [XAC_NHAN=>'DT xác nhận',THANH_CONG=>'DT thành công',CHUYEN_HANG=>'DT Chuyển hàng',CHUYEN_HOAN=>'DT Chuyển hoàn'];
        $groups = $this->get_groups(self::$system_group_id);
        $group_ids = [];
        if (isset($_REQUEST['group_ids']) && is_array($_REQUEST['group_ids'])){
            $group_ids = $_REQUEST['group_ids'];
        }
        $group_ids = array_filter($group_ids,'strlen');
        if (count($group_ids) == count($groups)) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        $this->map['group_ids_option'] = '<option value="" '. $selected .'>Tất cả</option>';
        foreach($groups as $key=>$val){
            if (in_array($key,$group_ids)) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $this->map['group_ids_option'] .= '<option value="'.$key.'"'.$selected.'>'.$val['name'].'</option>';
        }
        $this->map['group_ids_list'] = array(''=>'Lọc theo shop') + MiString::get_list($groups);
        $this->map['view_type_list'] = [''=>'Xem theo cha con','1'=>'Chỉ xem riêng từng hệ thống'];
        $this->map['filter_by_list'] = [''=>'Theo hệ thống','1'=>'Ngày thành lập shop','2'=>'Ngày tạo shop'];
        if(Url::get('filter_by')){
            $this->map['title'] = 'THÀNH LẬP TRONG THÁNG';
        }else{
            $this->map['title'] = 'HỆ THỐNG '.$this->map['group_system_name'];
        }
        $this->parse_layout('system_dashboard',$this->map);
    }
    function get_groups($system_group_id){
        $sql = '
            select 
                groups.id,groups.name
            from `groups`
            join groups_system on groups_system.id = groups.system_group_id
            where '.Systems::getIDStructureChildCondition(DB::structure_id('groups_system',$system_group_id)).'
            order by groups_system.structure_id
        ';
        return DB::fetch_all($sql);
    }
    function get_systems($system_group_id){
        $sql = '
            select id,name,icon_url,structure_id
            from groups_system
            where '.Systems::getIDStructureChildCondition(DB::structure_id('groups_system',$system_group_id)).'
            order by structure_id
        ';
        return DB::fetch_all($sql);
    }

    function get_groups_statistic($system_group_id, $start_time,$end_time){
        DB::$db_num_queries = 0;
        $cond = 'groups.expired_date > "'.$start_time.'"';
        $cond .= $this->getFilterByCondition($system_group_id, $start_time, $end_time);
        $cond .= $this->getGroupIDsCondition();
        return DB::fetch_all($this->buildSqlQuery($cond));
    }

    private function getTotalPrice($sql)
    {   
        $prices = ['total' => 0, 'total_cskh' => 0, 'total_somoi' => 0];

        if(!$results = DB::query($sql)){
            return $prices;
        }

        while($result = $results->fetch_assoc()){
            $prices['total'] += $result['total'];
            $prices['total_cskh'] += $result['type'] != 1 ? $result['total'] : 0;
            $prices['total_somoi'] += $result['type'] == 1 ? $result['total'] : 0;
        }

        return $prices;
    }

    /**
     * Gets the filter by condition.
     *
     * @param      int     $system_group_id  The system group identifier
     * @param      <type>  $start_time       The start time
     * @param      string  $end_time         The end time
     *
     * @return     string  The filter by condition.
     */
    private function getFilterByCondition($system_group_id, $start_time, $end_time)
    {
        $filter_by = Url::get('filter_by');

        if($filter_by && Dashboard::$bdh==true){//ban dieu hanh
            if($filter_by == self::LOC_THEO_NGAY_THANH_LAP_SHOP){
                return sprintf(
                    ' and (groups.date_established >= "%s-01" and groups.date_established <= "%s")',
                    date('Y-m',strtotime($start_time)),
                    $end_time
                );
            }
            
            return sprintf(
                ' and (groups.created >= "%s-01" and groups.created <= "%s")', 
                date('Y-m',strtotime($start_time)), 
                $end_time
            );
        }

        if(Url::get('system_group_id')){
            return ' and '.(Url::iget('view_type') ?
                             ' groups_system.id='.Url::iget('system_group_id')
                             :$this->getStructureIDCondition(Url::iget('system_group_id')));
        }

        return ' and '.$this->getStructureIDCondition($system_group_id);
        
    }

    /**
     * Gets the structure id condition.
     *
     * @param      <type>  $system_group_id  The system group identifier
     *
     * @return     <type>  The structure id condition.
     */
    private function getStructureIDCondition($system_group_id)
    {
        return Systems::getIDStructureChildCondition(DB::structure_id('groups_system',$system_group_id));
    }

    /**
     * Gets the group IDs condition.
     *
     * @return     string  The group IDs condition.
     */
    private function getGroupIDsCondition()
    {
        if(empty($_REQUEST['group_ids']) || !is_array($_REQUEST['group_ids'])){
            return;
        }

        $validGroupIDs = [];
        foreach ($_REQUEST['group_ids'] as $groupID) {
            if($groupID = intval($groupID)){
                $validGroupIDs[] = $groupID;
            }
        }
        
        return ' AND groups.id IN ('.implode(',',$validGroupIDs).')';
    }

    /**
     * Gets the system statuses.
     *
     * @param      bool    $withLevelCondition  The with level condition
     *
     * @return     <type>  The system statuses.
     */
    private function getSystemStatuses($withLevelCondition = false)
    {
        $sql = '
            SELECT `id` 
            FROM statuses 
            WHERE 
                is_system = 1 
                AND (no_revenue = 0 OR no_revenue IS NULL)'
                . ($withLevelCondition ? ' AND statuses.level >=2' : '')
                . '';

        return array_keys(DB::fetch_all($sql, 'id'));
    }

    /**
     * Builds a sql query.
     *
     * @param      string  $cond   The condition
     *
     * @return     string  The sql query.
     */
    private function buildSqlQuery($cond)
    {   
        $and = '';
        $usersTrue = $this->get_user_report();
        if($usersTrue){
            $and = ' AND users.id IN ('. $usersTrue .')';
        }

        return '
            SELECT 
                COUNT(DISTINCT account.id) as total_user,
                COUNT(DISTINCT account_parent.id) as total_parent_user,
                groups.id,groups.name,groups.address,groups.image_url,
                "-" AS position_exchange,groups.expired_date,
                groups.rated_point
            FROM 
                account
                JOIN users ON users.username = account.id AND account.is_active = 1
                JOIN groups ON users.group_id = groups.id
                JOIN groups_system ON groups_system.id = system_group_id
                LEFT JOIN (SELECT id,is_active,is_count FROM account WHERE account.is_active = 1 AND account.is_count = 1) as account_parent ON account_parent.id = account.id
            WHERE
                '. $cond . $and . '
            GROUP BY `groups`.`id`
        ';
    }

    private function get_user_report(){
        if(!Url::get('date_from')){
            $_REQUEST['date_from'] = date('d/m/Y');
        }
        if(!Url::get('date_to')){
            $_REQUEST['date_to'] = date('d/m/Y');
        }

        $start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
        $end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));
        DB::$db_num_queries = 0;
        $cond = 'groups.expired_date > "'.$start_time.'"';
        $cond .= $this->getFilterByCondition(self::$system_group_id, $start_time, $end_time);
        $cond .= $this->getGroupIDsCondition();
        $sql = '
            SELECT 
                users.id,users.username
            FROM 
                users
                JOIN groups ON users.group_id = groups.id
                JOIN groups_system ON groups_system.id = system_group_id
                JOIN account ON account.id = users.username AND account.is_active = 1
            WHERE
                '.$cond.'
        ';
        $query = DB::fetch_all($sql);
        $strUser = '';
        $arrUser = [];
        if ($query) {
            foreach($query as $key => $value){
                $arrUser[] = $key;
            }
        }
        $strUserId = $this->get_user($arrUser);
        return $strUserId;
    }
    private function get_group_owner($strUser){
        $sql = "SELECT 
                    users.id,
                    users.username,
                    groups.name as group_name 
                FROM 
                    users
                JOIN 
                    groups ON groups.code = users.username
                JOIN 
                    account ON account.id = users.username AND account.is_active = 1
                WHERE users.id IN ($strUser)
                ";
        $frefix = 'get_group_owner_';
        $row = $this->getCacheUser($sql, $frefix);
        $data = [];
        if($row){
            foreach ($row as $key => $value) {
                $data[] = $key;
            }
        }
        return $data;
    }
    private function get_leader($strUser){
        $sql = "SELECT 
                    users.id,
                    users.username
                FROM 
                    users
                JOIN 
                    account_group ON account_group.admin_user_id = users.id
                JOIN 
                    account ON account.id = users.username AND account.is_active = 1
                WHERE users.id IN ($strUser)
                ";
        $frefix = 'get_leader_';
        $row = $this->getCacheUser($sql, $frefix);
        $data = [];
        if($row){
            foreach ($row as $key => $value) {
                $data[] = $key;
            }
        }
        return $data;
    }
    private function get_admin($strUser){
        $sql = "SELECT 
                    users.id,
                    users.username
                FROM 
                    users
                JOIN 
                    account ON account.id = users.username AND account.is_active = 1
                WHERE users.id IN ($strUser)
                AND account.admin_group = 1
                ";
        $frefix = 'get_admin_';
        $row = $this->getCacheUser($sql, $frefix);
        $data = [];
        if($row){
            foreach ($row as $key => $value) {
                $data[] = $key;
            }
        }
        return $data;
    }
    private function get_user_privilege($strUser,$arrUser){
        $roleFail = $this->get_role_fail(); 
        $codeAll = $this->get_role_all();
        $frefix = 'get_user_privilege_';
        $sql = "SELECT 
                        *
                    FROM 
                        roles_to_privilege
                    JOIN 
                        users_roles ON users_roles.role_id = roles_to_privilege.role_id
                    WHERE 
                        users_roles.user_id IN ($strUser)
                    ";
        $row = $this->getCacheUser($sql, $frefix);
        $newCode = [];
        foreach ($codeAll as $kCode => $valCode) {
            $newCode[] = $valCode['code'];
        }
        $codeDiff = array_diff($newCode,$roleFail);
        $codeDiffNew = [];
        foreach ($codeDiff as $valDiff) {
            $codeDiffNew[$valDiff] = $valDiff;
        }
        $newArr = [];
        foreach ($row as $k => $v) {
            $newArr[$v['user_id']][$v['privilege_code']] =  $v['privilege_code'];
            if(isset($codeDiffNew[$v['privilege_code']])){
                $newArr[$v['user_id']]['CHECK_CODE'] =  1;
            }
        }
        $arr = [];
        foreach ($newArr as $kNew => $valNew) {
            if(!empty($valNew['QUYEN_GIAM_SAT'])){
                $count = count($valNew);
                if($count > 1){
                    foreach ($valNew as $kV => $valV) {
                       if(in_array($kV, $roleFail) && empty($valNew['CHECK_CODE'])){
                            $arr[] = $kNew;
                       }
                    } 
                } elseif ($count == 1) {
                    $arr[] = $kNew;
                } 
            }
        }
        $arrUserFail = array_unique($arr);
        $arrUserTrue = array_diff($arrUser,$arrUserFail);
        return $arrUserTrue;
    }
    private function get_user($arrUser){
        $strUser = '';
        if(!empty($arrUser)){
            $strUser = implode(',', $arrUser);
        }
        $userOwner = $this->get_group_owner($strUser);
        $userLeader = $this->get_leader($strUser);
        $userAdmin = $this->get_admin($strUser);
        $userTrueAll = array_unique(array_merge($userOwner,$userLeader,$userAdmin));
        $userFailAll = array_diff($arrUser,$userTrueAll);
        $strUserFailAll = '';
        if(!empty($userFailAll)){
            $strUserFailAll = implode(',', $userFailAll);
        }
        $userPrivilege = array_unique($this->get_user_privilege($strUserFailAll,$userFailAll));
        $users = array_unique(array_merge($userOwner,$userLeader,$userAdmin,$userPrivilege));
        $str = '';
        if(!empty($users)){
            $str = implode(',', $users);
        }
        return $str;
    }

    private function getCacheUser($sql, $frefix){
        if(System::is_local()) {
            return DB::fetch_all($sql);
        }

        $cache_key = $frefix . md5($sql);
        if($cache = MC::get_items($cache_key)){
            return json_decode($cache, true);
        }

        if($cache = DB::fetch_all($sql)){
            MC::set_items($cache_key, json_encode($cache), time() + 600); // cache 10 phut 
        }

        return $cache;
    }

    private function get_role_fail()
    {
        $arr = [
            'SUA_DON_NHANH',
            'BC_BXH_VINH_DANH',
            'QUYEN_GIAM_SAT',
            'CUSTOMER',
            'BC_DOANH_THU_NV',
            'BUNGDON2',
            'BC_DOANH_THU_MKT',
            'cs',
            'HCNS',
            'BUNGDON_NHOM',
            'BUNGDON',
            'ADMIN_KHO',
            'ADMIN_CS'
        ];
        return $arr;
    }
    private function get_role_all()
    {
        $code_fail = $this->get_role_fail();
        $str_code_fail = "('" . implode("','", $code_fail) . "')";
        $sql = "SELECT id,code FROM roles_activities WHERE code NOT IN $str_code_fail";
        if(System::is_local()) {
            return DB::fetch_all($sql);
        }

        $cache_key = 'get_role_all_' . md5($sql);
        if($cache = MC::get_items($cache_key)){
            return json_decode($cache, true);
        }

        if($cache = DB::fetch_all($sql)){
            MC::set_items($cache_key, json_encode($cache), time() + 600); // cache 10 phut 
        }

        return $cache;
    }
    /**
     * { function_description }
     */
    private function render_company_chart($system_group_id,$start_time,$end_time)
    {
        $groups = $this->get_groups_statistic($system_group_id,$start_time,$end_time);
        $datagroups = array();
        foreach ($groups as $k=>$item){
            $expired = false;
            // kiểm tra tài khoản trong khoảng lọc đã hết hạn chưa
            if(strtotime($item['expired_date']) < time()){
                $item['name'] .=' (Hết hạn)';
                $expired = true;
            }
            // Chỉ show cty nếu chưa hết hạn hoặc hết hạn nhưng có 1 trong các tổng
            if(!$expired){
                $datagroups[$k]= $item;
            }
        }
        $this->map['groups'] = $datagroups;
        
        
        return $this->parse_layout('system_dashboard_ajax',$this->map);
    }

    /**
     * Gets the group statistic.
     */
    private function get_group_statistic()
    {   
        $date_from = date('Y-m-d',Date_Time::to_time(URL::get('date_from')));
        $date_to = date('Y-m-d',Date_Time::to_time(URL::get('date_to')));
        $groupIDs = URL::get('ID');
        $rawGroupIDs = implode(',', $groupIDs);
        $status_id = Url::get('status_id');
        $rawSystemmStatues = implode(',', $this->getSystemStatuses(true));
        $status_no_revenue = DashboardDB::getStatusLevelHaveRevenue($rawGroupIDs);
        $level_no_revenue = DashboardDB::getStatusLevelHaveRevenue($rawGroupIDs, true);
        $groups = [];
        //$usersFail = $this->get_user_quyen_giam_sat();
        $arr_status = [CHUYEN_HOAN,TRA_VE_KHO];
        $arr_status_chuyen_hoan = [THANH_CONG,DA_THU_TIEN];
        foreach ($groupIDs as $groupID) {
            $groups[$groupID] = ['id' => $groupID];
            $groups[$groupID]['no_revenue_status'] = $rawSystemmStatues;
            $groups[$groupID]['no_revenue_status'] .=  !empty($status_no_revenue[$groupID]['sid']) ? ',' . $status_no_revenue[$groupID]['sid'] : '';

            $groups[$groupID]['level_no_revenue'] = $rawSystemmStatues;
            $groups[$groupID]['level_no_revenue'] .= !empty($level_no_revenue[$groupID]['sid']) ? ',' . $level_no_revenue[$groupID]['sid'] : '';
        }


        $sum_all = [];
        $sum_order = [];
        $sum_phone = [];
        foreach($groups as $groupID=>$group){
            //----------------------------
            if($status_id==THANH_CONG){
                $no_revenue_status = array_unique(explode(',',$group['no_revenue_status']));
                $no_level_status = array_unique(explode(',',$group['level_no_revenue']));
                foreach ($no_revenue_status as $k => $val) {
                    $no_revenue_status[$k] = intval($val);
                }
                $diff = array_diff($no_revenue_status, $arr_status);
                $group['no_revenue_status'] = implode(',', $diff);

                foreach ($no_level_status as $k => $val) {
                    $no_level_status[$k] = intval($val);
                }
                $diff = array_diff($no_level_status, $arr_status);
                $group['level_no_revenue'] = implode(',', $diff);
            } else if ($status_id==CHUYEN_HOAN){
                $no_revenue_status = array_unique(explode(',',$group['no_revenue_status']));
                $no_level_status = array_unique(explode(',',$group['level_no_revenue']));
                foreach ($no_revenue_status as $k => $val) {
                    $no_revenue_status[$k] = intval($val);
                }
                $diff = array_diff($no_revenue_status, $arr_status_chuyen_hoan);
                $group['no_revenue_status'] = implode(',', $diff);

                foreach ($no_level_status as $k => $val) {
                    $no_level_status[$k] = intval($val);
                }
                $diff = array_diff($no_level_status, $arr_status_chuyen_hoan);
                $group['level_no_revenue'] = implode(',', $diff);
            }

            $total_phone_cond1 = 'orders.group_id=' . $groupID;
            $total_phone_cond1 .= ' and orders.created>="' . $date_from . ' 00:00:00" and  orders.created<="' . $date_to . ' 23:59:59"';
            $total_phone_sql1 = '
                    SELECT 
                            '.$groupID.' as id,
                            COUNT(*) as total, 
                            COALESCE(SUM(IF(orders.type > 1  AND orders.type IS NOT NULL AND orders.type != 9, 1, 0)), 0) AS cskh, 
                            COALESCE(SUM(IF(orders.type = 1, 1, 0)), 0) AS somoi,
                            COALESCE(SUM(IF(orders.type = 9, 1, 0)), 0) AS toiuu
                    FROM
                            orders
                    WHERE
                    ' . $total_phone_cond1;
            $sum_phone[] = $this->get_statistics($total_phone_sql1);

            //----------------------------
            $total_order_cond = 'orders.group_id=' . $groupID;
            $total_order_cond .= ' and orders.confirmed>="' . $date_from . ' 00:00:00" and  orders.confirmed<="' . $date_to . ' 23:59:59"  and orders.status_id IN (' . $group['level_no_revenue'] . ')';
            $join = ($status_id==THANH_CONG ||  $status_id==CHUYEN_HOAN? 'LEFT JOIN orders_extra ON orders_extra.order_id=orders.id' : '');
            if($status_id == THANH_CONG){
                $total_order_cond .= ' and orders_extra.update_successed_time != "0000-00-00 00:00:00" and  orders_extra.update_successed_time IS NOT NULL';
            }
            if($status_id == CHUYEN_HANG){
                $total_order_cond .= ' and orders.delivered != "0000-00-00 00:00:00" and  orders.delivered IS NOT NULL';
            }

            if($status_id == CHUYEN_HOAN){
                $total_order_cond .= ' and orders_extra.update_returned_time != "0000-00-00 00:00:00" and  orders_extra.update_returned_user IS NOT NULL';
            }
            $total_order_sql = '
                    SELECT  
                            '.$groupID.' as id,
                            count(*) as total, 
                            COALESCE(SUM(IF(orders.type > 1  AND orders.type IS NOT NULL AND orders.type != 9, 1, 0)), 0) AS cskh, 
                            COALESCE(SUM(IF(orders.type = 1, 1, 0)), 0) AS somoi,
                            COALESCE(SUM(IF(orders.type = 9, 1, 0)), 0) AS toiuu
                    FROM
                            orders
                            ' . $join . '
                    WHERE
                    ' . $total_order_cond;
            $sum_order[] = $this->get_statistics($total_order_sql);

            // ------------------------------
            $cond = 'orders.group_id=' . $groupID . '';
            $cond .= ' and orders.status_id IN (' . $group['no_revenue_status'] . ')';
            $cond .= ' and orders.confirmed>="' . $date_from . ' 00:00:00" and  orders.confirmed<="' . $date_to . ' 23:59:59"';
            $join = ($status_id==THANH_CONG ||  $status_id==CHUYEN_HOAN? 'LEFT JOIN orders_extra ON orders_extra.order_id=orders.id' : '');
            if($status_id == THANH_CONG){
                $cond .= ' and orders_extra.update_successed_time != "0000-00-00 00:00:00" and  orders_extra.update_successed_time IS NOT NULL';
            }

            if($status_id == CHUYEN_HANG){
                $cond .= ' and orders.delivered != "0000-00-00 00:00:00" and  orders.delivered IS NOT NULL';
            }
            if($status_id == CHUYEN_HOAN){
                $cond .= ' and orders_extra.update_returned_time != "0000-00-00 00:00:00" and  orders_extra.update_returned_user IS NOT NULL';
            }
            // var_dump($cond);
            $sql = '
                    SELECT 
                            '.$groupID.' as id,
                            COALESCE(SUM(total_price), 0) AS total, 
                            COALESCE(SUM(IF(orders.type > 1  AND orders.type IS NOT NULL AND orders.type != 9, total_price, 0)), 0) AS cskh, 
                            COALESCE(SUM(IF(orders.type = 1, total_price, 0)), 0) AS somoi,
                            COALESCE(SUM(IF(orders.type = 9, total_price, 0)), 0) AS toiuu
                    FROM
                            orders
                            ' . $join . '
                    WHERE
                    ' . $cond;
            $sum_all[] = $this->get_statistics($sql);
        }
        return [
            'total' => $sum_all,
            'order' => $sum_order,
            'phone' => $sum_phone
        ];
    }

    /**
     * Gets the statistics.
     *
     * @param      string  $sql    The sql
     *
     * @return     <type>  The statistics.
     */
    private function get_statistics(string $sql)
    {   
        if(System::is_local()) {
            return DB::fetch($sql);
        }

        $cache_key = 'groups_statistics_' . md5($sql);
        if($cache = MC::get_items($cache_key)){
            return json_decode($cache);
        }

        if($cache = DB::fetch($sql)){
            MC::set_items($cache_key, json_encode($cache), time() + 600); // cache 10 phut 
        }

        return $cache;
    }
}
?>