<?php

class PrintDB
{

    static function checkExistsPrintTemplate($cond)
    {
        $result = DB::fetch("SELECT id, data, is_default, is_system FROM prints_templates AS pt WHERE $cond");
        if (!empty($result)) {
            return $result;
        }

        return false;
    }

    static function getOldPrintTemplates($group_id)
    {
        $results = DB::fetch_all("
            SELECT id, print_name, print_address, print_phone, template
            FROM order_print_template
            WHERE group_id = $group_id
            ORDER BY id ASC
        ");

        return $results;
    }

    static function getTemplateByGroupId($group_id, $paper_size)
    {
        $sql = "SELECT id, data FROM prints_templates WHERE group_id = $group_id AND paper_size = '$paper_size'";

        return DB::fetch($sql);
    }

    static function getTemplateDataSystem($paper_size)
    {
        return DB::fetch("SELECT id, data FROM prints_templates WHERE paper_size = '$paper_size' AND is_system = 1");
    }

    static function getGroupsHasTemplate()
    {
        $sql = "
            SELECT g.id, g.name
            FROM order_print_template AS opt
            INNER JOIN groups AS g ON g.id = opt.group_id
            GROUP BY opt.group_id
        ";

        return DB::fetch_all($sql);
    }
}