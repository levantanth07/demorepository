<?php

use RedisClient\RedisClient;

class PhpRedisInstance
{   
    private static $instance = null;

    /**
     * Constructs a new instance.
     */
    private function __construct() {}

    /**
     * Gets the instance.
     *
     * @throws     Exception  (description)
     *
     * @return     <type>     The instance.
     */
    public static function getInstance()
    {
        if (!defined('REDIS_LOGIN_TOKEN') || !is_array(REDIS_LOGIN_TOKEN)) {
            throw new Exception('Vui lòng cung cấp thông tin kết nối redis hợp lệ !');
        }

        if (is_null(self::$instance)) {
            self::$instance = new RedisClient(REDIS_LOGIN_TOKEN);
        }

        return self::$instance;
    }
}