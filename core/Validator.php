<?php

/**
 * Validator
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Src\Core;

class Validator
{
    /**
     * 检测邮箱
     * @param $email
     * @return bool
     */
    public static function email($email)
    {
        $result = filter_var($email, FILTER_VALIDATE_EMAIL);
        return empty($result) ? false : true;
    }

    /**
     * 检测url
     * @param $url
     * @return bool
     */
    public static function url($url)
    {
        $result = filter_var($url, FILTER_VALIDATE_URL);
        return empty($result) ? false : true;
    }

    /**
    * 检测是否IP地址
    * @access   public
    * @param    string
    * @param    string  ipv4 or ipv6
    * @return   bool
    */
    public static function ip($ip, $which = '')
    {
        $which = strtolower($which);

        // filter_var 函数是否存在
        if (is_callable('filter_var')) {
            switch ($which) {
                case 'ipv4':
                    $flag = FILTER_FLAG_IPV4;
                    break;
                case 'ipv6':
                    $flag = FILTER_FLAG_IPV6;
                    break;
                default:
                    $flag = '';
                    break;
            }

            return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flag);
        }

        if ($which !== 'ipv6' && $which !== 'ipv4') {
            if (strpos($ip, ':') !== FALSE) {
                $which = 'ipv6';
            } elseif (strpos($ip, '.') !== FALSE) {
                $which = 'ipv4';
            } else {
                return FALSE;
            }
        }

        $func = $which;
        return self::$func($ip);
    }
    
    /**
    * 检测是否IPv4地址
    * @access   public
    * @param    string
    * @return   bool
    */
    public static function ipv4($ip)
    {
        $ip_segments = explode('.', $ip);
        // IP 数字数量校验
        if (count($ip_segments) !== 4) {
            return FALSE;
        }
        // IP 第一个数字不为0
        if ($ip_segments[0][0] == '0') {
            return FALSE;
        }

        // 检测每个数字
        foreach ($ip_segments as $segment) {
            // IP 必须是数字且不能多于3个数字不能大于255
            if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
    * 检测是否 IPv6 地址
    * @access   public
    * @param    string
    * @return   bool
    */
    public static function ipv6($str)
    {
        $groups = 8;
        $collapsed = FALSE;

        $chunks = array_filter(preg_split('/(:{1,2})/', $str, NULL, PREG_SPLIT_DELIM_CAPTURE));

        if (current($chunks) == ':' OR end($chunks) == ':') {
            return FALSE;
        }

        if (strpos(end($chunks), '.') !== FALSE) {
            $ipv4 = array_pop($chunks);

            if (!self::ipv4($ipv4)) {
                return FALSE;
            }

            $groups--;
        }

        while ($seg = array_pop($chunks)) {
            if ($seg[0] == ':') {
                if (--$groups == 0) {
                    return FALSE;
                }

                if (strlen($seg) > 2) {
                    return FALSE;
                }

                if ($seg == '::') {
                    if ($collapsed) {
                        return FALSE;
                    }
                    $collapsed = TRUE;
                }
            } elseif (preg_match("/[^0-9a-f]/i", $seg) OR strlen($seg) > 4) {
                return FALSE;
            }
        }

        return $collapsed OR $groups == 1;
    }

    /**
     * 是否为电话号码
     * @param $phone
     * @return bool
     */
    public static function phone($phone)
    {
        return preg_match('/^(\+86)?1[3-8]\d{9}$/', $phone) > 0 ? true : false;
    }

    /**
     * 是否邮编
     * @param $code
     * @return bool
     */
    public static function postcode($code)
    {
        return preg_match('/^\d{6}$/', $code) > 0 ? true : false;
    }

    /**
     * 检测UTF-8汉字
     * @param $str
     * @return bool
     */
    public static function chinese($str)
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str) > 0 ? true : false;
    }

    /**
     * 检测包含汉字
     * @param $str
     * @return bool
     */
    public static function containChinese($str)
    {
        return preg_match('/[\x80-\xff]./', $str) ? true : false;
    }

    /**
     * 检测json
     * @param $str
     * @return bool
     */
    public static function json($str)
    {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
