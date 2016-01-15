<?php

namespace Madphp\Db;

abstract class Factory
{

    abstract public function createDb();

    public function create()
    {
        return call_user_func_array(array($this, 'createDb'), func_get_args());
    }
}