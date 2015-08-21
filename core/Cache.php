<?php

/**
 * Cache
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Src\Core;

class Cache
{
    public $instance;
    public function __construct($storage = "", $config = array())
    {
        $this->instance = Cache\Factory::instance($storage, $config);
    }
    
    public function __call($method, $parameters)
    {
        if ($parameters) {
            return call_user_func_array(array($this->instance, $method), $parameters);
        } else {
            return call_user_func(array($this->instance, $method));
        }
    }

    public static function isExistingDriver($name)
    {
        return Cache\Base::isExistingDriver($name);
    }

}