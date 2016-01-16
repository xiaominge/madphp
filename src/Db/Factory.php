<?php

namespace Madphp\Db;

abstract class Factory
{

    public static $instances = array();

    public static $types = array(
        'pdo', 'mongo',
    );

    public static function getInstance($type)
    {
        if (!in_array($type, self::$types)) {
            throw new \Exception("$type not found!");
        }

        if (empty(self::$instances[$type])) {
            $dbFactory = __NAMESPACE__ . "\\" . ucfirst(strtolower($type)) . "Factory";
            self::$instances[$type] = new $dbFactory;
        }

        return self::$instances[$type];
    }
}