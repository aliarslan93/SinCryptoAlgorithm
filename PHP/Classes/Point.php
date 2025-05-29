<?php

namespace Class;

use Traits\Formater;

class Point
{
    use Formater;
    protected $rules;
    public $field = [];
    protected $defaultCols = [
        'open',
        'high',
        'low',
        'close',
        'up',
        'down',
        'asset',
        'volume',
        'openTime',
        'closeTime',
        'expiredTime',
        'startTime',
        'assetVolume',
        'average',
        'baseVolume',
        'trades',
        'assetBuyVolume',
        'takerBuyVolume',
        'color',
        'type',
        'savedRate'
    ];
    public function __construct($currency, $data)
    {
        $this->rules = getRules($currency);
        return $this->initFields($data);
    }
    public function initFields($data)
    {
        $this->field['open'] = $this->priceFormat($data['open']);
        $this->field['high'] = $this->priceFormat($data['high']);
        $this->field['low'] = $this->priceFormat($data['low']);
        $this->field['close'] = $this->priceFormat($data['close']);
        $this->field['volume'] = $this->priceFormat($data['volume']);

        $this->field['up'] = $this->priceFormat(($this->field['high']  +  $this->field['close']) / 2);
        $this->field['down'] = $this->priceFormat(($this->field['low'] +  $this->field['open']) / 2);
        $this->field['route_line'] = $this->priceFormat(($this->field['close'] +  $this->field['open']) / 2);
        $this->field['order_point'] = $this->priceFormat(($this->field['low'] +  $this->field['close']) / 2);
        $this->field['trades'] = $data['trades'];
        $this->field['assetBuyVolume'] = $data['assetBuyVolume'];
        $this->field['takerBuyVolume'] = $data['takerBuyVolume'];
        $this->field['baseVolume'] = $data['baseVolume'];
        $this->field['tempRange'] = $this->priceFormat($this->field['close'] - $this->field['open']);
        $this->field['average'] = $this->priceFormat(($this->field['high']  +  $this->field['low']) / 2);
        return $this;
    }
    /**
     * 0 default
     * 1 buy
     * 2 sell
     */
    private function decideType()
    {
        $type = 0;
        if ($this->field['close'] >= $this->field['open']) {
            $type = 1;
        }

        return $type;
    }
    public function export()
    {

        $result = [];
        foreach ($this->field as $field => $value) {
            $result[$field] = $value;
        }

        return $result;
    }
}
