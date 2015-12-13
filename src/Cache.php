<?php

/**
 * Cache
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;

class Cache
{

    public static function __callstatic($method, $parameters)
    {
        return Cache\Factory::instance(strtolower($method), $parameters[0]);
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