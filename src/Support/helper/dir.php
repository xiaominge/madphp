<?php

/**
 * 目录函数库
 * @author 徐亚坤 hdyakun@sina.com
 */

/**
 * 转化 \ 为 /
 * @author 徐亚坤
 * @param string $path 路径
 * @return string 路径
 */
if (!function_exists('dir_path')) {
    function dir_path($path)
    {
        $path = str_replace('\\', '/', $path);
        if (substr($path, -1) != '/') {
            $path = $path . '/';
        }
        return $path;
    }
}

/**
 * 创建目录
 * @author 徐亚坤
 * @param string $path 路径
 * @param string $mode 属性
 * @return string 如果已经存在则返回true，否则为flase
 */
if (!function_exists('dir_create')) {
    function dir_create($path, $root_dir, $mode = 0777)
    {
        if (is_dir($path)) {
            @chmod($path, 0777);
        }
        if (is_dir($path) || $path == '') {
            return true;
        }

        $path = dir_path($path);
        // 截取不包括根目录的路径
        $path = str_replace(str_replace('\\', '/', $root_dir), '', $path);
        // 路径数组
        $temp = array_filter(explode('/', $path));
        // 当前目录
        $cur_dir = $root_dir;

        foreach ($temp as $name) {
            $cur_dir .= $name . '/';
            if (@is_dir($cur_dir)) continue;
            @mkdir($cur_dir, 0777, true);
            @chmod($cur_dir, 0777);
        }
        return is_dir($root_dir.$path);
    }
}

/**
 * 递归创建目录
 */
if (!function_exists('mkdirs')) {
    function mkdirs($dir, $mode = '0777', $recursive = true)
    {
        if (is_null($dir) || $dir === "") {
            return FALSE;
        }
        if (is_dir($dir) || $dir === "/") {
            chmod($dir, $mode);
            return TRUE;
        }
        if (mkdirs(dirname($dir), $mode, $recursive)) {
            try {
                return mkdir($dir, $mode);
            } catch(\Exception $e) {
                throw new \Exception("Can't create dir '".$dir."' with mode " . $mode);
            }
        }
        return FALSE;
    }
}

/**
 * 列出目录下所有文件
 * @author 徐亚坤
 * @param string $path 路径
 * @param string $exts 扩展名
 * @param array $list 增加的文件列表
 * @return array 所有满足条件的文件
 */
if (!function_exists('dir_list')) {
    function dir_list($path, $exts = '', $list = array())
    {
        $path = dir_path($path);
        $files = glob($path.'*');

        foreach ($files as $v) {
            if (!$exts || pathinfo($v, PATHINFO_EXTENSION) == $exts) {
                $list[] = $v;
                if (is_dir($v)) {
                    $list = dir_list($v, $exts, $list);
                } 
            } 
        }
        return $list;
    }
}

/**
 * Tests for file writability
 *
 * is_writable() returns TRUE on Windows servers when you really can't write to
 * the file, based on the read-only attribute.  is_writable() is also unreliable
 * on Unix servers if safe_mode is on.
 *
 * @access  private
 * @return  void
 */
if (!function_exists('is_really_writable')) {
    function is_really_writable($file)
    {
        if (!defined('FOPEN_WRITE_CREATE')) {
            throw new \InvalidArgumentException("FOPEN_WRITE_CREATE is undefined!");
        }

        if (!defined('DIR_WRITE_MODE')) {
            throw new \InvalidArgumentException("DIR_WRITE_MODE is undefined!");
        }

        // If we're on a Unix server with safe_mode off we call is_writable
        if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE) {
            return is_writable($file);
        }

        // For windows servers and safe_mode "on" installations we'll actually
        // write a file then read it.  Bah...
        if (is_dir($file)) {
            $file = rtrim($file, '/').'/'.md5(mt_rand(1,100).mt_rand(1,100));

            if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE) {
                return FALSE;
            }

            fclose($fp);
            @chmod($file, DIR_WRITE_MODE);
            @unlink($file);
            return TRUE;
        } elseif ( ! is_file($file) OR ($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE) {
            return FALSE;
        }

        fclose($fp);
        return TRUE;
    }
}