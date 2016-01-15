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
    
    public static function pdo($dbname, $config = array(), $type = "mysql")
    {
        return Db\PdoFactory::getInstance()->create($dbname, $config, $type);
    }
    
    public static function mongo($dbname, $config = array())
    {
        return Db\MongoFactory::getInstance()->create($dbname, $config);
    }
}