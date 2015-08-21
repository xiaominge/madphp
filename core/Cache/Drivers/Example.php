<?php

namespace Madphp\Src\Core\Cache\Drivers;
use Madphp\Src\Core\Cache\Base;
use Madphp\Src\Core\Cache\Driver;

class Example extends Base implements Driver
{
    function checkDriver()
    {
         return true;
         return false;
    }

    function connectServer()
    {

    }

    function __construct($config = array())
    {
        $this->setup($config);
        if (!$this->checkdriver() && !isset($config['skipError'])) {
            throw new \Exception("Can't use this driver for your website!");
        }
    }

    function driverSet($keyword, $value = "", $time = 300, $option = array())
    {
        if (isset($option['skipExisting']) && $option['skipExisting'] == true) {
            // skip driver
        } else {
            // add driver
        }
    }

    function driverGet($keyword, $option = array())
    {
        // return null if no caching
        // return value if in caching

        return null;
    }

    function driverDelete($keyword, $option = array())
    {

    }

    function driverStats($option = array())
    {
        $res = array(
            "info"  => "",
            "size"  =>  "",
            "data"  => "",
        );

        return $res;
    }

    function driverClean($option = array())
    {

    }

    function driverIsExisting($keyword)
    {

    }

}