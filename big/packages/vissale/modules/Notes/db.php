<?php

class NotesDB
{

    static function getNotesByUserId($page = 1)
    {
        $item_per_page = 10;
        if($page < 1) $page = 1;
        $offset = ($page - 1) * $item_per_page;
        $user_id = get_user_id();

        $sql = "SELECT * FROM notes WHERE user_id = $user_id AND is_pin = 2 ORDER BY updated_at DESC LIMIT $offset, $item_per_page";

        return DB::fetch_all($sql);
    }

    static function getNotesPins($user_id)
    {
        $sql = "SELECT * FROM notes WHERE user_id = $user_id AND is_pin = 1 ORDER BY updated_at DESC";

        return DB::fetch_all($sql);
    }

    static function getNotesDashboard($user_id)
    {
        return DB::fetch_all("SELECT * FROM notes WHERE user_id = $user_id ORDER BY is_pin ASC, updated_at DESC LIMIT 3");
    }

}