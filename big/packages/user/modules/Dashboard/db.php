<?php

class DashboardDB
{
    static function merAccountGroup(){
        $get_account_in_group = explode(',', get_account_id());
        $get_account_group_ids = explode(',', get_account_group_ids());
        $accountIds = array_merge($get_account_in_group,$get_account_group_ids);
        $accountIds = array_unique($accountIds);
        $strAccountIds = implode(',', $accountIds);
        return $strAccountIds;
    }
    static function checkUserOnlyRole($privilege_code){
         $user_id = get_user_id();
         $sql = "SELECT * FROM 
                roles_to_privilege 
            LEFT JOIN 
                users_roles ON users_roles.role_id = roles_to_privilege.role_id
            WHERE 
                users_roles.user_id = $user_id
            ";
        $results = DB::fetch_all($sql);
        if($results){
            $array = [];
            foreach ($results as $key => $value) {
                $array[] = $value['privilege_code'];
            }
            if(!empty($array) && sizeof($array) == 1 && in_array($privilege_code, $array)){
                return true;
            }
            return false;
        }
        return false; 
    }
    static function getUserNew($code=false,$isActive=false)
    {
        $group_id = Session::get('group_id');
        $cond = '';
        if(self::getViewRole()){
            $cond = '';
        }else if(self::getKhoiBaoCaoMKT()){
            $cond = '';
        }else if(self::getKhoiBaoCaoSale()){
            $cond = '';
        }else if(self::getKhoiBaoCaoTrucPage()){
            $cond = '';
        }else if(self::getKhoiBaoCaoChung()){
            $cond = '';
        } else if(is_account_group_department() && get_account_group_ids() && !Dashboard::$quyen_xem_bc_doi_nhom){
            $cond.= ' AND account.account_group_id IN ('.get_account_group_ids().')';
        } else if(Dashboard::$quyen_xem_bc_doi_nhom && get_account_id()){
            if(!is_account_group_manager()){
                $cond.= ' AND account.account_group_id = '.get_account_id().' ';
            } else if(is_account_group_manager() && get_account_group_ids() && ( !is_account_group_department() || is_account_group_department() )){
                $cond.= ' AND account.account_group_id IN ('.self::merAccountGroup().')';
            }
        } else if(is_account_group_manager()){
            $cond .= ' AND account_group.admin_user_id = ' . get_user_id() .' ';
        } else if(Dashboard::$quyen_marketing || Dashboard::$quyen_sale){
            $cond .= ' AND account.id = "' . Session::get('user_id') . '"';
        }

        if($isActive == false){
            $cond .= ' AND account.is_active = 1';
        } else if($isActive == '1'){
            $cond .= ' AND (account.is_active = 0 OR account.is_active IS NULL)';
        }
        if($code){
            $cond .= ' AND roles_to_privilege.privilege_code = "'.$code.'" ';
        }
        if(Url::get('account_group_id')){
            $cond .= ' AND account.account_group_id = '.DB::escape(Url::get('account_group_id'));
        }

        $sql = '
            SELECT 
                users.id as id,
                users.id as user_id,
                users.group_id,
                users.name as full_name,
                account.id as username
            FROM `account` 
            LEFT JOIN account_group ON account_group_id = account_group.id 
            JOIN users ON users.username = account.id
            LEFT JOIN users_roles ON  users_roles.user_id = users.id
            LEFT JOIN roles ON roles.id = users_roles.role_id
            LEFT JOIN roles_to_privilege ON roles_to_privilege.role_id = roles.id
            WHERE account.group_id = ' . $group_id . $cond .' '
                ;
        $results = DB::fetch_all($sql);
        return $results;
    }
    static function getKhoiBaoCaoMKT(){
        $array = [
            'adv_money','doanh_thu_mkt','doanh_thu_upsale','adv_money_new','kho_so_mkt'
        ];
        if (in_array(Url::get('do'), $array) && Dashboard::$xem_khoi_bc_marketing) {
            return true;
        }
        return false;
    }
    static function getKhoiBaoCaoTrucPage(){
        $array = [
            'kho_so','order_action'
        ];
        if (in_array(Url::get('do'), $array) && Dashboard::$xem_khoi_bc_truc_page) {
            return true;
        }
        return false;
    }
    static function getKhoiBaoCaoSale(){
        $array = [
            'doanh_thu_nv','kho_so_sale','report','ty_le_chot_don','tong_hop_sale'
        ];
        if (in_array(Url::get('do'), $array) && Dashboard::$xem_khoi_bc_sale) {
            return true;
        }
        return false;
    }
    static function getKhoiBaoCaoChung(){
        $array = [
            'transport'
        ];
        if (in_array(Url::get('do'), $array) && Dashboard::$xem_khoi_bc_chung) {
            return true;
        }
        return false;
    }
    static function getUserNew2($code=false,$isActive=false)
    {
        $group_id = Session::get('group_id');
        $cond = '';

        if (is_group_owner() || User::is_admin() || check_user_privilege('KE_TOAN') || check_user_privilege('VAN_DON') || Dashboard::$xem_khoi_bc_sale) {
            $cond = '';
        } else if (Dashboard::$is_account_group_manager){
            $user_id = get_user_id();
            $account_group_id = DB::fetch('select id from account_group where admin_user_id='.$user_id, 'id');
            $cond .= ' AND account.account_group_id = '.$account_group_id;
        } else if (check_user_privilege('GANDON', false, false, true)){
            $cond .= ' AND account.id = "' . Session::get('user_id') . '"';
        } else {
            $cond .= ' AND 1 = 2';
        }

        if($isActive == false){
            $cond .= ' AND account.is_active = 1';
        } else if($isActive == '1'){
            $cond .= ' AND (account.is_active = 0 OR account.is_active IS NULL)';
        }
        if($code){
            $cond .= ' AND roles_to_privilege.privilege_code = "'.$code.'" ';
        }

        $sql = '
            SELECT 
                users.id as id,
                users.id as user_id,
                users.group_id,
                users.name as full_name,
                account.id as username
            FROM `account` 
            LEFT JOIN account_group ON account_group_id = account_group.id 
            JOIN users ON users.username = account.id
            LEFT JOIN users_roles ON  users_roles.user_id = users.id
            LEFT JOIN roles ON roles.id = users_roles.role_id
            LEFT JOIN roles_to_privilege ON roles_to_privilege.role_id = roles.id
            WHERE account.group_id = ' . $group_id . $cond .' '
                ;
        $results = DB::fetch_all($sql);
        return $results;
    }
    static function getUserSale($code=false,$isActive=false){
        $group_id = Session::get('group_id');
        $cond = '';
        if(!is_group_owner() && !Dashboard::$admin_group && is_account_group_department() && get_account_group_ids() && (Dashboard::$quyen_admin_marketing || Dashboard::$quyen_bc_doanh_thu_mkt)){
            $cond.= ' AND account.account_group_id IN ('.get_account_group_ids().')';
        }
        if($isActive == false){
            $cond .= ' AND account.is_active = 1';
        } else if($isActive == '1'){
            $cond .= ' AND (account.is_active = 0 OR account.is_active IS NULL)';
        }
        if($code){
            $cond .= ' AND roles_to_privilege.privilege_code = "'.$code.'" ';
        }
        if(Url::get('account_group_id')){
            $cond .= ' AND account.account_group_id = '.DB::escape(Url::get('account_group_id'));
        }
        $sql = '
            SELECT 
                users.id as id,
                users.id as user_id,
                users.group_id,
                users.name as full_name,
                account.id as username
            FROM `account` 
            LEFT JOIN account_group ON account_group_id = account_group.id 
            JOIN users ON users.username = account.id
            LEFT JOIN users_roles ON  users_roles.user_id = users.id
            LEFT JOIN roles ON roles.id = users_roles.role_id
            LEFT JOIN roles_to_privilege ON roles_to_privilege.role_id = roles.id
            WHERE account.group_id = ' . $group_id . $cond .' '
                ;
        $results = DB::fetch_all($sql);
        return $results;
    }
    static function getAccountSale(){
        $groups = [];
        $group_id = Session::get('group_id');
        $cond = '';
        if(!is_group_owner() && !Dashboard::$admin_group && is_account_group_department() && get_account_group_ids() && (Dashboard::$quyen_admin_marketing || Dashboard::$quyen_bc_doanh_thu_mkt)){
            $cond.= ' AND account.account_group_id IN ('.get_account_group_ids().')';
        }
        $sql = '
            SELECT 
                account_group.id,account_group.name 
            FROM 
                account_group 
            JOIN 
                `account` on `account`.account_group_id=account_group.id
            WHERE 
                account.group_id = ' . $group_id . $cond .' 
            ORDER BY
            account_group.name
        ';
        $groups = DB::fetch_all($sql);
        return $groups;
    }
    static function getViewRole(){
        if((is_group_owner()
           || Dashboard::$admin_group 
           || Dashboard::$quyen_bc_doanh_thu_nv
           || Dashboard::$quyen_admin_marketing 
           || Dashboard::$quyen_bc_doanh_thu_mkt
            )){
            return true;
        }
        return false;
    }
    static function getAccountGroup(){
        $groups = [];
        $group_id = Session::get('group_id');
        $cond = '';
        if(self::getViewRole()){
            $cond = '';
        }else if(self::getKhoiBaoCaoMKT()){
            $cond = '';
        }else if(self::getKhoiBaoCaoSale()){
            $cond = '';
        }else if(self::getKhoiBaoCaoTrucPage()){
            $cond = '';
        }else if(self::getKhoiBaoCaoChung()){
            $cond = '';
        } else if(is_account_group_department() && get_account_group_ids() && !Dashboard::$quyen_xem_bc_doi_nhom){
            $cond.= ' AND account_group.id IN ('.get_account_group_ids().')';
        } else if(Dashboard::$quyen_xem_bc_doi_nhom && get_account_id() && !is_account_group_manager()){
            $cond.= ' AND account_group.id = '.get_account_id().' ';
        } else if(Dashboard::$quyen_xem_bc_doi_nhom && get_account_id() && is_account_group_manager() && get_account_group_ids() && ( !is_account_group_department() || is_account_group_department() )){
            $cond.= ' AND account.account_group_id IN ('.self::merAccountGroup().')';
        } else if(is_account_group_manager()){
            $cond .= ' AND account_group.admin_user_id = ' . get_user_id() .' ';
        } else if((Dashboard::$quyen_sale || Dashboard::$quyen_marketing) && $account_group_id=DB::fetch('select account_group_id from account where account.id="'.Session::get('user_id').'"','account_group_id')){
            $cond .= ' AND account_group.id = '.$account_group_id.'';
        }
        $sql = '
            SELECT 
                account_group.id,account_group.name 
            FROM 
                account_group 
            JOIN 
                `account` on `account`.account_group_id=account_group.id
            WHERE 
                account.group_id = ' . $group_id . $cond .' 
            ORDER BY
            account_group.name
        ';
        $groups = DB::fetch_all($sql);
        return $groups;
    }
    static function checkMarketing(){
        $access = ['ADMIN_MARKETING','BC_DOANH_THU_MKT'];
        $check = self::checkRequire($access);
        return $check;
    }

