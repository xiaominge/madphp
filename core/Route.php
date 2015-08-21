<?php

/**
 * Route
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Src\Core;

/**
 * @method static Route get(string $route, Callable $callback)
 * @method static Route post(string $route, Callable $callback)
 * @method static Route put(string $route, Callable $callback)
 * @method static Route delete(string $route, Callable $callback)
 * @method static Route options(string $route, Callable $callback)
 * @method static Route head(string $route, Callable $callback)
 */
class Route
{

    public static $halts = false;

    public static $routes = array();

    public static $methods = array();

    public static $callbacks = array();

    public static $patterns = array(
        ':any' => '[^/]+',
        ':num' => '[0-9]+',
        ':all' => '.*'
    );

    public static $errorCallback;

    public static function __callstatic($method, $params)
    {
        $uri = ltrim($params[0], '/');
        $callback = $params[1];
        
        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
    }

    public static function error($callback)
    {
        self::$errorCallback = $callback;
    }
    
    public static function haltOnMatch($flag = true)
    {
        self::$halts = $flag;
    }

    public static function dispatch()
    {
        $foundRoute = false;
        // $method = $_SERVER['REQUEST_METHOD'];
        $method = Request::getMethod();
        $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathinfo = pathinfo($_SERVER['SCRIPT_NAME']);
        $fileDir = $pathinfo['dirname'];
        $fileName = $pathinfo['basename'];
        
        $uri = ltrim(str_replace(array($fileName, $fileDir), array('', ''), $urlPath), '/');

        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        // 存在此路由
        if (in_array($uri, self::$routes)) {
            // 路由的键
            $routePos = array_keys(self::$routes, $uri);
            foreach ($routePos as $pos) {
                // 请求方法相同
                if (self::$methods[$pos] == $method) {
                    $foundRoute = true;
                    $ret = self::_dispatch($pos);
                    if($ret === false) return;
                }
            }
        } else {
            $pos = 0;
            foreach (self::$routes as $route) {
                // 包含 : 确定是模糊路由
                if (strpos($route, ':') !== false) {
                    // 替换模糊路由为正则表达式的模式
                    $route = str_replace($searches, $replaces, $route);
                    // 路由匹配成功
                    if (preg_match('#^' . $route . '$#', $uri, $matched)) {
                        // 请求方法相同
                        if (self::$methods[$pos] == $method) {
                            $foundRoute = true;
                            array_shift($matched);
                            $ret = self::_dispatch($pos, $matched);
                            if($ret === false) return;
                        }
                    }
                }
                $pos++;
            }
        }

        // 路由未找到
        if ($foundRoute == false) {
            self::_error404();
        }
    }

    private static function _error404()
    {
        if (!self::$errorCallback) {
            self::$errorCallback = function() {
                header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
                echo '404';
            };
        }
        call_user_func(self::$errorCallback);
    }

    private static function _dispatch($pos, $matched = null)
    {
        // 回调函数不是对象
        if (!is_object(self::$callbacks[$pos])) {
            // 用 / 分隔符分隔回调函数为数组
            $parts = explode('/', self::$callbacks[$pos]);
            // 数组最后一个元素是控制器和方法
            $last = end($parts);
            // 获取控制器和方法
            list($controller, $method) = explode('@', $last);
            // 实例化控制器
            $controller = new $controller();
            $callback = array($controller, $method);
        } else {
            $callback = self::$callbacks[$pos];
        }
        // 执行回调函数
        if (is_array($matched) && $matched) {
            call_user_func_array($callback, $matched);
        } else {
            call_user_func($callback);
        }
        // 是否停止执行此路由的其他回调函数
        if (self::$halts) return false;
        return true;
    }
}
