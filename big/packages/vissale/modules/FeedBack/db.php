<?php

class FeedBackDB
{

    static function getGroups()
    {
        $sql = "
            SELECT id, name, code
            from `groups`
            WHERE active = 1
        ";

        return DB::fetch_all($sql);
    }

    static function getAllFeedBacks($cond)
    {
        return DB::fetch("
            SELECT COUNT(f.id) AS total
            FROM feedbacks AS f
            WHERE f.id > 0 ". implode(" ", $cond) ."
        ", "total");
    }

    static function getFeedBacks($conds = [], $item_per_page = '')
    {
        $limit = "";
        if (!empty($item_per_page)) {
            $limit = (page_no() - 1) * $item_per_page;
            $limit = "LIMIT $limit, $item_per_page";
        }

        $sql = "
            SELECT
                f.*, u.username, g.name AS group_name
            FROM feedbacks AS f
            INNER JOIN users AS u ON u.id = f.user_id
            INNER JOIN groups AS g ON g.id = f.group_id
            WHERE f.id > 0 ". implode(" ", $conds) ."
            ORDER BY f.id DESC
            $limit
        ";

        return DB::fetch_all($sql);
    }
}