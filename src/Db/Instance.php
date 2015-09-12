<?php

/**
 * Db Instance
 * 获取数据库连接实例
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Db;

class Instance
{
    public static $instances = array();
    public static $modality = 'read';
    
    public static function get($dbname, $type, $config, $pdo)
    {
        if (isset($config['modality']) && in_array($config['modality'], array('write', 'read'))) {
            $modality = $config['modality'];
        } else {
            $modality = self::$modality;
        }
        $pdoStr = $pdo ? '_pdo' : '';
        $instance = $type . $pdoStr . '_' . $modality . '_' . $dbname;
        $classPrefix = $pdo ? '\Engine\Pdo\\' : '\Engine\\';
        if (!isset(self::$instances[$instance])) {
            $class = __NAMESPACE__ . $classPrefix . ucfirst(strtolower($type));
            if (class_exists($class)) {
                try {
                    switch ($type) {
                        case 'mongo':
                            self::$instances[$instance] = new $class($dbname, $config);
                            break;
                        default :
                            self::$instances[$instance] = new $class($dbname, $modality, $config);
                            break;
                    }
                } catch (Exception $exc) {
                    throw new \Exception("$class can not new!");
                }
            } else {
                throw new \Exception("$class not found!");
            }
        }

        return self::$instances[$instance];
    }
}