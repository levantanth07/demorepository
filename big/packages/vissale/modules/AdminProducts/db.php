<?php
Class AdminProductsDB {

    public static function get_duplicated_by_code($code)
    {
        $sql = "SELECT id FROM products where group_id = ".Session::get('group_id')." and code LIKE '".DB::escape($code)."' LIMIT 0,1";
        return DB::fetch($sql, 'id');
    }
}