<?php

/**
 * Output
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Core;
use Madphp\Core\Support\Format;

class Output
{
    public $data;
    public $args;

    public function __construct($data, $args = array())
    {
        $this->data = $data;
        $this->args = $args;
    }

    /**
     * 输出 json 数据
     */
    public static function json($data, $option = 0, $return = false, $fromType = null)
    {
        if (is_resource($data)) {
            throw new \UnexpectedValueException("Output::json can not recieve resource!");
        } else {
            $output = new self($data);
            if ($return) {
                return Format::factory($output->data, $fromType)->toJson($option);
            }
            echo Format::factory($output->data, $fromType)->toJson($option);
        }
    }
    
    /**
     * 输出 xml 数据
     */
    public static function xml($arr, $structure = null, $basenode = 'xml', $return = false, $fromType = null)
    {
        $output = new self($arr);

        if ($structure !== null && !$structure) {
            $structure = null;
        }
        if (!$basenode) {
            $basenode = 'xml';
        }

        $xml_output = Format::factory($output->data, $fromType)->toXml($structure, $basenode);

        if ($return) {
            return $xml_output;
        }
        header('Content-Type: text/xml');
        echo $xml_output;
    }

    /**
     * 输出 serialize 数据
     */
    public static function serialize($data, $return = false, $fromType = null)
    {
        if (is_resource($data)) {
            throw new \UnexpectedValueException("Output::serialize can not recieve resource!");
        } else {
            $output = new self($data);
            if ($return) {
                return Format::factory($output->data, $fromType)->toSerialize();
            }
            echo Format::factory($output->data, $fromType)->toSerialize();
        }
    }

    /**
     * 输出变量的字符串表示
     */
    public static function php($data, $return = false, $fromType = null)
    {
        $output = new self($data);
        if ($return) {
            return Format::factory($output->data, $fromType)->toPhp();
        }
        echo Format::factory($output->data, $fromType)->toPhp();
    }

    /**
     * 输出数组
     */
    public static function asArray($data, $return = false, $fromType = null)
    {
        $output = new self($data);
        if ($return) {
            return Format::factory($output->data, $fromType)->toArray();
        }
        var_dump(Format::factory($output->data, $fromType)->toArray());
    }

    /**
     * 输出csv
     */
    public static function csv($data, $fileName, $return = false, $fromType = null)
    {
        $output = new self($data);
        if ($return) {
            return Format::factory($output->data, $fromType)->toCsv();
        }
        header("Content-type:application/vnd.ms-excel");
        header("content-Disposition:filename={$fileName}");
        echo Format::factory($output->data, $fromType)->toCsv();
    }
}