<?php

class Groups
{
    /**
     * Di chuyển các shop từ hệ thống này sang hệ thống khác
     *
     * @param      int         $fromID  ID hệ thống cần di chuyển đi
     * @param      int|string  $toID    ID hệ thống ần di chuyển đến
     *
     * @return     bool  
     */
    public static function moveToSystem(int $fromID, int $toID)
    {
        return DB::update('groups', ['system_group_id' => $toID], '`system_group_id` = ' . $fromID);
    }
}