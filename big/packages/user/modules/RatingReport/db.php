<?php

class RatingReportDB
{
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
        $danger = RatingReport::$mkt_cost_per_revenue_danger;
        $danger = $danger?$danger:35;
        $warning = RatingReport::$mkt_cost_per_revenue_warning;
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
        $group_id = RatingReport::$group_id;
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
				 qlbh_warehouse.group_id='.RatingReport::$group_id.' OR structure_id='.ID_ROOT.'			
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
        $group_id = RatingReport::$group_id;
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
                AND (groups.expired_date > "'.$start_time.'" or groups.expired_date="'.RatingReport::$date_init_value.'")
                AND roles_to_privilege.privilege_code="'.$code.'"
                AND groups.id='.$group_id.'
        ');
        //System::debug($sql);
        $users = DB::fetch_all($sql);
        $no_revenue_status = DashboardDB::get_no_revenue_status();
        foreach ($users as $key => $value){
            //$reports[$key]['id'] = $key;
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
        }
        if (sizeof($users) > 1) {
            System::sksort($users, 'total', 'DESC');
        }
        return $users;
    }
    static function get_user_statistic($system_group_id, $start_time,$end_time,$code='GANDON'){
        if(!$system_group_id){
            $user_id = get_user_id();
            $all_system_group_id= DB::fetch('select id,system_group_id from groups_system_account where user_id='.$user_id.' limit 0,1','system_group_id');
        }
        $sql =('
            SELECT
                users.id,users.name,users.group_id,
                groups.name as group_name,
                groups.expired_date
            FROM
              users
                JOIN account on account.id=users.username
                JOIN `groups` on groups.id = users.group_id
                JOIN groups_system ON groups_system.id = groups.system_group_id
                JOIN users_roles ON users_roles.user_id = users.id
                JOIN roles_to_privilege ON roles_to_privilege.role_id = users_roles.role_id
            WHERE
                account.is_active
                AND (groups.expired_date > "'.$start_time.'" or groups.expired_date="0000-00-00 00:00:00")
                AND '.($system_group_id?'groups_system.id='.$system_group_id:IDStructure::child_cond(DB::structure_id('groups_system',$all_system_group_id))).'
                AND roles_to_privilege.privilege_code="'.$code.'"
        ');
        $users = DB::fetch_all($sql);
        $no_revenue_status = DashboardDB::get_no_revenue_status();
        foreach ($users as $key => $value){
            $cond = 'orders.group_id=' . $value['group_id'] . '';
            $cond .= ' and orders.status_id NOT IN (' . $no_revenue_status . ')';
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
        $group_id = $group_id?$group_id:RatingReport::$group_id;
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
        $group_id = RatingReport::$group_id;
        $sql = "
            SELECT
                op.id, op.product_name, SUM(op.qty * op.product_price) AS doanhthu, COUNT(o.id) AS total_order, AVG(IFNULL(op.staff_rating, 0)) AS staff_rate, AVG(IFNULL(op.customer_rating, 0)) AS customer_rate,
                o.type, (SELECT SUM(op_temp.qty * op_temp.product_price) FROM orders_products AS op_temp JOIN orders AS o_temp ON o_temp.id = op_temp.order_id WHERE op_temp.id = op.id AND o_temp.type > 2) AS doanhthu_cskh
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
        $group_id = RatingReport::$group_id;
        $sql = "
            SELECT
                op.id, op.product_name, (op.qty * op.product_price) AS doanhthu, (IFNULL(op.staff_rating, 0)) AS staff_rate, (IFNULL(op.customer_rating, 0)) AS customer_rate, op.product_id,
                o.type, (SELECT SUM(op_temp.qty * op_temp.product_price) FROM orders_products AS op_temp JOIN orders AS o_temp ON o_temp.id = op_temp.order_id WHERE op_temp.id = op.id AND o_temp.type > 2) AS doanhthu_cskh
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
					order_source.group_id = '.RatingReport::$group_id.'
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
        $group_id = RatingReport::$group_id;
        $cond = 'account_log.group_id='.$group_id;
        $items = DB::fetch_all('
            select
                account_log.id,account_log.account_id,account_log.time
            from
                account_log
            where
                '.$cond.'
            ORDER BY
                account_log.id desc
            limit
                0,5
        ');
        return $items;
    }
    static function get_logs(){
        $group_id = RatingReport::$group_id;
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
            $m_key='groups_'.RatingReport::$group_id;
            if(!$items=MC::get_items($m_key)){
                $items = DB::fetch_all('select id,name from `groups` where master_group_id='.RatingReport::$group_id.' order by name');
                MC::set_items($m_key,$items,time() + 24*3600);
            }
        }else{
            $items = DB::fetch_all('select id,name from `groups` where master_group_id='.RatingReport::$group_id.' order by name');
        }
        return $items;
    }
    static function get_account_groups(){
       return get_account_groups();
    }

    static function get_bundles($group_id=false){
        $group_id = $group_id ? $group_id:RatingReport::$group_id;
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
                    shipping_services.group_id = '.RatingReport::$group_id.'
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
                account.id = "'.Session::get('user_id').'" AND account.group_id = '.RatingReport::$group_id.'
        ');
    }
    static function get_users($code='GANDON',$check_is_active=false,$by_user_id=false){
        $group_id = RatingReport::$group_id;
        $master_group_id = RatingReport::$master_group_id;

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
        if(RatingReport::$account_type==TONG_CONG_TY){//khoand edited in 05/10/2018
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
                '.((RatingReport::$account_type==TONG_CONG_TY)?'CONCAT(CONCAT(party.full_name,IF(account.admin_group=1," (admin)","")),": cty ",groups.name)':'party.full_name').' AS full_name,
                users.username,
                users.rated_point,
                users.rated_quantity
            FROM
                account
                JOIN `groups` on groups.id = account.group_id
                JOIN party ON party.user_id = account.id
                JOIN users ON users.username = account.id
                JOIN users_roles ON users_roles.user_id = users.id
                JOIN roles_to_privilege ON roles_to_privilege.role_id = users_roles.role_id
        WHERE
                '.$cond.'
                '.(($code)?' AND	roles_to_privilege.privilege_code="'.DB::escape($code).'"':'').'
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
        //if(RatingReport::$master_group_id or RatingReport::$account_type==3)
        {
            $statuses_cond .= ' or id=3';
        }
        if(RatingReport::$admin_group or RatingReport::$quyen_admin_ke_toan){
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
            statuses.group_id = '.RatingReport::$group_id.' '.($status_id?' and statuses.id='.$status_id.'':'OR (statuses.is_system=1)').'
            '.$extra_cond.'
          order by
            statuses_custom.level,statuses_custom.position
          ');
    }
    static function get_statuses($status_id=false,$extra_cond=''){
        return DB::fetch_all('
          select 
            statuses.id,statuses.name,0 as total,0 as qty,
            0 as turnover,
            0 as total_delivered,
            0 as total_reached,(IF(statuses.id=7,10,statuses.id)) as order_by
          from 
            statuses
            left join statuses_custom on statuses_custom.status_id = statuses.id
          where
            statuses.group_id = '.RatingReport::$group_id.' '.($status_id?' and statuses.id='.$status_id.'':'OR (statuses.is_system=1)').'
            '.$extra_cond.'
          order by
            statuses_custom.position
          ');
    }
    static function get_report_info(){
        $party = DB::fetch('select full_name,note1,note2,address,phone,website from party WHERE user_id="'.Session::get('user_id').'" ');
        return $party;
    }
    static function get_no_revenue_status($group_id=false){
        $group_id = $group_id?$group_id:RatingReport::$group_id;
        $master_group_id = RatingReport::$master_group_id;
        $no_revenue_status = MiString::get_list(DB::fetch_all('select id from statuses where no_revenue=1 and (('.(($master_group_id)?'group_id = '.$master_group_id.'':'group_id = '.$group_id.'').') or statuses.is_system=1)'));
        $no_revenue_status=implode(',', $no_revenue_status);
        return $no_revenue_status;
    }
    static function get_no_reached_status(){
        $no_revenue_status = MiString::get_list(DB::fetch_all('select id from statuses where not_reach=1 and (('.((RatingReport::$master_group_id)?'group_id = '.RatingReport::$master_group_id.'':'group_id = '.RatingReport::$group_id.'').') or statuses.is_system=1)'));
        $no_revenue_status=implode(',', $no_revenue_status);
        return $no_revenue_status;
    }
}
?>
