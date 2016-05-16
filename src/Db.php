<?php

/**
 * Db
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;
Db\Factory::registry();

class Db
{
    private function __construct()
    {

    }
    
    public static function pdo($dbname, $config = array(), $type = "mysql")
    {
        return Db\Factory::getInstance('pdo')->createDb($dbname, $config, $type);
    }
    
    public static function mongo($dbname, $config = array())
    {
        return Db\Factory::getInstance('mongo')->createDb($dbname, $config);
    }
}