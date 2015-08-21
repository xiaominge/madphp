<?php

/**
 * 函数库
 * @author 徐亚坤 hdyakun@sina.com
 */

/**
 * 图片别名加载
 * @param  dynamic  mixed  配置文件中的别名
 * @return string
 */
function img()
{
    $configKey = 'extend.imgAliases';

    $args = array_merge(array($configKey), func_get_args());
    return call_user_func_array('\Madphp\Src\Core\View\Widget::img', $args);
}

/**
 * 样式别名加载
 * @param  dynamic  mixed  配置文件中的别名
 * @return string
 */
function style()
{
    $configKey = 'extend.cssAliases';

    $args = array_merge(array($configKey), func_get_args());
    return call_user_func_array('\Madphp\Src\Core\View\Widget::style', $args);
}

/**
 * 脚本别名加载
 * @param  dynamic  mixed  配置文件中的别名
 * @return string
 */
function script()
{
    $configKey = 'extend.jsAliases';

    $args = array_merge(array($configKey), func_get_args());
    return call_user_func_array('\Madphp\Src\Core\View\Widget::script', $args);
}