    static function getUser(){
        $sql = 'SELECT 
                        users.id as id
                FROM `account` 
                LEFT JOIN account_group ON account_group_id = account_group.id 
                JOIN users ON users.username = account.id
                LEFT JOIN users_roles ON  users_roles.user_id = users.id
                LEFT JOIN roles ON roles.id = users_roles.role_id
                LEFT JOIN roles_to_privilege ON roles_to_privilege.role_id = roles.id
                WHERE account.group_id = '.Session::get('group_id').' 
                AND account.account_group_id = '.get_account_id().'  
                AND account.is_active = 1 
                AND roles_to_privilege.privilege_code = "GANDON"';
        $data = DB::fetch_all($sql);
        if($data){
            return array_keys($data);
        } 
        return [];
    }
    static function getGroup(){
         $sql = 'SELECT 
                account_group.id,account_group.name
            FROM 
                account_group 
            JOIN 
                `account` on `account`.account_group_id=account_group.id
            WHERE 
                account.group_id = '.Session::get('group_id').' AND account_group.id = '.get_account_id().'  
            ORDER BY
            account_group.name';
            $data = DB::fetch_all($sql);
            if($data){
                return $data;
            } 
            return [];
    }
    static function getDataOrderProduct($cond)
    {
        $sql = "SELECT  
                        orders_products.id,
                        orders_products.id as order_product_id,
                        orders_products.product_id,
                        orders_products.discount_amount,
                        orders_products.warehouse_id,
                        orders_products.qty,
                        orders_products.product_price,
                        products.price,
                        products.code,
                        products.name,
                        qlbh_warehouse.name as warehouse_name,
                        groups.name as group_name,
                        orders.status_id,
                        units.name as unit_name,
                        labels.name as lable_name,
                        bundles.name as bundle_name
                    FROM 
                        orders_products
                    JOIN
                        products ON products.id = orders_products.product_id
                    JOIN 
                        orders ON orders.id = orders_products.order_id
                    JOIN  
                        groups ON groups.id = orders.group_id
                    LEFT JOIN  
                        labels ON labels.id = products.label_id
                    LEFT JOIN 
                        bundles ON bundles.id = products.bundle_id
                    JOIN  
                        qlbh_warehouse ON qlbh_warehouse.id = orders_products.warehouse_id
                    LEFT JOIN
                        units ON units.id = products.unit_id
                    WHERE
                        ". $cond ."
                    ORDER BY 
                        orders_products.id
                        ";
        $data = DB::fetch_all($sql);
        foreach ($data as $key => $value) {
            $discount_amount = $value['discount_amount']/$value['qty'];
            $after_discount = $value['product_price'] - $discount_amount;
            $data[$key]['discount_amount']  = $discount_amount;
            $data[$key]['after_discount']  = $after_discount;
        }
        return self::mergeDataOrderProduct($data);
    }
    static function mergeDataOrderProduct($data)
    {
        $newArry = [];
        foreach ($data as $product) {
            $key = sprintf('%d-%d-%d-%d', $product['product_id'],$product['discount_amount'],$product['after_discount'],$product['warehouse_id']);
            if (isset($newArry[$key])) {
                $newArry[$key]['qty'] += $product['qty'];
                $newArry[$key]['discount_amount'] = $product['discount_amount'];
                $newArry[$key]['after_discount'] = $product['after_discount'];
                continue;
            }
            $newArry[$key] = $product;
        }
        return $newArry;
    }
    static function getDefaultAvatar(){
        $imageDefault = 'assets/vissale/BXHHTML/images/tuha.png?v=03122021';
        return $imageDefault;
    }

