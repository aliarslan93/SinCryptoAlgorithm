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

        if (empty($list) || count($list) < 2) {
            return [];
        }

        $config = $this->initConfig();
        $openRow = $list['open'];
        $closeRow = $list['close'];
        $middleRow = $list['middle'];
        $list['checkColumn'] = 'point';
        if ($config['LEVEL' == 'strong'])
            if ($openRow['color'] == 'line_color' & $middleRow['color'] == 'line_color' & $closeRow['color'] == 'line_color') {
                return $list;
            }
        return [];
    }
    /**
     * Fill in the blank
     * @param Array $list 
     * @return Array
     */
    public function validateReturnList($list)
    {

        if (empty($list) || count($list) < 2) {
            return [];
        }

        $openRow = $list['open'];
        $closeRow = $list['close'];
        $middleRow = $list['middle'];

        $list['checkColumn'] = 'point';

        /*
        if ($openRow['color'] == 'green' & $middleRow['color'] == 'red' & $closeRow['color'] == 'green' & $list['deepPoint'] == 'close') {
            return $list;
        }
       */
        //Strong
        if ($openRow['color'] == 'red' & $middleRow['color'] == 'green' & $closeRow['color'] == 'green' & $openRow['close'] > $openRow['average']) {
            return $list;
        }

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
