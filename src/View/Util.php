<?php

/**
 * Util
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\View;
use Madphp\View as ViewProvider;

class Util
{
    /**
     * 渲染模板文件
     */
    public static function render(ViewProvider $object, $data = null)
    {
        $data = array_merge((array) $object->data, (array) $data);

        if ($object->isLayout) {
            $output = self::_render($object, $data);
            $object->layout->set('content', $output);
            $allData = array_merge($data, array('layoutData' => $object->layout->data));
            return self::layout($object->layout->layoutName, $allData);
        } else {
            return self::_render($object, $data);
        }
    }

    /**
     * 渲染模板文件
     */
    protected static function _render(ViewProvider $object, $data = null)
    {
        if ($object->isCompiler) {
            $viewStr = file_get_contents($object->viewFile);
            $viewCompiledStr = $object->compiler->parse($viewStr);

            $compiledFile = self::getCompiledFile($object);
            if (!file_exists($compiledFile) or filemtime($object->viewFile) > filemtime($compiledFile)) {
                $compiledPath = pathinfo($compiledFile, PATHINFO_DIRNAME);
                mkdirs($compiledPath, 0777);
                file_put_contents ($compiledFile, $viewCompiledStr);
                chmod ($compiledFile, 0777);
            }

            $output = self::load($compiledFile, $data);
        } else {
            $output = self::load($object->viewFile, $data);
        }
        return $output;
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
    
    protected static function getCompiledFile(ViewProvider $viewProvider)
    {
        $viewName = $viewProvider->viewName;
        $filePath = str_replace('.', DIRECTORY_SEPARATOR, $viewName);

        if ($viewProvider->isLayoutFile) {
            $folder = $viewProvider->layout->layoutFolder;
        } else {
            $folder = $viewProvider->viewFolder;
        }
        return $viewProvider->compiler->compilerRoot . $folder . DIRECTORY_SEPARATOR . $filePath . '.php';
    }

    public static function getFilePath(ViewProvider $viewProvider)
    {
        $viewName = $viewProvider->viewName;
        $filePath = str_replace('.', DIRECTORY_SEPARATOR, $viewName);

        if ($viewProvider->isLayoutFile) {
            $path = $viewProvider->layout->layoutPath;
        } else {
            $path = $viewProvider->viewPath;
        }
        return $path . $filePath . '.php';
    }

    /**
     * 渲染Layout
     */
    public static function layout($name, $data = null)
    {
        $object = ViewProvider::make($name, true);
        return self::render($object, $data);
    }
}

