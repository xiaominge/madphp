<?php

namespace Madphp\Src\Core\Cache;

class Factory
{
    protected static $tmp = array();
    public static $disabled = false;
    public static $config = array(
        "storage"       =>  "", // blank for auto
        "default_chmod" =>  0777, // 0777 , 0666, 0644
        /*
         * Fall back when old driver is not support
         */
        "fallback"  => "file",

        "securityKey"   =>  "auto",
        "htaccess"      =>  true,
        "path"          =>  "",

        "memcache"      =>  array(
            array("127.0.0.1", 11211, 1),
        ),

        "redis"         =>  array(
            "host"      =>  "127.0.0.1",
            "port"      =>  "",
            "password"  =>  "",
            "database"  =>  "",
            "timeout"   =>  ""
        ),

        "extensions"    =>  array(),
    );

    private function __construct() {}

    public static function instance($storage = "", $config = array())
    {
        return Instance::get($storage, $config);
    }

    public static function getPath($skip_create_path = false, $config)
    {
        if ($config['path'] == '') {
            // revision 618
            if (self::isPHPModule()) {

                $tmp_dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
                $path = $tmp_dir;

            } else {
                $path = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], "/").'/' : rtrim(dirname(__FILE__), "/")."/";
            }

            if (self::$config['path'] != "") {
                $path = $config['path'];
            }
        } else {
            $path = $config['path'];
        }

        $securityKey = $config['securityKey'];
        if ($securityKey == "" || $securityKey == "auto") {
            $securityKey = self::$config['securityKey'];
            if ($securityKey == "auto" || $securityKey == "") {
                $securityKey = isset($_SERVER['HTTP_HOST']) ? ltrim(strtolower($_SERVER['HTTP_HOST']),"www.") : "default";
                $securityKey = preg_replace("/[^a-zA-Z0-9]+/", "", $securityKey);
            }
        }
        if ($securityKey != "") {
            $securityKey .= "/";
        }

        $full_path = rtrim($path, "/")."/".$securityKey;
        $full_pathx = md5($full_path);

        if ($skip_create_path  == false && !isset(self::$tmp[$full_pathx])) {
            if (!file_exists($full_path) || !is_writable($full_path)) {
                if (!file_exists($full_path)) {
                    mkdirs($full_path, self::setChmodAuto($config));
                }
                if (!is_writable($full_path)) {
                    chmod($full_path, self::setChmodAuto($config));
                }
                if (!file_exists($full_path) || !is_writable($full_path)) {
                    die("Sorry, Please create ".$full_path." and SET Mode 0777 or any Writable Permission!");
                }
            }
            self::$tmp[$full_pathx] = true;
            self::htaccessGen($full_path, $config['htaccess']);
        }

        return $full_path;
    }

    public static function isPHPModule()
    {
        if (PHP_SAPI == "apache2handler") {
            return true;
        } else {
            if (strpos(PHP_SAPI, "handler") !== false) {
                return true;
            }
        }
        return false;
    }

    public static function setChmodAuto($config)
    {
        if ($config['default_chmod'] == "" || is_null($config['default_chmod'])) {
            return 0777;
        } else {
            return $config['default_chmod'];
        }
    }

    protected static function htaccessGen($path, $create = true)
    {
        if ($create == true) {
            if (!is_writeable($path)) {
                try {
                    chmod($path,0777);
                } catch (\Exception $e) {
                    die(" NEED WRITEABLE ".$path);
                }
            }
            if (!file_exists($path."/.htaccess")) {
                //   echo "write me";
                $html = "order deny, allow \r\ndeny from all \r\nallow from 127.0.0.1";

                $f = @fopen($path."/.htaccess", "w+");
                if (!$f) {
                    die(" CANT CREATE HTACCESS TO PROTECT FOLDER - PLZ CHMOD 0777 FOR ".$path);
                }
                fwrite($f, $html);
                fclose($f);
            }
        }
    }

    protected static function getOS()
    {
        $os = array(
            "os" => PHP_OS,
            "php" => PHP_SAPI,
            "system" => php_uname(),
            "unique" => md5(php_uname().PHP_OS.PHP_SAPI)
        );
        return $os;
    }

    public static function setup($name, $value = "")
    {
        if (is_array($name)) {
            self::$config = $name;
        } else {
            self::$config[$name] = $value;
        }
    }
}