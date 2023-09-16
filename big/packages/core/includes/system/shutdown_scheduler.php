<?php

/**
 * Class shutdownScheduler
 */
class ShutdownScheduler {
    private $callbacks;

    public function __construct() {
        $this->callbacks = [];
        register_shutdown_function([$this, 'callRegisteredShutdown']);
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasKeyRegistered($key) {
        return array_key_exists($key, $this->callbacks);
    }

    public function registerShutdownEvent($key, $callback) {
        if (! is_callable($callback)) {
            return false;
        }

        $this->callbacks[$key] = $callback;
        return true;
    }

    /**
     * @return void
     */
    public function callRegisteredShutdown() {
        ksort($this->callbacks);

        foreach ($this->callbacks as $callback) {
            call_user_func($callback);
        }

        $this->callbacks = [];
    }
}