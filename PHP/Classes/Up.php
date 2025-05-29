<?php

namespace Class;

use Interface\TreshholdInterface;

class Up implements TreshholdInterface
{

    public function validateList($list)
    {

        return [];
    }
    public function validateReturnList($list)
    {

        return [];
    }
    public function decideLineRoute($list)
    {
        $route = 'string';
        return $route;
    }
    public function initConfig()
    {
        return  getConfig();
    }
}
