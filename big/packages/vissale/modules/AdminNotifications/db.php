<?php

class NotificationsDB
{

    static function getNotifications($cond = [])
    {
        $sql = "
            SELECT 
                n.id, n.notificationable_id, n.content, n.created_at, n.is_public, GROUP_CONCAT(DISTINCT groups.name) AS group_name,
                n.type, n.date_from, n.date_to, n.title
            FROM notifications AS n
            LEFT JOIN notifications_recieved AS nr ON nr.notification_id = n.id
            left join `groups` on groups.id = nr.group_id
            WHERE n.notificationable_type = 2 ". implode(" ", $cond) ."
            GROUP BY n.id
            ORDER BY n.id DESC
        ";

        return DB::fetch_all($sql);
    }

    static function getGroups()
    {
        $sql = "
            SELECT id, name, code
            from `groups`
            WHERE active = 1
        ";

        return DB::fetch_all($sql);
    }

    static function getUsersSystemOfGroup($group_id)
    {
        $sql = "
            SELECT u.id
            FROM users AS u INNER JOIN account AS a ON a.id = u.username
            WHERE u.group_id = $group_id AND u.status = 1 AND admin_group = 1
        ";

        return DB::fetch_all($sql);
    }

    static function getGroupsByNotification($id)
    {
        $groups_id = [];
        $items = DB::fetch_all("SELECT id, group_id FROM notifications_recieved WHERE notification_id = $id GROUP BY group_id");
        if (!empty($items)) {
            foreach ($items as $value) {
                $groups_id[] = $value['group_id'];
            }
        }

        return $groups_id;
    }
}