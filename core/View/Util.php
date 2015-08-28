<?php

/**
 * Util
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Src\Core\View;
use Madphp\Src\Core\View as View;

class Util
{
    /**
     * 渲染模板文件
     */
    public static function render($object, $data = null)
    {
        $data = array_merge((array) $object->data, (array) $data);
        if ($object->isLayout) {

            if (!defined('LAYOUT_FOLDER')) {
                throw new \InvalidArgumentException("LAYOUT_FOLDER is undefined!");
            }

            $output = self::_render($object, $data);
            $layoutName = LAYOUT_FOLDER . '.' . $object->layout->layoutName;
            $object->layout->set('content', $output);
            $allData = array_merge($data, array('layoutData' => $object->layout->data));
            return $object->fetch($layoutName, $allData);
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
            if ($object->isCompiler) {
                $viewStr = file_get_contents($object->view);
                $viewCompiledStr = $object->compiler->parse($viewStr);
                
                $compiledFile = self::getCompiledFile($object->viewName);
                if (!file_exists($compiledFile) or filemtime($object->view) > filemtime($compiledFile)) {
                    $compiledPath = pathinfo($compiledFile, PATHINFO_DIRNAME);
                    mkdirs($compiledPath);
                    file_put_contents ($compiledFile, $viewCompiledStr);
                    chmod ($compiledFile, 0777);
                }
                
                $output = self::load($compiledFile, $data);
            } else {
                $output = self::load($object->view, $data);
            }
            return $output;
        } else {
            throw new \UnexpectedValueException("\$object must be instance of View!");
        }
    }
    
    protected static function load($file, $data = null)
    {
        extract($data);
        ob_start();
        require $file;
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
    
    protected static function getCompiledFile($viewName = null)
    {
        if (!defined('CACHE_PATH')) {
            throw new \InvalidArgumentException("CACHE_PATH is undefined!");
        }
        $viewPath = str_replace('.', DIRECTORY_SEPARATOR, $viewName);
        return CACHE_PATH . VIEW_FOLDER . DIRECTORY_SEPARATOR . $viewPath . '.php';
    }

    public static function getFilePath($viewName)
    {
        $filePath = str_replace('.', DIRECTORY_SEPARATOR, $viewName);
        return VIEW_PATH . $filePath . '.php';
    }
}

