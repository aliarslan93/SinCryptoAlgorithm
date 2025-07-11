<?php

namespace Class;

class Manipulation extends Base
{

    protected $treshhold;
    protected $time;
    public function __construct($currency, string $timeName)
    {
        $this->time = $timeDetail = self::getTimeDetail(['key' => 'name', 'value' => $timeName]);
        $this->currency = $currency;
        $this->treshhold = getSyncPoint($this->currency, $timeDetail['path']);
    }
    public function validateManipulation()
    {
        $goingUp = $this->treshhold['level_three'];
        $_goingUp = $this->treshhold['level_one'];
        if ($goingUp['close'] > $goingUp['middle'] || $goingUp['close'] > $goingUp['open']) {
            return $goingUp[$goingUp['deepPoint']];
        }
        if ($goingUp['close'] > $goingUp['middle'] || $goingUp['close'] > $goingUp['open']) {
            return $goingUp[$goingUp['peakPoint']];
        }
        if ($_goingUp['close'] > $_goingUp['middle'] || $_goingUp['close'] > $_goingUp['open']) {
            return $_goingUp[$_goingUp['deepPoint']];
        }
        if ($_goingUp['close'] > $_goingUp['middle'] || $_goingUp['close'] > $_goingUp['open']) {
            return $_goingUp[$_goingUp['peakPoint']];
        }
        return [];
    }
    public function upStart($endTime)
    {
        $deepPoint = $this->validateManipulation();
        if (!empty($deepPoint)) {
            $config = getConfig();
            $config['END_OF'] = getTimestamp() + (60 * 60 * $endTime); //Minutes and Seconds
            setConfig($config);
        }
    }
    public function downStart($endTime)
    {
        $peakPoint = $this->validateManipulation();
        if (!empty($peakPoint)) {
            $config = getConfig();
            $config['END_OF'] = getTimestamp() + (60 * 60 * $endTime); //Minutes and Seconds
            setConfig($config);
        }
    }
}
