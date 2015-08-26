<?php

/**
 * Compiler
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Src\Core\View;

class Compiler
{
    public $data = array();
    
    public $engineName = 'litwit';

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
     * 设置引擎
     */
    public function setCompiler($compilerEngineName = null)
    {
        $this->engineName = strval($compilerEngineName);
    }
    
    /**
     * 获取引擎
     */
    public function getCompiler()
    {
        return $this->engineName;
    }
    
    /**
     * 获取解析后的内容
     */
    public function parse($str)
    {
        $engineName = __NAMESPACE__."\\Compiler\\".ucfirst($this->engineName);
        $engine = new $engineName;
        $parseStr = $engine->parse($str);
        return $parseStr;
    }
    
}