    static function getArrayAvatar(){
        $arrayAvatar = [
            '1'=>['url'=>'assets/vissale/BXHHTML/images/img-1.png'],
            '2'=>['url'=>'assets/vissale/BXHHTML/images/img-2.png'],
            '3'=>['url'=>'assets/vissale/BXHHTML/images/img-3.png'],
            '4'=>['url'=>'assets/vissale/BXHHTML/images/img-4.png'],
            '5'=>['url'=>'assets/vissale/BXHHTML/images/img-5.png'],
        ];
        return $arrayAvatar;
    }
    static function get_revenue_status_update($group_id=false){
        $group_id = $group_id?$group_id:Dashboard::$group_id;
        $master_group_id = Dashboard::$master_group_id;
        $revenue_status = DB::fetch_all('SELECT id FROM statuses WHERE (no_revenue = 0 OR  no_revenue IS NULL ) AND (('.(($master_group_id)?'group_id = '.$master_group_id.'':'group_id = '.$group_id.'').') OR statuses.is_system=1)');
        return $revenue_status;
    }
    static function checkPermissionBXHReport(){ 
        $access = ['BC_BXH_VINH_DANH'];
        $check = self::checkRequire($access);
        return $check;
    }
    static function checkRequire($access){
        $user_id = get_user_id();
        $strAccess = "('" . implode("','", $access) . "')";
        $check = [];
        if($user_id){
            $sql = "SELECT 
                        roles_to_privilege.id
                    FROM 
                        users_roles 
                    JOIN 
                        roles_to_privilege ON users_roles.role_id = roles_to_privilege.role_id 
                    WHERE 
                        roles_to_privilege.privilege_code 
                    IN  
                        $strAccess 
                    AND 
                        users_roles.user_id = $user_id";
            $check = DB::fetch_all($sql);
        }
        if (Session::get('admin_group') || is_group_owner() || sizeof($check)>0) {
            return true;
        }
        return false;
    }
    static function getTotalOrderByCond($cond = []){
        return DB::fetch("
            SELECT 
                COUNT(orders.id) AS total 
            FROM 
                orders
            WHERE " . implode(" ", $cond),
            "total"
        );
    }

