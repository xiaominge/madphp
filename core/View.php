<?php

/**
 * View
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Src\Core;

class View
{
    public $data;
    public $view;

    public function __construct($view)
    {
        $this->view = $view;
    }
    
    public static function make($viewName = null)
    {
        if (!defined('VIEW_PATH')) {
            throw new \InvalidArgumentException("VIEW_PATH is undefined!");
        }

        if(!$viewName) {
            throw new \InvalidArgumentException("View name can not be empty!");
        } else {
            $viewFilePath = self::getFilePath($viewName);
            if (is_file($viewFilePath)) {
                return new self($viewFilePath);
            } else {
                throw new \UnexpectedValueException("View file does not exist!");
            }
        }
    }

    /**
     * 添加变量
     */
    public function with($key, $value = null)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * 获取输出内容
     */
    public function complete()
    {
        return self::process($this, true);
    }

    /**
     * 输出内容
     */
    public function show()
    {
        return self::process($this, false);
    }

    public function __call($method, $parameters)
    {
        if (starts_with($method, 'with')) {
            return $this->with(snake_case(substr($method, 4)), $parameters[0]);
        }

        throw new \BadMethodCallException("Function [$method] does not exist!");
    }

    private static function getFilePath($viewName)
    {
        $filePath = str_replace('.', '/', $viewName);
        return VIEW_PATH . $filePath . '.php';
    }

    private static function process($object, $return = false)
    {
        if ($object instanceof View) {
            extract($object->data);
            ob_start();
            require $object->view;
            $output = ob_get_contents();
            ob_end_clean();
            if ($return) {
                return $output;
            }
            Response::setBody($output);
            $ret = Response::send();
            if (!$ret) {
                throw new \Exception("View output error!");
            }
        } else {
            throw new \UnexpectedValueException("\$object must be instance of View!");
        }
    }
}