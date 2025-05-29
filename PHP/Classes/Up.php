<?php

namespace Class;

use Interface\TreshholdInterface;

class Up implements TreshholdInterface
{

    /**
     * Fill in the blank
     * @param Array $list 
     * @return Array
     */
    public function validateList($list)
    {

        return [];
    }
    /**
     * Fill in the blank
     * @param Array $list 
     * @return Array
     */
    public function validateReturnList($list)
    {

        return [];
    }
    /**
     * Fill in the blank
     * @param Array $list 
     * @return String
     */
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
