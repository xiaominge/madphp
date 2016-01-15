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
        $o = new Db\PdoFactory();
        return $o->create($dbname, $config, $type);
    }
    
    public static function mongo($dbname, $config = array())
    {
        $o = new Db\MongoFactory();
        return $o->create($dbname, $config);
    }
}