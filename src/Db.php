<?php

/**
 * Db
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;

class Db
{
    private function __construct()
    {

    }
    
    public static function connection($dbname, $config = array(), $type = "mysql", $pdo = false)
    {
        return Db\Factory::instance($dbname, $type, $config, $pdo);
    }
    
    public static function pdo($dbname, $config = array(), $type = "mysql")
    {
        return self::connection($dbname, $config, $type, true);
    }
    
    public static function mongo($dbname, $config = array())
    {
        return self::connection($dbname, $config, 'mongo', false);
    }
}