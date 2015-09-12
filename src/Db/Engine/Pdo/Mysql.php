<?php

namespace Madphp\Db\Engine\Pdo;
use Madphp\Config;

class Mysql extends Connection
{

    var $instance;
    var $dbname;
    var $config;
    var $modality;
    var $debug = true;
    
    public static $connections = array();
    
    /**
     * 属性
     * @var array
     */
    private $_attribute = array(
        
        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
        \PDO::ATTR_STRINGIFY_FETCHES => false,
    );

    function __construct($dbname, $modality, $config)
    {
        if (!$this->checkDriver()) {
            throw new \Exception('Can not find mysql pdo driver.');
        }
        if (isset($config['debug'])) {
            $this->debug = $config['debug'];
        }
        $this->dbname = $dbname;
        $this->config = $config;
        $this->modality = $modality;
    }

    function checkDriver()
    {
        $drivers = pdo_drivers();
        if (array_search('mysql', $drivers) === FALSE) {
            return false;
        }
        return TRUE;
    }

    function connect()
    {
        $environment = defined('ENVIRONMENT') ? ENVIRONMENT : 'development';
        $connection_key = $this->dbname . $this->modality . $environment;
        
        if (!isset(self::$connections[$connection_key])) {
            $server = Config::get('db.'.$environment, 'mysql');
            $dbConfig = Config::get('db.'.$environment, 'database,'.$this->dbname);
            $dbHostConfig = explode(':', $dbConfig[$this->modality]);

            $charset = isset($server['charset']) ? $server['charset'] : 'UTF8';
            $dbname = isset($dbConfig['dbname']) ? $dbConfig['dbname'] : $this->dbname;
            $dsn = 'mysql:host=' . $dbHostConfig['0'] . ';port=' . $dbHostConfig['1'] . ';dbname=' . $dbname;
            $options = array(
                \PDO::ATTR_PERSISTENT => isset($server['persistent']) ? $server['persistent'] : true,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $charset
            );
            
            try {
                self::$connections[$connection_key] = new \PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);
                foreach ($this->_attribute as $key => $val) {
                    self::$connections[$connection_key]->setAttribute($key, $val);
                }
            } catch (\PDOException $e) {
                throw new \DebugException($e->getMessage(), $e->getCode(), $e->getPrevious());
            }
        }
        
        return self::$connections[$connection_key];
    }

}