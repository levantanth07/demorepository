<?php
require_once ROOT_PATH.'vendor/autoload.php';
require_once ROOT_PATH.'config/message_queue_keys.php';
require_once ROOT_PATH.'cache/db/redis.php';
/**
 * Redis adapter
 * User: AnLT
 * Date: 8/11/2020
 * Time: 9:48 AM
 */
use RedisClient\RedisClient;
use RedisClient\ClientFactory;

class message_queue
{
    /**
     * @var redis_adapter
     */
    private static $_intance;
    /**
     * @var RedisClient
     */
    private $_redis;

    /**
     * @return message_queue
     */
    public static function getInstance(){
        if(self::$_intance != null){
            return self::$_intance;
        }
        self::$_intance = new message_queue();
        self::$_intance->init();
        return self::$_intance;
    }

    private function init(){
        $this->_redis = ClientFactory::create(REDIS_MESSAGE_QUEUE);
    }

    /**
     * @param $key
     * @param $value object to sync
     */
    private function lPush($key, $value){
        $this->_redis->lpush($key, $value);
    }

    /**
     * trigger pushed event
     * @param $table
     * @param $before
     * @param $after
     */
    // nhập dữ liệu bắn sang bên CRM
    public function publishPushedEventInsert($table, $before, $after){
        $stdClass = new stdClass();
        $stdClass->provider = PROVIDER;
        $stdClass->before = $before;
        $stdClass->after = $after;
        $channel = $this->getChannelPrefix() . PROVIDER."::".DATA_ADDED."::".$table;

        $this->_redis->publish( $channel, json_encode($stdClass) );
    }

    // update dữ liệu sang bên CRM
    public function publishPushedEventUpdate($table, $before, $after){
        $stdClass = new stdClass();
        $stdClass->provider = PROVIDER;
        $stdClass->before = $before;
        $stdClass->after = $after;
        $channel = $this->getChannelPrefix() . PROVIDER."::".DATA_UPDATED."::".$table;

        $this->_redis->publish( $channel, json_encode($stdClass) );
    }

    // delete dữ liệu sang bên CRM
    public function publishPushedEventDelete($table, $before, $after){
        $stdClass = new stdClass();
        $stdClass->provider = PROVIDER;
        $stdClass->before = $before;
        $stdClass->after = $after;
        $channel = $this->getChannelPrefix() . PROVIDER."::".DATA_DELETED."::".$table;

        $this->_redis->publish( $channel, json_encode($stdClass) );
    }

    /**
     * @return string
     */
    protected function getChannelPrefix() {
        if (! empty(REDIS_MESSAGE_QUEUE['prefix'])) {
            return REDIS_MESSAGE_QUEUE['prefix'] . '::';
        }

        return '';
    }

    /**
     * @return RedisClient
     */
    public function getRedis() {
        return $this->_redis;
    }
}