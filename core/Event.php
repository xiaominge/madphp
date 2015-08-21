<?php

/**
 * Event
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Src\Core;
use Madphp\Src\Core\Event\Dispatcher;

class Event
{
    public static function getInstance()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new Dispatcher();
        }
        return $instance;
    }

    private function __construct()
    {

    }

    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array(array(self::getInstance(), $method), $parameters);
    }

    private function __clone() { }

    private function __wakeup() { }
}