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
        $route = 'complex';
        $openRow = $list['open'];
        $closeRow = $list['close'];
        $middleRow = $list['middle'];
        if ($openRow['route_line'] < $closeRow['route_line'] & $openRow['route_line'] < $middleRow['route_line'] & $middleRow['route_line'] < $closeRow['route_line']) {
            $route = 'up';
        }
        if ($openRow['route_line'] > $closeRow['route_line'] & $openRow['route_line'] > $middleRow['route_line']) {
            if ($openRow['average'] > $closeRow['average'] & $middleRow['asset'] > $closeRow['asset']) {
                $route = 'down';
            }
        }
        return $route;
    }
    public function initConfig()
    {
        return  getConfig();
    }
}
