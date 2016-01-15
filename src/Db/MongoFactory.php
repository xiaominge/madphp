<?php

namespace Madphp\Db;

class MongoFactory extends Factory
{

    public static $self = null;

    public static $modality = 'read';

    public static $instances = array();

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (self::$self === null) {
            self::$self = new static;
        }

        return self::$self;
    }

    public function createDb()
    {
        $args = func_get_args();
        $dbname = $args[0];
        $config = $args[1];
        if (isset($config['modality']) && in_array($config['modality'], array('write', 'read'))) {
            $modality = $config['modality'];
        } else {
            $modality = self::$modality;
        }

        $instance = $modality . '_' . $dbname;
        if (!isset(self::$instances[$instance])) {
            $class = __NAMESPACE__ . "\\Mongo\\MongoCore";
            if (class_exists($class)) {
                try {
                    self::$instances[$instance] = new $class($dbname, $config);
                } catch (\Exception $exc) {
                    throw new \Exception("$class can not new!");
                }
            } else {
                throw new \Exception("$class not found!");
            }
        }

        return self::$instances[$instance];
    }
}
