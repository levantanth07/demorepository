<?php

class NotificationDB
{

    static function getPublicNotifications($page = 1)
    {
        $item_per_page = 20;
        $limit = ($page - 1) * $item_per_page;
        $sql = "
            SELECT n.id, n.notificationable_id, n.content, n.created_at, n.notificationable_type, n.title
            FROM notifications AS n
            LEFT JOIN notifications_recieved AS nr ON nr.notification_id = n.id
            WHERE n.type = 1 AND n.is_public = 1  and is_excel_notification != 1 and is_print_notification != 1
            ORDER BY n.id DESC
            LIMIT $limit, $item_per_page
        ";
        return DB::fetch_all($sql);
    }
    static function getUserNotifications($page = 1){
        $user_id = get_user_id();
        $item_per_page = 20;
        $limit = ($page - 1) * $item_per_page;
        $sql = "
            SELECT n.id, n.notificationable_id, n.content, n.created_at, n.notificationable_type, n.title
            FROM notifications AS n
            LEFT JOIN notifications_recieved AS nr ON nr.notification_id = n.id
            WHERE n.type = 1 AND nr.user_id = $user_id  and is_excel_notification != 1 and is_print_notification != 1
            ORDER BY n.id DESC
            LIMIT $limit, $item_per_page
        ";
        return DB::fetch_all($sql);
    }

    static function getUserExportExcelNotifications($page = 1){
        $user_id = get_user_id();
        $item_per_page = 20;
        $limit = ($page - 1) * $item_per_page;
        $sql = "
            SELECT n.id, n.notificationable_id, n.content, n.created_at, n.notificationable_type, n.title
            FROM notifications AS n
            LEFT JOIN notifications_recieved AS nr ON nr.notification_id = n.id
            WHERE n.type = 1 AND nr.user_id = $user_id and is_excel_notification = 1
            ORDER BY n.id DESC
            LIMIT $limit, $item_per_page
        ";
        return DB::fetch_all($sql);
    }

    static function getUserPrintNotifications($page = 1){
        $user_id = get_user_id();
        $item_per_page = 20;
        $limit = ($page - 1) * $item_per_page;
        $sql = "
            SELECT n.id, n.notificationable_id, n.content, n.created_at, n.notificationable_type, n.title
            FROM notifications AS n
            LEFT JOIN notifications_recieved AS nr ON nr.notification_id = n.id
            WHERE n.type = 1 AND nr.user_id = $user_id and is_print_notification = 1
            ORDER BY n.id DESC
            LIMIT $limit, $item_per_page
        ";
        return DB::fetch_all($sql);
    }
}