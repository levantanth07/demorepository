<?php
class MenuDB
{
    static function get_un_assign_orders(){
        $user_id = get_user_id();
        $group_id = Session::get('group_id');
        $sql = '
            SELECT
              orders.id, orders.`assigned`,orders.mobile
            FROM
              orders
            WHERE
              orders.user_assigned='.$user_id.'
              AND orders.status_id = '.CHUA_XAC_NHAN.'
              AND orders.group_id = '.$group_id.'
            LIMIT
                0,10
        ';
        return DB::fetch_all($sql);
    }
    
    static function getOrderStatusKhachPhanVanSale(){
        $user_id = get_user_id();
        $group_id = Session::get('group_id');
        $sql = '
            SELECT
                orders.id, orders.status_id,orders.mobile
            FROM
                orders
            LEFT JOIN
                orders_extra ON orders_extra.order_id = orders.id
            WHERE
                orders.user_confirmed = '.$user_id.'
                AND orders.status_id = '.KHACH_PHAN_VAN.'
                AND orders.group_id = '.$group_id.'
                AND orders_extra.confused_user_confirmed IS NOT NULL
            LIMIT
                0,10
        ';
        return DB::fetch_all($sql);
    }

    static function getOrderStatusKhachKhongNgheMaySale(){
        $user_id = get_user_id();
        $group_id = Session::get('group_id');
        $sql = '
            SELECT
                orders.id, orders.status_id,orders.mobile
            FROM
                orders
            LEFT JOIN
                orders_extra ON orders_extra.order_id = orders.id
            WHERE
                orders.user_confirmed = '.$user_id.'
                AND orders.status_id = '.KHACH_KHONG_NGHE_MAY.'
                AND orders.group_id = '.$group_id.'
                AND orders_extra.not_answer_phone_user_confirmed IS NOT NULL
            LIMIT
                0,10
        ';
        return DB::fetch_all($sql);
    }

    static function getOrderStatusKhachPhanVanVanDon(){
        $user_id = get_user_id();
        $group_id = Session::get('group_id');
        $sql = '
            SELECT
                orders.id, orders.status_id,orders.mobile
            FROM
                orders
            LEFT JOIN
                orders_extra ON orders_extra.order_id = orders.id
            WHERE
                orders.user_confirmed IS NOT NULL
                AND orders.status_id = '.KHACH_PHAN_VAN.'
                AND orders.group_id = '.$group_id.'
                AND orders_extra.confused_user_lastest_confirmed = '. $user_id .'
            LIMIT
                0,10
        ';
        return DB::fetch_all($sql);
    }

    static function getOrderStatusKhachKhongNgheMayVanDon(){
        $user_id = get_user_id();
        $group_id = Session::get('group_id');
        $sql = '
            SELECT
                orders.id, orders.status_id,orders.mobile
            FROM
                orders
            LEFT JOIN
                orders_extra ON orders_extra.order_id = orders.id
            WHERE
                orders.user_confirmed IS NOT NULL
                AND orders.status_id = '.KHACH_KHONG_NGHE_MAY.'
                AND orders.group_id = '.$group_id.'
                AND orders_extra.not_answer_phone_user_lastest_confirmed = '. $user_id .'
            LIMIT
                0,10
        ';
        return DB::fetch_all($sql);
    }
    static function getNotifications($page = 1)
    {
        $user_id = get_user_id();
        $item_per_page = 5;
        $limit = ($page - 1) * $item_per_page;
        $sql = "
            SELECT 
              n.id, n.notificationable_id, 
              n.content, n.created_at, 
              n.notificationable_type, noti_fake.is_read, n.title
            FROM notifications AS n
            LEFT JOIN notifications_recieved AS nr ON nr.notification_id = n.id
            LEFT JOIN (SELECT nr.notification_id, nr.is_read FROM notifications_recieved AS nr WHERE nr.user_id = $user_id) AS noti_fake ON noti_fake.notification_id = n.id
            WHERE 
              n.type = 1 AND nr.user_id = $user_id and n.is_public <> 1
            GROUP BY 
              n.id
            ORDER BY 
              n.id DESC
            LIMIT 
              $limit, $item_per_page
        ";
        // OR n.is_public = 1
        $items1 = DB::fetch_all($sql);
        $sql = "
            SELECT 
              n.id, n.notificationable_id, 
              n.content, n.created_at, 
              n.notificationable_type, noti_fake.is_read, n.title
            FROM notifications AS n
            LEFT JOIN notifications_recieved AS nr ON nr.notification_id = n.id
            LEFT JOIN (SELECT nr.notification_id, nr.is_read FROM notifications_recieved AS nr WHERE nr.user_id = $user_id) AS noti_fake ON noti_fake.notification_id = n.id
            WHERE 
              n.type = 1 AND nr.user_id = $user_id and n.is_public = 1
            GROUP BY 
              n.id
            ORDER BY 
              n.id DESC
            LIMIT 
              $limit, $item_per_page
        ";
        //echo $sql;
        // OR n.is_public = 1
        $items2 = DB::fetch_all($sql);
        $items =  array_merge($items1,$items2);
        return $items;
    }

