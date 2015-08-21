<?php

namespace Madphp\Src\Core\Cache;

interface Driver
{
    /*
     * Check if this Cache driver is available for server or not
     */
    function __construct($config = array());

    function checkDriver();

    /*
     * SET
     * set a obj to cache
     */
    function driverSet($keyword, $value = "", $time = 300, $option = array());

    /*
     * GET
     * return null or value of cache
     */
    function driverGet($keyword, $option = array());

    /*
     * Stats
     * Show stats of caching
     * Return array ("info","size","data")
     */
    function driverStats($option = array());

    /*
     * Delete
     * Delete a cache
     */
    function driverDelete($keyword, $option = array());

    /*
     * clean
     * Clean up whole cache
     */
    function driverClean($option = array());

    /*
     * isExisting
     */
    function driverIsExisting($keyword);
}