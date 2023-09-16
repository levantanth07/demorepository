<?php

class AdminShopDB
{
    static function statusPalion()
    {
        $status = [
            0 => 'Chọn trạng thái',
            1 => 'Đã thanh toán',
            2 => 'Dùng thử'
        ];
        return $status;
    }
    static function getHistoryPackages($group_id)
    {
        $sql = "
            SELECT
                p.name, pg.id, DATE_FORMAT(pg.billing_at, '%d-%m-%Y') AS date_at, DATE_FORMAT(pg.created_at, '%d-%m-%Y') AS created_at,
                pg.months, pg.discount, pg.total_price,pg.palion_price,  DATE_FORMAT(pg.palion_paid_at, '%d-%m-%Y') AS palion_paid_at
            FROM acc_packages_groups AS pg
            LEFT JOIN acc_packages AS p ON p.id = pg.package_id
            WHERE pg.group_id = $group_id
            ORDER BY pg.id DESC
        ";

        return DB::fetch_all($sql);
    }

    static function getTotalPricePackagesByMonth($cond = []){
        $sql = "
            SELECT SUM(pg.total_price) AS total_price, p.name, p.id, DATE_FORMAT(pg.billing_at, '%m-%Y') AS date_at
            FROM acc_packages_groups AS pg
            LEFT JOIN acc_packages AS p ON p.id = pg.package_id
            WHERE pg.id > 0 ". implode(" ", $cond) ."
            GROUP BY  pg.package_id, date_at
        ";

        return DB::fetch_all($sql);
    }

    static function getTotalPricePackages($cond = []){
        $sql = "
            SELECT SUM(pg.total_price) AS total_price, p.name, p.id
            FROM acc_packages_groups AS pg
            LEFT JOIN acc_packages AS p ON p.id = pg.package_id
            WHERE pg.id > 0 ". implode(" ", $cond) ."
            GROUP BY pg.package_id
        ";

        return DB::fetch_all($sql);
    }

    static function getPackages($order = false){
        if ($order) {
            return DB::fetch_all("
              SELECT 
                acc_packages.id,
                acc_packages_groups.months as number_months,
                acc_packages_groups.discount as percent_discount,
                acc_packages_groups.total_price,
                acc_packages.max_user,
                acc_packages.max_page,
                acc_packages.weight
              FROM 
                acc_packages 
                inner  join acc_packages_groups on acc_packages_groups.package_id
              ORDER BY 
              acc_packages.weight ASC, acc_packages.id DESC
           ");
        }
        return DB::fetch_all("SELECT * FROM acc_packages ORDER BY id DESC");
    }

    static function get_total_expired_shop($cond){
        $sql = '
            SELECT
              count(groups.id) as total
            FROM
                `groups`
              left join groups_system on groups_system.id=groups.system_group_id
            WHERE 
              '.$cond.' and groups.expired_date > "0000-00-00" AND groups.expired_date < "'.date('Y-m-d').'"
        ';
        return DB::fetch($sql,'total');
    }

    static function get_total_actived_shop($cond){
        $sql = '
            SELECT
              count(groups.id) as total
            FROM
                `groups`
              left join groups_system on groups_system.id=groups.system_group_id
            WHERE 
              '.$cond.' and (groups.expired_date = "0000-00-00" or groups.expired_date > "'.date('Y-m-d').'")
        ';
        return DB::fetch($sql,'total');
    }

    static function get_total_good_shop($cond){
        return 0;
        /*$sql = '
            SELECT
              count(groups.id) as total
            FROM
                `groups`
              left join groups_system on groups_system.id=groups.system_group_id
            WHERE 
              '.$cond.' and (groups.expired_date = "0000-00-00" or groups.expired_date > "'.date('Y-m-d').'")
              AND (select count(orders.id) from orders where orders.group_id=groups.id) > 500
        ';
        return DB::fetch($sql,'total');*/
    }

    static function check_user_counter($group_id){
        $user_counter = DB::fetch('select user_counter from `groups` where id='.$group_id.'','user_counter');
        $sql = '
          select 
            count(users.id) as total 
          from 
            users
            inner join account on account.id = users.username
          where 
            users.group_id='.$group_id.' and account.is_active';
        $total_user = DB::fetch($sql,'total');
        if($total_user >= $user_counter){
            return $user_counter;
        }else{
            return false;
        }
    }

    static function check_page_counter($group_id){
        $page_counter = DB::fetch('select page_counter from `groups` where id='.$group_id.'','page_counter');
        $total_page = DB::fetch('select count(id) as total from fb_pages where group_id='.$group_id.'','total');
        if($total_page < $page_counter){
            return $page_counter;
        }else{
            return false;
        }   
    }

    static function get_admins_of_shop($group_id){
        $sql = '
            SELECT
              users.id,
              users.name
            FROM
              users
              INNER JOIN account ON account.id = users.username
            WHERE
              users.group_id = '.$group_id.' and account.admin_group=1
        ';
        $items = DB::fetch_all($sql);
        $str = '';
        foreach($items as $val){
            $str .= ($str?', ':'').$val['name'];
        }
        return $str;
    }

    static function get_roles($group_id){
        $sql = '
            SELECT
              roles.id,roles.name
            FROM
              roles
            WHERE
              roles.group_id = '.$group_id.'
            ORDER BY
              roles.name
        ';
        $items = DB::fetch_all($sql);
        return $items;
    }

    static function get_systems(){
        return $groups = DB::fetch_all('select id,name,structure_id from `groups_system` where 1=1 order by structure_id');
    }

    static function get_account_groups(){
        return $groups = DB::fetch_all('select id,name from account_group where group_id='.Session::get('group_id').' order by name');
    }

    static function init_fb_cron_config($group_id){
        if(!DB::exists('select group_id from fb_cron_config where group_id = '.$group_id)){
            $items = DB::fetch_all('select fb_cron_config._key as id,fb_cron_config.* from fb_cron_config WHERE group_id=1');
            foreach($items as $key=>$val){
                $arr = array(
                  'group_id'=>$group_id,
                    '_key'=>$val['_key'],
                    'type'=>$val['type'],
                    'description'=>$val['description'],
                    'value'=>$val['value'],
                    'level'=>$val['level'],
                    'created'=>date('Y-m-d H:i:s'),
                    'updated'=>date('Y-m-d H:i:s'),
                    'parent_id'=>$val['parent_id']?$val['parent_id']:0,
                );
                DB::insert('fb_cron_config',$arr);
            }
        }
    }

    static function get_total_group(){
        return DB::fetch('select count(*) as total from `groups`','total');
    }

    static function get_roles_of_the_user($user_id,$group_id){
        if($user_id = DB::fetch('select id from users where username="'.$user_id.'" and group_id='.$group_id.'','id')){
            $sql = '
            select
                users_roles.id
                ,users_roles.user_id
                ,users_roles.role_id
                ,roles.name
            from
                users_roles
                INNER JOIN roles ON users_roles.role_id = roles.id
            WHERE
                users_roles.user_id='.$user_id.'
            GROUP BY
                users_roles.id
            order by
                users_roles.id  DESC';
            $items = DB::fetch_all($sql);
            //System::debug($items);die;
            $str = '';
            foreach ($items as $key => $val) {
                $str.=$val['name'].', ';
            }
            return rtrim($str,', ');
        }
        else{
            return '';
        }
        
    }

    static function get_items($cond){
        $sql = '
            SELECT
              roles.id
              ,roles.group_id
              ,roles.name
            FROM
              roles
            WHERE
              '.$cond.'
            ORDER BY
              roles.name
        ';
        $items = DB::fetch_all($sql);
        return $items;
    }
}
?>