    static function getNotificationPopup()
    {
        $user_id = get_user_id();
        $now = date("Y-m-d 00:00:00");
        $sql = "
            SELECT n.id, n.content, n.title
            FROM notifications AS n
            LEFT JOIN notifications_recieved AS nr ON nr.notification_id = n.id AND nr.user_id = $user_id
            WHERE 
              n.type = 2 AND (nr.is_read IS NULL and n.is_public <> 1) 
              AND nr.is_read IS NULL 
              AND n.date_from <= '$now' 
              AND n.date_to >= '$now'
            ORDER BY 
              n.id DESC
            LIMIT 1
        ";
        $items1 = DB::fetch($sql);

        $sql = "
            SELECT n.id, n.content, n.title
            FROM notifications AS n
            LEFT JOIN notifications_recieved AS nr ON nr.notification_id = n.id AND nr.user_id = $user_id
            WHERE 
              n.type = 2 AND (nr.is_read IS NULL and n.is_public = 1) 
              AND nr.is_read IS NULL 
              AND n.date_from <= '$now' 
              AND n.date_to >= '$now'
            ORDER BY 
              n.id DESC
            LIMIT 1
        ";
        $items2 = DB::fetch($sql);

        return !empty($items1)?$items1:$items2;
    }

    static function getTotalNotifications()
    {
        $user_id = get_user_id();
        $m_key = 'notify_'.$user_id;
        $sql1 = "
            SELECT count(n.id) AS total
            FROM notifications AS n
            LEFT JOIN notifications_recieved AS nr ON nr.notification_id = n.id AND nr.user_id = $user_id
            WHERE n.type = 1 AND ((nr.is_read = 0 AND n.is_public = 2))
        ";
        $sql2 = "
            SELECT count(n.id) AS total
            FROM notifications AS n
            LEFT JOIN notifications_recieved AS nr ON nr.notification_id = n.id AND nr.user_id = $user_id
            WHERE n.type = 1 AND ((n.is_public = 1 AND nr.is_read IS NULL))
        ";
        if ($m_key and !System::is_local()) {
            $total = MC::get_items($m_key);
            if (!$total) {
                $total1 = DB::fetch($sql1,'total');
                $total2 = DB::fetch($sql2,'total');
                $total = $total1+$total2;
                MC::set_items($m_key, $total, time() + 60*15);
            }
        } else {
            $total1 = DB::fetch($sql1,'total');
            $total2 = DB::fetch($sql2,'total');
            $total = $total1+$total2;
        }
        return $total;
    }

    static function updateNotifications()
    {
        $user_id = get_user_id();
        DB::update('notifications_recieved', ['is_read' => 1], "user_id = $user_id");
        $sql = "
            SELECT n.id
            FROM notifications AS n
            LEFT JOIN notifications_recieved AS nr ON nr.notification_id = n.id AND nr.user_id = $user_id
            WHERE n.is_public = 1 AND nr.is_read IS NULL
        ";
        $results = DB::fetch_all($sql);
        if (!empty($results)) {
            foreach ($results as $value) {
                DB::insert('notifications_recieved', [
                    'notification_id' => $value['id'],
                    'user_id' => $user_id,
                    'is_read' => 1,
                    'read_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    static function get_group_info()
    {
        $group_id = Session::get('group_id');
        return DB::fetch('
            SELECT
                id,name,code,address,phone,system_group_id
            FROM
                `groups`
            WHERE
                id='.$group_id.'
        ');
    }
}
?>
