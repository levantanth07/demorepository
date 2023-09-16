<?php
/**
 * Created by PhpStorm.
 * User: trinhdinh
 * Date: 2019-01-16
 * Time: 18:01
 */

class EditAdminProductsAjax extends Form {

    function __construct(){

    }

    function on_submit()
    {

    }

    function draw()
    {
        header('Content-type:application/json');
        $code = DataFilter::removeDuplicatedSpaces(Url::get('code'));
        $id = AdminProductsDB::get_duplicated_by_code($code);
        if ($id) {
            echo json_encode(['duplicated'=>1]);
        } else {
            echo json_encode(['duplicated'=>0]);
        }
        exit(0);
    }
}