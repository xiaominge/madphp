<?php

/**
 * Layout
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Core\View;

class Layout
{
    public $data = array();

    public $layoutName = 'default';

    public function __construct()
    {

    }

    public function set($key, $val)
    {
        $this->data[$key] = $val;
    }

    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return $default;
    }

    /**
     * 设置布局文件
     */
    public function setLayout($layoutName = null)
    {
        $this->layoutName = strval($layoutName);
    }
}