    static function getTotalRevenueOrderByCond($cond = []){
        return DB::fetch("
            SELECT 
                SUM(orders.total_price) AS total 
            FROM 
                orders
            WHERE 
                " . implode(" ", $cond),
            "total"
        );
    }
    static function get_color_by_rate($value){
        $color = '';
        if($value>0){
            if($value>=80){
                $color = '#7fff00';
            }elseif($value>=60){
                $color = '#00ae9c';
            }elseif($value>=50){
                $color = '#f79406';
            }elseif($value>=30){
                $color = '#ed5d5d';
            }elseif($value>0){
                $color = '#d90000';
            }
        }
        return $color;
    }
    static function get_ads_warning_color($cost){
        $danger = Dashboard::$mkt_cost_per_revenue_danger;
        $danger = $danger?$danger:35;
        $warning = Dashboard::$mkt_cost_per_revenue_warning;
        $warning = $warning?$warning:30;
        if($cost>=$danger){
            $color = '#ff4b4b';
        }elseif($cost>=$warning and $cost<$danger){
            $color = '#fcbd0b';
        }elseif($cost>0){
            $color = '#1ef693';
        }else{
            $color = '#FFF';
        }
        return $color;
    }
    static function get_zones(){
        $group_id = Dashboard::$group_id;
        $items = DB::fetch_all('
            SELECT 
                zone_provinces_v2.province_id as id,
                zone_provinces_v2.province_name as name,
                0 as qty,
                0 as total_amount
            FROM 
            `zone_provinces_v2` 
            ORDER BY `zone_provinces_v2`.`province_id` ASC
        ');
        return $items;
    }
    static function get_warehouses(){
        $sql = 'select 
                qlbh_warehouse.id,
                qlbh_warehouse.name
                
            from 
                qlbh_warehouse
            where
                 qlbh_warehouse.group_id='.Dashboard::$group_id.' OR structure_id='.ID_ROOT.'           
            order by
                qlbh_warehouse.is_default desc,qlbh_warehouse.name
        ';
        return $items = DB::fetch_all($sql);
    }
    static function check_remote_file($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        // don't download content
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);
        if($result !== FALSE)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    static function get_user_rank($start_time,$end_time,$code='GANDON'){
        $group_id = Dashboard::$group_id;
        $sql =('
            SELECT
                users.id,users.name,users.group_id,
                groups.name as group_name,
                groups.expired_date,
                (select name from account_group where account_group.id=account.account_group_id limit 0,1) account_group_name,
                party.image_url as avatar
            FROM
              users
                JOIN account on account.id=users.username
                JOIN party ON party.user_id = account.id
                JOIN `groups` on groups.id = users.group_id
                JOIN users_roles ON users_roles.user_id = users.id
                JOIN roles_to_privilege ON roles_to_privilege.role_id = users_roles.role_id
            WHERE
                account.is_active
                AND (groups.expired_date > "'.DB::escape($start_time).'" or groups.expired_date="'.Dashboard::$date_init_value.'")
                AND roles_to_privilege.privilege_code="'.DB::escape($code).'"
                AND groups.id='.$group_id.'
        ');
        $users = DB::fetch_all($sql);
        $no_revenue_status = DashboardDB::get_no_revenue_status();
        foreach ($users as $key => $value){
            $cond = 'orders.group_id=' . $value['group_id'] . '';
            $cond .= ' and orders.status_id NOT IN (' . $no_revenue_status . ')';
            $qty_cond = 'orders.group_id=' . $value['group_id'] . ' and orders.status_id NOT IN (' . $no_revenue_status . ')';
            if($code=='MARKETING'){
                $cond .= ' and orders.user_created='.$value['id'].' and orders.confirmed>="' . $start_time . ' 00:00:00" and  orders.confirmed<="' . $end_time . ' 23:59:59"';
                $qty_cond .=  ' and orders.user_created='.$value['id'].' and orders.confirmed>="' . $start_time . ' 00:00:00" and  orders.confirmed<="' . $end_time . ' 23:59:59"';
            }else{
                $cond .= ' and orders.user_confirmed='.$value['id'].' and orders.confirmed>="' . $start_time . ' 00:00:00" and  orders.confirmed<="' . $end_time . ' 23:59:59"';
                $qty_cond .= ' and orders.user_assigned='.$value['id'].' and orders.assigned>="' . $start_time . ' 00:00:00" and  orders.assigned<="' . $end_time . ' 23:59:59"';
            }
            if($status_id = Url::get('status_id')){
                if($status_id==THANH_CONG){
                    $cond .= ' and orders_extra.update_successed_time<>"0000-00-00 00:00:00"';
                    $sql = '
                        SELECT
                                total_price AS total
                        FROM
                                orders
                                JOIN orders_extra ON orders_extra.order_id=orders.id
                        WHERE
                        ' . $cond;
                    $qty_sql = '
                         SELECT
                                orders.id
                        FROM
                                orders
                                JOIN orders_extra ON orders_extra.order_id=orders.id
                        WHERE
                        ' . $qty_cond;
                }else{
                    $sql = 'select total_price as total from orders where ' . $cond;
                    $qty_sql = 'select id from orders where ' . $qty_cond;
                }
            }else{
                $sql = 'select total_price as total from orders where ' . $cond;
                $qty_sql = 'select id from orders where ' . $qty_cond;
            }
            $total_price = DashboardDB::get_total_amount($sql);
            $total_qty = DashboardDB::get_total_item($qty_sql);
            $users[$key]['total'] = $total_price ? $total_price/1000000 : 0;
            $users[$key]['qty'] = $total_qty;
            $users[$key]['ty_le_chot'] = ($total_qty>0)?( round(($total_price/1000000)/$total_qty,0) )*100:'';
            if($total_price<=0){
                
            }
        }
        if (sizeof($users) > 1) {
            System::sksort($users, 'total', 'DESC');
        }
        return $users;
    }
    static function get_user_statistic($system_group_id, $start_time,$end_time,$code='GANDON',$view_type=false, $group_ids = [])
    {
        if(!$system_group_id){
            $user_id = get_user_id();
            $all_system_group_id= DB::fetch('select id,system_group_id from groups_system_account where user_id='.$user_id.' limit 0,1','system_group_id');
        }
        $sql ='
            SELECT
                users.id,users.name,users.group_id,
                groups.name as group_name,
                groups.expired_date,
                users.rated_point,
                users.rated_quantity
            FROM
              users
                JOIN account on account.id=users.username
                JOIN `groups` on groups.id = users.group_id
                JOIN groups_system ON groups_system.id = groups.system_group_id
                JOIN users_roles ON users_roles.user_id = users.id
                JOIN roles_to_privilege ON roles_to_privilege.role_id = users_roles.role_id
            WHERE
                account.is_active
                AND (groups.expired_date > "'.$start_time.'" or groups.expired_date=NULL)
                AND '.($system_group_id?(!$view_type?IDStructure::child_cond(DB::structure_id('groups_system',$system_group_id)):'groups_system.id='.$system_group_id):IDStructure::child_cond(DB::structure_id('groups_system',$all_system_group_id))).'
                AND roles_to_privilege.privilege_code="'.$code.'"
        ';

        if($group_ids){
            $sql .= sprintf(' AND groups.id IN (%s)', implode(',', $group_ids));
        }
        $users = DB::fetch_all($sql);
        $groupIDs = implode(',', array_unique(array_column($users, 'group_id')));
        $revenue_status = DashboardDB::getStatusLevelHaveRevenue($groupIDs);
        $rawSystemmStatues = implode(',', self::getSystemStatuses(true));
        $arr_status = [CHUYEN_HOAN,TRA_VE_KHO];
        foreach ($users as $key => $value){
            $cond = 'orders.group_id=' . $value['group_id'] . '';
            $status = $rawSystemmStatues . ($revenue_status[$value['group_id']]['sid'] ? ',' . $revenue_status[$value['group_id']]['sid'] : '');
            if($status_id = Url::get('status_id')){
                if($status_id==THANH_CONG){
                    $no_revenue_status = array_unique(explode(',',$status));
                    foreach ($no_revenue_status as $k => $val) {
                        $no_revenue_status[$k] = intval($val);
                    }
                    $diff = array_diff($no_revenue_status, $arr_status);
                    $status = implode(',', $diff);
                }
            }
            $cond .= ' and orders.status_id IN (' . $status . ')';
            if($code=='MARKETING'){
                $cond .= ' and orders.user_created='.$value['id'].' and orders.confirmed>="' . $start_time . ' 00:00:00" and  orders.confirmed<="' . $end_time . ' 23:59:59" AND orders.status_id<>'.HUY;
            }else{
                $cond .= ' and orders.user_confirmed='.$value['id'].' and orders.confirmed>="' . $start_time . ' 00:00:00" and  orders.confirmed<="' . $end_time . ' 23:59:59" AND orders.status_id<>'.HUY;
            }
            if($status_id = Url::get('status_id')){
                if($status_id==THANH_CONG){
                    $cond .= ' and orders_extra.update_successed_time<>"0000-00-00 00:00:00"';
                    $sql = '
                        SELECT
                                total_price AS total
                        FROM
                                orders
                                JOIN orders_extra ON orders_extra.order_id=orders.id
                        WHERE
                        ' . $cond;
                    $qty_sql = '
                        SELECT
                                orders.id
                        FROM
                                orders
                                JOIN orders_extra ON orders_extra.order_id=orders.id
                        WHERE
                        ' . $cond;
                }else{
                    $sql = 'select total_price as total from orders where ' . $cond;
                    $qty_sql = 'select id from orders where ' . $cond;
                }
            }else{
                $sql = 'select total_price as total from orders where ' . $cond;
                $qty_sql = 'select id from orders where ' . $cond;
            }
            $total_price = DashboardDB::get_total_amount($sql);
            $users[$key]['total'] = $total_price ? $total_price/1000000 : 0;
            $users[$key]['qty'] = DashboardDB::get_total_item($qty_sql);
            if($total_price<=0){
                unset($users[$key]);
            }
            //
        }
        if (sizeof($users) > 1) {
            System::sksort($users, 'total', 'DESC');
        }
        return $users;
    }
    static function get_revenue_by_month($group_id){
        $group_id = $group_id?$group_id:Dashboard::$group_id;
        $no_revenue_status = get_no_revenue_status($group_id);
        $sql = '
            SELECT 
                MONTH(confirmed) as id, MONTH(confirmed) as mon, SUM(total_price) AS total_amount
            FROM 
                orders
            WHERE 
                YEAR(confirmed) = '.date('Y').'
                AND group_id = '.$group_id.'
                AND orders.status_id NOT IN ('.$no_revenue_status.')
            GROUP BY MONTH(confirmed)
        ';
        if (!System::is_local()) {
            $m_key = md5($sql);
            if(!$revenue_by_months  = MC::get_items($m_key)){
                $revenue_by_months = DB::fetch_all($sql);
                if(sizeof($revenue_by_months)<12){
                    for($i=1;$i<=12;$i++){
                        if(!isset($revenue_by_months[$i])){
                            $revenue_by_months[$i]['id'] = $i;
                            $revenue_by_months[$i]['mon'] = $i;
                            $revenue_by_months[$i]['total_amount'] = 0;
                        }
                    }
                }
                MC::set_items($m_key, $revenue_by_months, time() + 60*10);
            }
        }else{
            $revenue_by_months = DB::fetch_all($sql);
            if(sizeof($revenue_by_months)<12){
                for($i=1;$i<=12;$i++){
                    if(!isset($revenue_by_months[$i])){
                        $revenue_by_months[$i]['id'] = $i;
                        $revenue_by_months[$i]['mon'] = $i;
                        $revenue_by_months[$i]['total_amount'] = 0;
                    }
                }
            }
        }
        return $revenue_by_months;
    }
    static function get_total_item($sql){
        $total = 0;
        if($sql){
            $m_key = md5($sql);
            if (!System::is_local()) {
                if(!$total=MC::get_items($m_key)){
                    DB::query($sql);
                    $qr = DB::$db_result;
                    $total = 0;
                    if(isset($qr->num_rows)){
                        $total = $qr->num_rows;
                    }
                    MC::set_items($m_key, $total, time() + 60*10);
                }
            }else{
                DB::query($sql);
                $qr = DB::$db_result;
                $total = 0;
                if(isset($qr->num_rows)){
                    $total = $qr->num_rows;
                }
            }
        }
        return $total;
    }
    static function get_total_amount($sql){
        $total = 0;
        if($sql){
            $m_key = md5($sql);
            if (!System::is_local()) {
                if(!$total=MC::get_items($m_key)){
                    DB::query($sql);
                    $qr = DB::$db_result;
                    $total = 0;
                    if ($qr){
                        while($row = mysqli_fetch_row($qr)){
                            $total += $row[0];
                        }
                    }
                    MC::set_items($m_key, $total, time() + 60*10);
                }
            }else{
                DB::query($sql);
                $qr = DB::$db_result;
                $total = 0;
                if ($qr){
                    while($row = mysqli_fetch_row($qr)){
                        $total += $row[0];
                    }
                }
            }
        }
        return $total;
    }
    static function getOrderProductsCskh($cond = "")
    {
        $THANH_CONG = 5;
        $CHUYEN_HANG = 8;
        $group_id = Dashboard::$group_id;
        $sql = "
            SELECT
                op.id, op.product_name, SUM(op.qty * op.product_price - op.discount_amount) AS doanh_thu, COUNT(o.id) AS total_order, AVG(IFNULL(op.staff_rating, 0)) AS staff_rate, AVG(IFNULL(op.customer_rating, 0)) AS customer_rate,
                o.type, (SELECT SUM(op_temp.qty * op_temp.product_price - op_temp.discount_amount) FROM orders_products AS op_temp JOIN orders AS o_temp ON o_temp.id = op_temp.order_id WHERE op_temp.id = op.id AND o_temp.type > 2) AS doanh_thu_cskh
            FROM orders_products AS op
            JOIN orders AS o ON o.id = op.order_id
            WHERE o.group_id = $group_id AND (o.status_id = $THANH_CONG or o.status_id = $CHUYEN_HANG) $cond
            GROUP BY op.product_id
            ORDER BY o.id DESC, op.id DESC
        ";

        return DB::fetch_all($sql);
    }

    static function getOrderProductsCskhOther($cond = "")
    {
        $THANH_CONG = 5;
        $CHUYEN_HANG = 8;
        $group_id = Dashboard::$group_id;
        $sql = "
            SELECT
                op.id, op.product_name, (op.qty * op.product_price - op.discount_amount) AS doanh_thu, (IFNULL(op.staff_rating, 0)) AS staff_rate, (IFNULL(op.customer_rating, 0)) AS customer_rate, op.product_id,
                o.type, (SELECT SUM(op_temp.qty * op_temp.product_price - op_temp.discount_amount) FROM orders_products AS op_temp JOIN orders AS o_temp ON o_temp.id = op_temp.order_id WHERE op_temp.id = op.id AND o_temp.type > 2) AS doanh_thu_cskh
            FROM orders_products AS op
            JOIN orders AS o ON o.id = op.order_id
            WHERE o.group_id = $group_id AND (o.status_id = $THANH_CONG or o.status_id = $CHUYEN_HANG) $cond
            ORDER BY o.id DESC, op.id DESC
        ";

        return DB::fetch_all($sql);
    }
    /**
     * Lấy ra chi phí quảng cáo
     */
    static function getAdvCostBySource($cond = ""){
        $sql = "
            SELECT SUM(GREATEST(IFNULL(time_slot_1, 0), IFNULL(time_slot_2, 0), IFNULL(time_slot_3, 0), IFNULL(time_slot_4, 0), IFNULL(time_slot_5, 0), IFNULL(time_slot_6, 0), IFNULL(time_slot_7, 0))) AS total
            FROM vs_adv_money AS vam
            WHERE vam.id > 0 $cond
        ";
        return DB::fetch($sql, "total");
    }

    static function getPhonesBySource($cond = "")
    {
        return DB::fetch("
            SELECT COUNT(o.id) AS total
            FROM orders AS o
            WHERE $cond
        ", "total");
    }

    static function get_source(){
        $sql = '
                select
                    order_source.id,order_source.name,order_source.default_select
                from
                    order_source
                where
                    order_source.group_id = '.Dashboard::$group_id.'
                    OR order_source.group_id=0
                order by
                    order_source.id
            ';
        $order_source = DB::fetch_all($sql);
        //System::Debug($order_source);die;
        return $order_source;
    }
    static function getNotesDashboard($user_id)
    {
        return DB::fetch_all("SELECT notes.id,notes.title,notes.content,notes.updated_at FROM notes WHERE user_id = $user_id ORDER BY is_pin ASC, updated_at DESC LIMIT 3");
    }

    static function get_log_ins()
    {
        if ( defined('LOG_V2') && !empty(LOG_V2)) {
            $group_id = Dashboard::$group_id;
            $conditions = array('group_id' => $group_id);
            $payload = array(
                'conditions' => $conditions,
                'options' => array(
                    'sorts' => array(
                        '_id' => 'DESC'
                    ),
                    'limit' => 5,
                    'page' => 1
                )
            );
            $items = getAccountLog($payload);
        } else {
            $group_id = Dashboard::$group_id;
            $cond = 'account_log.group_id=' . $group_id;
            $items = DB::fetch_all('
                select
                    account_log.id,account_log.account_id,account_log.time
                from
                    account_log
                where
                    ' . $cond . '
                ORDER BY
                    account_log.id desc
                limit
                    0,5
            ');
        }
        return $items;
    }
    static function get_logs(){
        $group_id = Dashboard::$group_id;
        $cond = '`log`.group_id='.$group_id;
        $items = DB::fetch_all('
            select
                `log`.id,`log`.user_id,`log`.time,log.title,log.description
            from
                `log`
            where
                '.$cond.'
            ORDER BY
                `log`.id desc
            limit
                0,10
        ');
        return $items;
    }

    static function get_groups(){
        if(!System::is_local()){
            $m_key='groups_'.Dashboard::$group_id;
            if(!$items=MC::get_items($m_key)){
                $items = DB::fetch_all('select id,name from `groups` where master_group_id='.Dashboard::$group_id.' order by name');
                MC::set_items($m_key,$items,time() + 24*3600);
            }
        }else{
            $items = DB::fetch_all('select id,name from `groups` where master_group_id='.Dashboard::$group_id.' order by name');
        }
        return $items;
    }
    static function get_account_groups(){
        return get_account_groups();
    }

    static function get_bundles($group_id=false){
        $group_id = $group_id ? $group_id:Dashboard::$group_id;
        $sql = '
                select
                    bundles.id,bundles.name
                from
                    bundles
                WHERE
                    bundles.group_id='.$group_id.'
                order by
                    bundles.name
            ';
        $bundles = DB::fetch_all($sql);
        return $bundles;
    }
    static function get_lable(){
        $sql = '
                SELECT
                    labels.id,labels.name
                FROM
                    labels
                LEFT JOIN 
                    products on products.label_id = labels.id
                WHERE
                    products.group_id = '.Session::get('group_id').'
                ORDER BY
                    labels.id
            ';
        $query = DB::fetch_all($sql);
        return $query;
    }

    static function shipping_services(){
        $sql = '
                select
                    shipping_services.id,
                    shipping_services.name,
                    shipping_services.group_id,
                    shipping_services.is_default,
                    shipping_services.weight,
                    shipping_services.created,
                    shipping_services.modified,
                    shipping_services.url
                from
                    shipping_services
                WHERE
                    shipping_services.group_id = '.Dashboard::$group_id.'
                order by
                    shipping_services.name
            ';
        $items = DB::fetch_all($sql);
        return $items;
    }
    static function get_total_hitcount($cond='type="NEWS"'){
        return DB::fetch('
            SELECT
                sum(hitcount) as total
            FROM
                news
            WHERE
                '.$cond.'
                and news.portal_id="'.PORTAL_ID.'"
        ','total');
    }
    static function GetTotal($cond='type="NEWS"'){
        return DB::fetch('
            SELECT
                count(*) as acount
            FROM
                news
            WHERE
                '.$cond.'
                and news.portal_id="'.PORTAL_ID.'"
        ');
    }
    static function GetItems($cond='type="NEWS"',$order_by='hitcount DESC',$item_per_page=20){
        return DB::fetch_all('
            SELECT
                news.id,news.name_'.Portal::language().' as name
                ,news.name_id_'.Portal::language().' as name_id
                ,news.hitcount
                ,news.user_id
                ,news.time
                ,category.name_'.Portal::language().' as category_name
            FROM
                news
                left outer join news_category on news_category.news_id = news.id
                left outer join category on category.id = news_category.category_id
            WHERE
                '.$cond.'
                and news.portal_id="'.PORTAL_ID.'"
            ORDER BY
                '.$order_by.'
            LIMIT
                '.((page_no()-1)*$item_per_page).','.$item_per_page.'
        ');
    }
    static function GetTotalAdv($cond='1'){
        return DB::fetch('
            SELECT
                count(*) as acount
            FROM
                advertisment
                JOIN media on media.id=advertisment.item_id
                JOIN block on advertisment.region = block.name
                JOIN page on page.id = block.page_id
                left outer join category on category.id = advertisment.category_id
            WHERE
                '.$cond.'
                and module_id=5911
                and media.type="ADVERTISMENT"
                and media.portal_id="'.PORTAL_ID.'"
        ');
    }
    static function GetAdvItems($cond='1'){
        return DB::fetch_all('
            SELECT
                advertisment.*
                ,media.url
                ,media.name_'.Portal::language().' as name
                ,page.name as page
                ,category.name_'.Portal::language().' as category_name
            FROM
                advertisment
                JOIN media on media.id=advertisment.item_id
                JOIN block on advertisment.region = block.name
                JOIN page on page.id = block.page_id
                left outer join category on category.id = advertisment.category_id
            WHERE
                '.$cond.'
                and module_id=5911
                and media.type="ADVERTISMENT"
                and media.portal_id="'.PORTAL_ID.'"
            ORDER BY
                advertisment.click_count DESC
        ');
    }
    static function get_adv_moneys($cond,$item_per_page,$date_from=false,$date_to=false){
        $sql = '
            select
                vs_adv_money.id
                ,vs_adv_money.account_id
                ,vs_adv_money.total
                ,vs_adv_money.`date`
                ,SUM(vs_adv_money.time_slot_1) AS time_slot_1
                ,SUM(vs_adv_money.time_slot_2) AS time_slot_2
                ,SUM(vs_adv_money.time_slot_3) AS time_slot_3
                ,SUM(vs_adv_money.time_slot_4) AS time_slot_4
                ,SUM(vs_adv_money.time_slot_5) AS time_slot_5
                ,SUM(vs_adv_money.time_slot_6) AS time_slot_6
                ,SUM(vs_adv_money.time_slot_7) AS time_slot_7
            from
                vs_adv_money
            where
                '.$cond.'
            GROUP BY
                vs_adv_money.`account_id`,vs_adv_money.`date`
            ORDER BY
                vs_adv_money.`date` DESC
        ';
        $items = DB::fetch_all($sql);
        $i=1;
        foreach ($items as $key=>$value) {
            $items[$key]['i']=$i++;
        }
        return $items;
    }
    static function get_user(){
        return DB::fetch_all('
            SELECT
                account.id,party.full_name,account.group_id,users.id as user_id
            FROM
                account
                JOIN party ON party.user_id = account.id
                JOIN users ON users.username = account.id
        WHERE
                account.id = "'.Session::get('user_id').'" AND account.group_id = '.Dashboard::$group_id.'
        ');
    }
    static function get_user_report($code)
    {
        $group_id = Session::get('group_id');
        $sql = "SELECT 
                    users.id,
                    users.id as user_id,
                    party.image_url as avatar,
                    party.full_name,
                    account.is_active as is_active 
                FROM 
                    users
                JOIN 
                    party ON party.user_id = users.username
                JOIN 
                    account ON users.username = account.id
                JOIN 
                    users_roles ON users_roles.user_id = users.id
                JOIN 
                    roles_to_privilege ON roles_to_privilege.role_id = users_roles.role_id
                WHERE 
                    users.group_id = $group_id 
                AND 
                    roles_to_privilege.privilege_code = '".$code."'
                ";
        if(!System::is_local()){
            $m_key = false;
            if($m_key){
                $get_mc  = MC::get_items($m_key);
                if($get_mc){
                    $items = $get_mc;
                }else{
                    $items = DB::fetch_all($sql);
                    if($m_key){
                        MC::set_items($m_key, $items,time() + 300);
                    }
                }
            }else{
                $items = DB::fetch_all($sql);
            }
        } else {
            $items = DB::fetch_all($sql);
        }
        
        return $items;
    }
    static function get_users($code='GANDON',$check_is_active=false,$by_user_id=false){
        $group_id = Dashboard::$group_id;
        $master_group_id = Dashboard::$master_group_id;

        if($account_group_id=Url::iget('account_group_id')){
            $cond = 'account.account_group_id='.$account_group_id;
        }else{
            $cond = '1=1';
        }
        if($check_is_active){// loc theo tk kich hoat hay chua
            if($check_is_active==1){
                $cond .= ' and IFNULL(account.is_active,0)=0';
            }
        }else{
            $cond .= '  and IFNULL(account.is_active,0)<>0';
        }
        if(is_account_group_manager() and !check_user_privilege('BC_DOANH_THU_NV')){
            $account_group_ids = get_account_group_ids();
            if($account_group_ids){
                $cond .= ' AND account.account_group_id IN ('.$account_group_ids.')';
            }
        }
        if(Dashboard::$account_type==TONG_CONG_TY){//khoand edited in 05/10/2018
            if($search_group_id=Url::iget('group_id') or $search_group_id=Url::iget('search_group_id')){
                $cond .= ' and (groups.id='.$search_group_id.')';
            }else{
                $cond .= ' and (groups.id='.$group_id.' or groups.master_group_id = '.$group_id.')';
            }
        }elseif($master_group_id){
            $cond .= ' and (groups.id='.$group_id.')';
        }else{
            $cond .= ' and groups.id='.$group_id.'';
        }
        $sql = '
            SELECT
                '.($by_user_id?'users.id,users.id as user_id':'account.id,users.id as user_id').',
                account.group_id,
                '.((Dashboard::$account_type==TONG_CONG_TY)?'CONCAT(CONCAT(party.full_name,IF(account.admin_group=1," (admin)","")),": cty ",groups.name)':'party.full_name').' AS full_name,
                users.username,
                users.rated_point,
                users.rated_quantity,
                party.image_url as avatar
            FROM
                account
                JOIN `groups` on groups.id = account.group_id
                JOIN party ON party.user_id = account.id
                JOIN users ON users.username = account.id
                JOIN users_roles ON users_roles.user_id = users.id
                JOIN roles_to_privilege ON roles_to_privilege.role_id = users_roles.role_id
        WHERE
                '.$cond.'
                '.(($code)?' AND    roles_to_privilege.privilege_code="'.$code.'"':'').'
        ';
        // memcached
        $m_key = false;//dashboard_users_'.$group_id.'_'.get_user_id();
        if($m_key){
            $get_mc  = MC::get_items($m_key);
            if($get_mc){
                $items = $get_mc;
            }else{
                $items = DB::fetch_all($sql);
                if($m_key){
                    MC::set_items($m_key, $items,time() + 300);
                }
            }
        }else{
            $items = DB::fetch_all($sql);
        }
        return $items;
    }
    static function get_orders_by_post($cond){
        $sql = '
            SELECT
                orders.id,
                orders.delivered,orders.customer_name,orders.mobile,
                orders.note1,orders.total_price,
                orders.status_id,
                fb_pages.page_id,fb_pages.page_name,orders.fb_post_id
            FROM
                orders
                LEFT OUTER JOIN fb_pages ON fb_pages.id = orders.fb_page_id
            WHERE
                '.$cond.'
            ORDER BY
                orders.id desc
            LIMIT 0,2000
        ';
        $items = DB::fetch_all($sql);
        return $items;
    }
    static function get_order_revision_by_cond($cond){
        $sql = '
                select
                    order_revisions.id,
                    order_revisions.order_id,
                    order_revisions.before_order_status_id,
                    order_revisions.order_status_id,
                    order_revisions.before_order_status,
                    order_revisions.order_status,
                    order_revisions.user_created_name,
                    order_revisions.created,
                    order_revisions.user_created,
                    order_revisions.modified    
                from
                    order_revisions
                    join orders on orders.id = order_revisions.order_id
                WHERE
                    '.$cond.'
                order by
                    order_revisions.id desc 
            ';
        $items = DB::fetch_all($sql);
        return $items;
    }
    static function get_total_order($cond){
        $sql = '
            SELECT
                count(orders.id) as total
            FROM
                orders
                JOIN statuses ON statuses.id = orders.status_id
            WHERE
                '.$cond.'
        ';
        return DB::fetch($sql,'total');
    }
    static function get_orders($cond,$account_type=1,$limit=false){
        $item_per_page = $limit?$limit:1000;
        $sql = '
            SELECT
                orders.id,
                orders.delivered,orders.customer_name,
                orders.mobile,orders.mobile2,
                orders.note1,orders.note2,
                orders.total_price,
                orders.status_id,statuses.name as status_name,
                orders.created,orders.assigned,
                orders.source_name,
                orders.fb_customer_id,
                orders.fb_page_id,
                orders.fb_post_id,
                (SELECT '.(($account_type==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_assigned limit 0,1) AS user_assigned,
                (SELECT '.(($account_type==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_confirmed limit 0,1) AS user_confirmed,
                (SELECT '.(($account_type==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_created limit 0,1) AS user_created,
                (SELECT '.(($account_type==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_delivered limit 0,1) AS user_delivered
            FROM
                orders
                JOIN statuses ON statuses.id = orders.status_id
            WHERE
                '.$cond.'
            ORDER BY
                orders.id desc
                '.($limit?''.($item_per_page?' LIMIT '.((page_no()-1)*$item_per_page).','.$item_per_page.'':'').'':'').'
        ';
        $items = DB::fetch_all($sql);
        return $items;
    }
    static function get_report_statuses(){
        $statuses_cond = 'id=5 or id=6 or id=7 or id=9 or id=8';
        //if(Dashboard::$master_group_id or Dashboard::$account_type==3)
        {
            $statuses_cond .= ' or id=3';
        }
        if(Dashboard::$admin_group or Dashboard::$quyen_admin_ke_toan or Dashboard::$quyen_bc_doanh_thu_mkt or Dashboard::$quyen_marketing){
            $statuses_cond .= ' or id=2';
        }
        return DB::fetch_all('
          select 
            statuses.id,statuses.name,0 as total,0 as qty,
            0 as turnover,
            0 as total_delivered,
            0 as total_reached,
            (IF(statuses.id=7,10,IF(statuses.id=3,8,statuses.id))) as order_by 
          from 
            statuses 
          where
           ('.$statuses_cond.') 
          order by 
            order_by DESC
         ');
    }
    static function get_statuses_by_revision($status_id=false,$extra_cond=''){
        return DB::fetch_all('
          select 
            statuses.id,statuses.name,0 as total,0 as qty
          from 
            statuses
            left join statuses_custom on statuses_custom.status_id = statuses.id
          where
            statuses.group_id = '.Dashboard::$group_id.' '.($status_id?' and statuses.id='.$status_id.'':'OR (statuses.is_system=1)').'
            '.$extra_cond.'
          order by
            statuses_custom.level,statuses_custom.position
          ');
    }
    static function get_status_customer(){
        $group_id = Session::get('group_id');
        $cond = ' groups.id='.$group_id.'';
        $sql = '
            SELECT 
                statuses.id, statuses.no_revenue,
                IF(statuses.level>0,CONCAT(statuses.level,". ",statuses.name),statuses.name) AS name,
                IF(statuses.is_system=1,statuses.level,statuses_custom.level) as level,
                IF(statuses.is_system=1,statuses.color,statuses_custom.color) as color
            FROM 
                `statuses`
                JOIN `groups` ON groups.id = statuses.group_id
                LEFT JOIN statuses_custom ON statuses_custom.status_id = statuses.id
            WHERE 
                '.$cond.' OR is_system=1
            GROUP BY
                statuses.id
            ORDER BY 
                IF(statuses.is_system=1,statuses.level,statuses_custom.level),
                statuses_custom.position,
                statuses.id
        ';

        $items = DB::fetch_all($sql);
        return $items;
    }
    static function get_statuses($status_id=false,$extra_cond=''){
        return DB::fetch_all('
          select 
            statuses.id,statuses.name, statuses.level, statuses.no_revenue, 0 as total,0 as qty,
            0 as turnover,
            0 as total_delivered,
            0 as total_reached,(IF(statuses.id=7,10,statuses.id)) as order_by
          from 
            statuses
            left join statuses_custom on statuses_custom.status_id = statuses.id
          where
            statuses.group_id = '.Dashboard::$group_id.' '.($status_id?' and statuses.id='.$status_id.'':'OR (statuses.is_system=1)').'
            '.$extra_cond.'
          order by
            statuses.level ASC
          ');
    }
    static function get_report_info(){
        $party = DB::fetch('select full_name,note1,note2,address,phone,website from party WHERE user_id="'.Session::get('user_id').'" ');
        return $party;
    }
    static function get_no_revenue_status($group_id=false){
        $group_id = $group_id?$group_id:Dashboard::$group_id;
        $master_group_id = Dashboard::$master_group_id;
        $no_revenue_status = MiString::get_list(DB::fetch_all('select id from statuses where no_revenue=1 and (('.(($master_group_id)?'group_id = '.$master_group_id.'':'group_id = '.$group_id.'').') or statuses.is_system=1)'));
        $no_revenue_status=implode(',', $no_revenue_status);
        return $no_revenue_status;
    }
    static function get_revenue_status($group_id=false){
        $group_id = $group_id?$group_id:Dashboard::$group_id;
        $master_group_id = Dashboard::$master_group_id;
        $revenue_status = MiString::get_list(DB::fetch_all('select id from statuses where (('.(($master_group_id)?'group_id = '.$master_group_id.'':'group_id = '.$group_id.'').') or statuses.is_system=1)'));
        $revenue_status=implode(',', $revenue_status);
        return $revenue_status;
    }
    /*
     * get status level >= 2
     * @input: id group
     * @output: list id status
     */
    static function get_status_level($group_id=false){
        $group_id = $group_id?$group_id:Dashboard::$group_id;
        $master_group_id = Dashboard::$master_group_id;
        $sql = 'select statuses.id, statuses.level as levelstatus, IFNULL(statuses_custom.level, 0) as levelcustom,statuses.no_revenue,statuses.group_id from statuses LEFT JOIN statuses_custom ON statuses.id = statuses_custom.status_id and statuses_custom.level>=2 WHERE (('.(($master_group_id)?'statuses.group_id = '.$master_group_id.'':'statuses.group_id = '.$group_id.'').') or statuses.is_system = 1) GROUP BY statuses.id';
        $level_status = DB::fetch_all($sql);
        $rs = '';
        foreach ($level_status as $item){
            $maxlevel = max($item['levelstatus'],$item['levelcustom']);
            if ($maxlevel >= 2){
                $rs.= $item['id'].',';
            }
            // kiểm tra nếu là trạng thái khách hàng tạo và có tính doanh thu thì add vào
            if($item['no_revenue'] == 0 && $item['group_id'] == $group_id){
                $rs.= $item['id'].',';
            }
        }
        $rs=trim($rs, ',');
        return $rs;
    }
    static function get_no_reached_status(){
        $no_revenue_status = MiString::get_list(DB::fetch_all('select id from statuses where not_reach=1 and (('.((Dashboard::$master_group_id)?'group_id = '.Dashboard::$master_group_id.'':'group_id = '.Dashboard::$group_id.'').') or statuses.is_system=1)'));
        $no_revenue_status=implode(',', $no_revenue_status);
        return $no_revenue_status;
    }

    /**
     * Gets the status level have revenue.
     *
     * @param      string  $IDs                 I ds
     * @param      bool    $withLevelCondition  The with level condition
     *
     * @return     <type>  The status level have revenue.
     */
    public static function getStatusLevelHaveRevenue(string $IDs, $withLevelCondition = false)
    {   
        $levelCondition = $withLevelCondition ? 'AND (statuses.level >=2 OR statuses_custom.level >= 2)' : '';

        return DB::fetch_all('
            SELECT gid as id, GROUP_CONCAT(id) as sid
            FROM(
                SELECT
                IF(statuses.group_id != 1, statuses.group_id, statuses_custom.group_id) AS gid,
                statuses.id, statuses.name, statuses.group_id, 
                statuses.is_system, 
                statuses.no_revenue, 
                statuses.level,
                statuses_custom.group_id as custom_group_id,
                statuses.level AS custom_level
                FROM statuses 
                LEFT JOIN statuses_custom ON statuses.id = statuses_custom.status_id 
                WHERE 
                     (
                        ((is_system = 0 OR is_system IS NULL) AND (no_revenue = 0 OR no_revenue IS NULL))
                        OR (is_system = 1 AND (no_revenue = 0 OR no_revenue IS NULL) ' . $levelCondition . ')
                    )
                    AND (statuses_custom.group_id IN (' . $IDs . ') OR statuses.group_id IN (' . $IDs . '))
            ) T
            GROUP BY T.gid
        ');
    }

    /**
     * Gets the system statuses.
     *
     * @param      bool    $withLevelCondition  The with level condition
     *
     * @return     <type>  The system statuses.
     */
    private static function getSystemStatuses($withLevelCondition = false)
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
    static function  getLevelStatuses($level = []){

        $statusId = [];
        $sql =  DB::fetch_all('
            SELECT
                statuses.id,
                statuses.name,
                (IF(statuses_custom.level,statuses_custom.level,statuses.level)) as level
            FROM
                statuses
                LEFT JOIN ( SELECT * FROM statuses_custom WHERE group_id = '.Dashboard::$group_id.' ) AS statuses_custom ON statuses.id = statuses_custom.status_id 
            WHERE
                (statuses.group_id = '.Dashboard::$group_id.' OR statuses.is_system = 1) AND 
                (statuses.level > 2 OR statuses_custom.level > 2) AND 
                statuses.id NOT IN (2,5) AND 
                (statuses.level in (' . implode(',', $level) . ') OR statuses_custom.level in (' . implode(',', $level) . '))
        ');
        foreach ($sql as $val) {
            $statusId[] = $val['id'];
        }
        return $statusId;
    }
    static function getUserReasonFail($code=false,$isActive=false)
    {
        $group_id = Session::get('group_id');
        $cond = '';
        if(is_account_group_manager()){
            $user_id = get_user_id();
            $account_group_id = DB::fetch('select id from account_group where admin_user_id='.$user_id, 'id');
            $cond .= ' AND account.account_group_id = ' . $account_group_id .' ';
        } else if(
                    Dashboard::$quyen_sale
                    && !is_group_owner()
                    && !Dashboard::$admin_group
                    && !Dashboard::$quyen_ke_toan
                    && !Dashboard::$quyen_van_don
        ){
            $cond .= ' AND account.id = "' . Session::get('user_id') . '"';
        }
        if($isActive == false){
            $cond .= ' AND account.is_active = 1';
        } else if($isActive == '1'){
            $cond .= ' AND (account.is_active = 0 OR account.is_active IS NULL)';
        }
        if($code){
            $cond .= ' AND roles_to_privilege.privilege_code = "'.$code.'" ';
        }

        $sql = '
            SELECT 
                users.id as id,
                users.id as user_id,
                users.group_id,
                users.name as full_name,
                account.id as username
            FROM `account` 
            LEFT JOIN account_group ON account_group_id = account_group.id 
            JOIN users ON users.username = account.id
            LEFT JOIN users_roles ON  users_roles.user_id = users.id
            LEFT JOIN roles ON roles.id = users_roles.role_id
            LEFT JOIN roles_to_privilege ON roles_to_privilege.role_id = roles.id
            WHERE account.group_id = ' . $group_id . $cond .' '
        ;
        $results = DB::fetch_all($sql);
        return $results;
    }

    /**
     * lấy danh sách id nguồn đơn marketing (Rule chuẩn hóa)
     *
     * @param string $ids
     * @param integer $groupId
     * @return string
     */
    public static function getIncludeSourceIds(string $ids, int $groupId): string {
        $ids = DataFilter::removeXSSinListIds($ids);
        $arrayIds = self::handleGetIncludeSourceIds($ids);
        if (!$arrayIds) {
            return $ids;
        }//end if

        $idsOfGroup = self::getSourceIdsOfGroup($groupId);
        $_ids = self::mapIncludeIds($arrayIds, $idsOfGroup);
        if ($_ids) {
            $ids .= ',' . $_ids;
        }//end if

        return $ids;
    }


    /**
     * lấy danh sách id nhóm sản phẩm (Rule chuẩn hóa)
     *
     * @param string $ids
     * @param integer $groupId
     * @return string
     */
    public static function getIncludeBundleIds(string $ids, int $groupId): string {
        $ids = DataFilter::removeXSSinListIds($ids);
        $arrayIds = self::handleGetIncludeBundleIds($ids);
        if (!$arrayIds) {
            return $ids;
        }//end if

        $idsOfGroup = self::getBundleIdsOfGroup($groupId);
        $_ids = self::mapIncludeIds($arrayIds, $idsOfGroup);
        if ($_ids) {
            $ids .= ',' . $_ids;
        }//end if

        return $ids;
    }

    /**
     * Lấy danh sách Id nguồn marketing cũ tự tạo của HKD
     *
     * @param integer $groupId
     * @return array
     */
    private static function getSourceIdsOfGroup(int $groupId): array {
        $sql = "SELECT id 
            FROM order_source 
            WHERE group_id = $groupId 
            OR (group_id = 0 AND ref_id != 0)";
        return DB::fetch_all_column($sql);
    }


    /**
     * Lấy danh sách Id nhóm sản phẩm cũ tự tạo của HKD
     *
     * @param integer $groupId
     * @return array
     */
    private static function getBundleIdsOfGroup(int $groupId): array {
        $sql = "SELECT id 
            FROM bundles 
            WHERE group_id = $groupId
        ";
        return DB::fetch_all_column($sql);
    }

    /**
     * Lấy danh sách Id nhóm sản phẩm cũ của HKD
     *
     * @param string $bundleIds
     * @return array
     */
    private static function handleGetIncludeBundleIds(string $bundleIds): array {
        $sql = "SELECT `id`, `include_ids`
            FROM `bundles`
            WHERE `id` IN ($bundleIds)
                AND `ref_id` = 0
                AND `group_id` = 0
                AND `standardized` = 1
        ";

        return DB::fetch_all_column($sql, 'include_ids');
    }

    /**
     * Lấy danh sách map id nguồn marketing hệ thống với nguồn marketing cũ
     *
     * @param string $sourceIds
     * @return array
     */
    private static function handleGetIncludeSourceIds(string $sourceIds): array
    {
        $sql = "SELECT `id`, `include_ids`
            FROM `order_source`
            WHERE `id` IN ($sourceIds)
                AND `ref_id` = 0
                AND `group_id` = 0
        ";
        
        return DB::fetch_all_column($sql, 'include_ids');
    }

    /**
     * map danh sách id hợp lệ
     *
     * @param array $sourceIds
     * @return string
     */
    private static function mapIncludeIds (array $ids, array $idsOfGroup): string {
        $ids = implode(',', $ids);
        $ids = explode(',', $ids);
        $ids = array_intersect($ids, $idsOfGroup);
        return implode(',', $ids);
    }
    
    /**
     * Lấy danh sách nhóm sản phẩm chuẩn hóa
     *
     * @return array
     */
    public static function getBundles(): array {
        $sql = "SELECT `id`, `name`
            FROM `bundles`
            WHERE `group_id` = 0
                AND `standardized` = 1 
                AND `ref_id` = 0 
            ORDER BY `name`";

        $result = DB::fetch_all($sql);
        return $result;
    }
    
    /**
     * Lấy danh sách nguồn marketing chuẩn hóa
     *
     * @return array
     */
    public static function getSource(): array {
        $sql = "SELECT `id`, `name`, `default_select`
            FROM `order_source`
            WHERE `ref_id` = 0
                AND `group_id`=0
            ORDER BY id";

        $result = DB::fetch_all($sql);
        return $result;
    }
}
