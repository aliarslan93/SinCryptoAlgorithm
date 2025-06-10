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
        if (empty($list) || count($list) < 2) {
            return [];
        }
        $config = $this->initConfig();
        $openRow = $list['open'];
        $closeRow = $list['close'];
        $middleRow = $list['middle'];
        $colors = $this->getColors($list);
        $list['checkColumn'] = 'order_point';

        if ($config['LEVEL'] == 'strong') {
            if ($openRow['asset'] > $openRow['close'] || $closeRow['asset'] > $closeRow['close'] || $middleRow['asset'] > $middleRow['close']) {
                if ($colors) {
                    return $list;
                }
            }
        }
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
