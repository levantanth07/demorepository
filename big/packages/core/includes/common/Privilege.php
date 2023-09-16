<?php
class Privilege
{
    static function xemBcDoanhThuHeThong() {
        $allowAccounts = ['PAL.khoand', 'pal.hoangpv', 'palshop', 'pal.support1', 'pal.huyenkute', 'pal.support2'];
        return in_array(Session::get('user_id'), $allowAccounts);
    }
}