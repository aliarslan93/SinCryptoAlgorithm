<?php

namespace Class;

use Interface\TreshholdInterface;
use Traits\Filter;

class Down implements TreshholdInterface
{
    use Filter;
    /**
     * Fill in the blank
     * @param Array $list 
     * @return Array
     */
    public function validateToTreshholdOrder($list) {}
    /**
     * Fill in the blank
     * @param Array $list 
     * @return Array
     */
    public function validateOrderList($list)
    {
        return [];
    }
     /**
     * Fill in the blank
     * @param Array $list 
     * @return Array
     */
    public function validateList($list, $passBigTreshhold = false)
    {
        return [];
    }
    public function decideLineRoute($list)
    {
        $route = 'string';
        return $route;
    }
    /**
     * Fill in the blank
     * @param Array $list 
     * @return boolean
     */
    public function getColors($list)
    {
        return false;
    }

    public function initConfig()
    {
        return  getConfig();
    }
}
