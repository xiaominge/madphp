<?php

/**
 * 数组函数库
 * @author 徐亚坤 hdyakun@sina.com
 */

/**
 * 计算数组深度
 * @author 徐亚坤
 * @param array $array
 * @return int
 */
if (!function_exists('array_depth')) {
    function array_depth($array)
    {
        $max_depth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $max_depth = array_depth($value) + 1;
            }
        }
        return $max_depth;
    }
}

/**
 * 将多维数组转为一维数组
 * @author 徐亚坤
 * @param array $arr
 * @return array
 */
if (!function_exists('array_tile')) {
    function array_tile($arr)
    {
        // 将数值第一元素作为容器，作地址赋值。
        $ar_room =& $arr[key($arr)];
        // 第一容器不是数组进去转
        if (!is_array($ar_room)) {
            // 转为成数组
            $ar_room = array($ar_room);
        }
        // 指针下移
        next($arr);
        // 遍历
        while (list($k, $v) = each($arr)) {
            // 是数组就递归深挖，不是就转成数组
            $v = is_array($v) ? call_user_func(__FUNCTION__, $v) : array($v);
            // 递归合并
            $ar_room = array_merge_recursive($ar_room, $v);
            // 释放当前下标的数组元素
            unset($arr[$k]);
        }
        return $ar_room;
    }
}

/**
 * 兼容不支持 array_column 函数的 PHP 版本
 */
if (!function_exists('array_column')) {
    function array_column(array $array, $column_key, $index_key = null)
    {
        $result = [];
        foreach ($array as $arr) {
            if (!is_array($arr)) {
                continue;
            }

            $value = is_null($column_key) ? $arr : $arr[$column_key];

            if (!is_null($index_key)) {
                $key = $arr[$index_key];
                $result[$key] = $value;
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }
}

/**
 * 对象转数组, 使用 get_object_vars 返回对象属性组成的数组
 */
function objectToArray($obj)
{
    $arr = is_object($obj) ? get_object_vars($obj) : $obj;
    // $arr = is_object($obj) ? (array) $obj : $obj;
    if (is_array($arr)) {
        return array_map(__FUNCTION__, $arr);
    } else {
        return $arr;
    }
}

/**
 * 数组转对象
 */
function arrayToObject($arr)
{
    if (is_array($arr)) {
        return (object) array_map(__FUNCTION__, $arr);
    } else {
        return $arr;
    }
}