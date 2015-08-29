<?php

/**
 * View
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Src\Core;
use Madphp\Src\Core\View\Layout as Layout;
use Madphp\Src\Core\View\Compiler as Compiler;
use Madphp\Src\Core\View\Util as ViewUtil;

class View
{
    public $view;

    public $viewName;

    public $layout;

    public $compiler;

    public $data = array();

    public $isLayout = true;

    public $isCompiler = true;
    
    public function __construct($viewName)
    {
        if (!defined('VIEW_PATH')) {
            throw new \InvalidArgumentException("VIEW_PATH is undefined!");
        }

        if(!$viewName) {
            throw new \InvalidArgumentException("View name can not be empty!");
        } else {
            $viewFile = ViewUtil::getFilePath($viewName);
            if (!is_file($viewFile)) {
                throw new \UnexpectedValueException("View file does not exist!");
            }
        }

        $this->view = $viewFile;
        $this->viewName = $viewName;
        $this->layout = new Layout();
        $this->compiler = new Compiler();
    }

    /**
     * 获取视图对象
     * @param null $viewName
     * @return View
     */
    public static function make($viewName = null)
    {
        return new self($viewName);
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
        return ViewUtil::render($this);
    }

    /**
     * 输出内容
     */
    public function show()
    {
        $output = ViewUtil::render($this);
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
     * 是否启用模板引擎
     */
    public function isCompiler($isCompiler, $object = null)
    {
        if (is_object($object) && ($object instanceof View)) {
            $object->isCompiler = boolval($isCompiler);
        } else {
            $this->isCompiler = boolval($isCompiler);
        }
    }

    /**
     * 设置模板引擎
     */
    public function setCompiler($compilerEngineName = null, $object = null)
    {
        if (!is_object($object) or !($object instanceof View)) {
            $object = $this;
        }

        if ($object->isCompiler) {
            $object->compiler->setCompiler(strval($compilerEngineName));
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
        return ViewUtil::render($object, $data);
    }

    public function __call($method, $parameters)
    {
        if (starts_with($method, 'with')) {
            return $this->with(snake_case(substr($method, 4)), $parameters[0]);
        }

        throw new \BadMethodCallException("Function [$method] does not exist!");
    }
}