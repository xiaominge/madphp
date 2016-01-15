<?php

namespace Madphp\Db;

class PdoFactory extends Factory
{

    public static $modality = 'read';

    public static $instances = array();

    public function __construct()
    {

    }

    public function createDb()
    {
        $args = func_get_args();
        $dbname = $args[0];
        $config = $args[1];
        $type = $args[2];
        if (isset($config['modality']) && in_array($config['modality'], array('write', 'read'))) {
            $modality = $config['modality'];
        } else {
            $modality = self::$modality;
        }

        $instance = $type . '_pdo_' . $modality . '_' . $dbname;
        $classPrefix = '\Pdo\\Engine\\';
        if (!isset(self::$instances[$instance])) {
            $class = __NAMESPACE__ . $classPrefix . ucfirst(strtolower($type));
            if (class_exists($class)) {
                try {
                    self::$instances[$instance] = new $class($dbname, $modality, $config);
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
