<?php

/**
 * 注册模式
 */
namespace Madphp\Db;

abstract class Factory
{

    public static $instances = array();

    public static $types = array(
        'pdo', 'mongo',
    );

    public static function registry()
    {
        foreach (self::$types as $type) {
            $class = __NAMESPACE__ . "\\" . ucfirst(strtolower($type)) . "Factory";
            self::setInstance($type, new $class);
        }
    }

    public static function getInstance($type)
    {
        if (self::checkInstance($type)) {
            return self::$instances[$type];
        }

        throw new \Exception("$type not found!");
    }

    public static function setInstance($type, $obj)
    {
        self::$instances[$type] = $obj;
    }

    public static function checkInstance($type)
    {
        if (!in_array($type, self::$types)) {
            throw new \Exception("$type not define!");
        }

        if (empty(self::$instances[$type])) {
            return false;
        }
        return true;
    }
}