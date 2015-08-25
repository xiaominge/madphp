<?php

use \Madphp\Src\Core\View;

/**
 * 函数库
 * @author 徐亚坤 hdyakun@sina.com
 */

function render($tpl, $data = array())
{
    echo View::fetch($tpl, $data);
}