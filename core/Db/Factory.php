<?php

namespace Madphp\Src\Core\Db;

class Factory
{
    private function __construct() {}

    public static function instance($dbname, $type, $config, $pdo)
    {
        return Instance::get($dbname, $type, $config, $pdo);
    }
}