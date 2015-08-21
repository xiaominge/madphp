<?php

/**
 * Widget
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Src\Core\View;
use Madphp\Src\Core\Config;

class Widget
{

    /**
     * 图片别名加载
     * @param  dynamic  mixed  配置文件中的别名
     * @return string
     */
    static function img()
    {
        $configKey = func_get_arg(0);
        list($file, $key) = explode('.', $configKey);
        $args = func_get_args();
        array_shift($args);
        $imgAliases = Config::get($file, $key);
        $imgArray = array_map(function ($aliases) use ($imgAliases) {
            if (isset($imgAliases[$aliases])) {
                return Html::image($imgAliases[$aliases]);
            }
        }, $args);
        return implode('', array_filter($imgArray));
    }

    /**
     * 样式别名加载
     * @param  dynamic  mixed  配置文件中的别名
     * @return string
     */
    static function style()
    {
        $configKey = func_get_arg(0);
        list($file, $key) = explode('.', $configKey);
        $args = func_get_args();
        array_shift($args);
        $cssAliases = Config::get($file, $key);
        $styleArray = array_map(function ($aliases) use ($cssAliases) {
            if (isset($cssAliases[$aliases])) {
                return Html::style($cssAliases[$aliases]);
            }
        }, $args);
        return implode('', array_filter($styleArray));
    }

    /**
     * 脚本别名加载
     * @param  dynamic  mixed  配置文件中的别名
     * @return string
     */
    static function script()
    {
        $configKey = func_get_arg(0);
        list($file, $key) = explode('.', $configKey);
        $args = func_get_args();
        array_shift($args);
        $jsAliases = Config::get($file, $key);
        $scriptArray = array_map(function ($aliases) use ($jsAliases) {
            if (isset($jsAliases[$aliases])) {
                return Html::script($jsAliases[$aliases]);
            }
        }, $args);
        return implode('', array_filter($scriptArray));
    }
}