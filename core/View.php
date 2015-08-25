<?php

/**
 * View
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Src\Core;
use Madphp\Src\Core\View\Layout as Layout;

class View
{
    public $data;
    public $view;
    public $isLayout = true;
    public $layout;

    public function __construct($viewName)
    {

        if (!defined('VIEW_PATH')) {
            throw new \InvalidArgumentException("VIEW_PATH is undefined!");
        }

        if(!$viewName) {
            throw new \InvalidArgumentException("View name can not be empty!");
        } else {
            $viewFile = self::getFilePath($viewName);
            if (!is_file($viewFile)) {
                throw new \UnexpectedValueException("View file does not exist!");
            }
        }

        $this->view = $viewFile;
        $this->data = array();
    }

    public static function make($viewName = null)
    {
        $object = new self($viewName);
        $object->layout = new Layout();
        return $object;
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
        $output = self::render($this);
        return $output;
    }

    /**
     * 输出内容
     */
    public function show()
    {
        $output = self::render($this);
        Response::setBody($output);
        $ret = Response::send();
        if (!$ret) {
            throw new \Exception("View output error!");
        }
        return true;
    }

    /**
     * 是否布局
     */
    public function isLayout($isLayout, $object = null)
    {
        if (is_object($object) && ($object instanceof View)) {
            $object->isLayout = boolval($isLayout);
        } else {
            $this->isLayout = boolval($isLayout);
        }
    }

    /**
     * 设置布局文件
     */
    public function setLayout($layoutName = null, $object = null)
    {
        if (!is_object($object) or !($object instanceof View)) {
            $object = $this;
        }

        if ($object->isLayout) {
            $object->layout->setLayout(strval($layoutName));
        }
    }

    /**
     * 多用于加载局部模板文件
     * 获取渲染模板文件的内容
     */
    public static function fetch($template, $data = null, $isLayout = false)
    {
        $object = self::make($template);
        $object->isLayout($isLayout, $object);
        return self::render($object, $data);
    }

    /**
     * 渲染模板文件
     */
    protected static function render($object, $data = null)
    {
        $data = array_merge((array) $object->data, (array) $data);
        if ($object->isLayout) {
            $output = self::_render($object, $data);

            $layoutName = 'layout.' . $object->layout->layoutName;
            $object->layout->set('content', $output);
            $allData = array_merge($data, array('layoutData' => $object->layout->data));
            $allOutput = $object->fetch($layoutName, $allData);
            return $allOutput;
        } else {
            return self::_render($object, $data);
        }
    }

    /**
     * 渲染模板文件
     */
    protected static function _render($object, $data = null)
    {
        if ($object instanceof View) {
            extract($data);
            ob_start();
            require $object->view;
            $output = ob_get_contents();
            ob_end_clean();

            return $output;
        } else {
            throw new \UnexpectedValueException("\$object must be instance of View!");
        }
    }

    protected static function getFilePath($viewName)
    {
        $filePath = str_replace('.', '/', $viewName);
        return VIEW_PATH . $filePath . '.php';
    }

    public function __call($method, $parameters)
    {
        if (starts_with($method, 'with')) {
            return $this->with(snake_case(substr($method, 4)), $parameters[0]);
        }

        throw new \BadMethodCallException("Function [$method] does not exist!");
    }
}