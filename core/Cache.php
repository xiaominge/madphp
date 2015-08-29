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

    /**
     * 检测缓存驱动是否存在
     * @param $name
     * @return bool
     */
    public static function isExistingDriver($name)
    {
        return Cache\Provider::isExistingDriver($name);
    }

}