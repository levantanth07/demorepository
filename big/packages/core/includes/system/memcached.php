<?php

class MC
{
    static $m = false;

    function __construct()
    {
        MC::$m = new Memcached();
        $port = 11211;
        MC::$m->addServer('localhost', $port) or die ("Không thể kết nối Memcached. Vui lòng kiểm tra lại!");
        return MC::$m;
    }

    static function get_items($key)
    {
        $get_mc = MC::$m->get($key);
        return $get_mc;
    }

    static function set_items($m_key, $items, $second)
    {
        MC::$m->set($m_key, $items, $second);
    }

    static function delete_item($m_key)
    {
        MC::$m->delete($m_key);
    }
}

$mc = new MC();
?>