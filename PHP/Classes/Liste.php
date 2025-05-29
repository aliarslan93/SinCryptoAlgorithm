<?php

namespace Class;

use Class\Point;

class Liste extends Base
{
    public $list;
    public function __construct($currency)
    {
        $this->currency = trim($currency);
        $this->rules =  getRules($this->currency);
    }
    public function setList($list)
    {
        $this->list = $list;
    }

    public function getList()
    {
        return $this->list;
    }
    public function coverListItems()
    {
        $list = $this->list;

        unset($this->list);

        foreach ($list as $row) {
            $point = new Point($this->currency, $row);

            $this->list[$point->field['openTime']] = $point->export();
        }
        $list = $this->linkedList($this->list);
        $list = $this->clearArrayKey($list, 'selected');
        $list = $this->orderByColName('openTime', $list, 'DESC');

        $list = $this->takeAverage($list);

        $list = $this->clearArrayKey($list, 'selected');

        $list = $this->orderByColName('openTime', $list, 'DESC');
        unset($list[array_key_last($list)]);
        $this->list = $list;

        return $this;
    }
    public function linkedList($list, $selectedPoint = null)
    {
        if (is_null($selectedPoint)) {
            $selectedPoint = $list[array_key_first($list)];
            if (array_key_exists('selected', $selectedPoint)) {
                if ($selectedPoint['selected'] == true) {
                    return $list;
                }
            } else {
                $selectedPoint['selected'] = true;
                unset($list[array_key_first($list)]);
            }

            foreach ($list as $price) {
                $listPrice = new Point($this->currency, $price);

                $selectedPoint = $this->calculatePriceTaker($selectedPoint, $listPrice->export());

                break;
            }
        }

        $list[$selectedPoint['openTime']] = $selectedPoint;
        return $this->linkedList($list);
    }
    /**
     * 
     * @param Array $next  
     * @param Array $before  
     * @return Array $next Linked List Like X
     */
    private function calculatePriceTaker($next, $before)
    {

        $next['taker'] = $next['takerBuyVolume'] - $before['takerBuyVolume'];
        return $next;
    }
    public function takeAverage($list, $selectedPoint = null)
    {
        if (is_null($selectedPoint)) {
            $selectedPoint = $list[array_key_first($list)];
            if (array_key_exists('selected', $selectedPoint)) {
                if ($selectedPoint['selected'] == true) {
                    return $list;
                }
            } else {
                $selectedPoint['selected'] = true;
                unset($list[array_key_first($list)]);
            }

            foreach ($list as $price) {
                $listPrice = new Point($this->currency, $price);
                $selectedPoint = $this->calculatePriceAverage($selectedPoint, $listPrice->export());
                break;
            }
        }
        $list[$selectedPoint['openTime']] = $selectedPoint;
        return $this->takeAverage($list);
    }
    private function calculatePriceAverage($last, $before)
    {
        $last['diff'] = $last['volume'] - $before['volume'];
        $last['taker'] = $last['takerBuyVolume'] - $before['takerBuyVolume'];
        $last['assetDiff'] = $last['asset'] - $before['asset'];
        $last['before_color'] = $before['color'];
        return $last;
    }
}
