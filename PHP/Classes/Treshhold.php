<?php

namespace Class;

use Traits\Filter;
use Traits\Formater;

class Treshhold
{
    use Formater, Filter;
    protected $side;
    protected $path;
    protected $currency;
    protected $file = [
        'level_one' => [], //33% Probably
        'level_two' => [],//49% Probably
        'level_three' => [],//33% Probably
    ];
    protected $selectedKey = null;
    protected $list;
    protected $model;
    protected $rules;
    public function __construct($currency, $side, $path)
    {
        $this->currency = $currency;
        $this->rules = getRules($currency);
        $this->path = $path;
        $this->file = getSyncPoint($currency, $this->path);
        $this->side = $side;

        if ($this->side == 'down') {
            $this->selectedKey = 'Choose Key';
            $this->model = new Down();
        }
        if ($this->side == 'up') {
            $this->selectedKey = 'Choose Key';
            $this->model = new Up();
        }
    }
    public function setFile($file)
    {
        $this->file = $file;
    }
    public function getFile()
    {
        return $this->file;
    }
    public function setList($list)
    {
        $this->list = $list;
    }
    /**
     * Fill in the blank
     * @param Array $list 
     * @return Array
     */
    public function decideListPoint($list) {}
    /**
     * Fill in the blank
     * @param Array $list 
     * @return Array
     */
    public function getCheckPoint($points) {}
    /**
     * Fill in the blank
     * @param Array $list 
     * @return Array
     */
    public function checkOrderPoint()
    {
        if (true) {
            return $data = ['orderColums'];
        }
        return [];
    }

    public function pushPoint()
    {
        $list = $this->list;
        $points = $this->model->validateList($list);
        if (empty($points)) {
            return false;
        }

        $points['path'] = $this->path;
        if ($this->side == 'level_one') {
            if (empty($this->file['level_one']) || empty($this->file)) {
                $this->file['level_one'] = $points;
            }
        }
        if ($this->side == 'down' & !empty($this->file['level_one'])) {
            if (empty($this->file['level_two']) & $points['open']['openTime'] > $this->file['level_one']['check_start_time'] & $this->file['level_one']['point'] > $points['point']) {
                $this->file['level_two'] = $points;
            }
            if ($this->file['level_two']['point'] < $points['point'] & $points['open']['openTime'] > $this->file['level_two']['update_time'] & $this->file['level_one']['point'] > $points['point']) {
                $this->file['level_two'] = $points;
            }
        }
    }
    /**
     * Fill in the blank
     * @param Array $points 
     * @return Array
     */
    private function coverPointToList($points)
    {
        $result[$points['close']['openTime']] = $points['close'];
        $result[$points['middle']['openTime']] = $points['middle'];
        $result[$points['open']['openTime']] = $points['open'];
        return $this->orderByColName('openTime', $result, 'DESC');
    }
    /**
     * Fill in the blank
     * @param Array $oldList 
     * @param Array $newList 
     * @return Array
     */
    private function combineList($oldList, $newList)
    {
        if ($newList['middle']['openTime'] == $oldList['close']['openTime']) {
            $oldList['close'] = $newList['middle'];
        }
        if ($newList['open']['openTime'] == $oldList['close']['openTime']) {
            $oldList['close'] = $newList['open'];
        }
        if ($newList['close']['openTime'] == $oldList['close']['openTime']) {
            $oldList['close'] = $newList['close'];
        }
        return $oldList;
    }
    /**
     * Fill in the blank
     */
    public function validateForSortOrder()
    {
        if (empty($this->file)) {
            return [];
        }
        //Exit Rule
        if (!empty($this->file['level_one']) & !empty($this->file['level_two'])) {
            return $this->file;
        }
        return [];
    }
    public function calculateSortOrderTreshhold($referencePoint)
    {
        //Exit Rule
        if (true) {
            return ['buy' => 1, 'sell' => 2, 'stoploss' => 3, 'rate' => 'Price Rate', 'orderTime' => 'Pushed Order Time'];
        }
        return [];
    }
    /**
     * Fill in the blank
     * @return void
     */
    public function updatePoint()
    {
        $treshholdPoint = getSyncPoint($this->currency, $this->path);
        if (empty($treshholdPoint)) {
            return false;
        }
        if (array_key_exists('level_three', $this->file)) {
            $this->selectedKey = 'level_three';
        }
        $updateList = $treshholdPoint[$this->selectedKey];
        $nowList = $this->decideListPoint($this->list);

        $combinedPoints = $this->combineList($updateList, $nowList);
        $combinedList = $this->coverPointToList($combinedPoints);
        /*
        $checkPoints = $this->decideListPoint($this->list);
        $comparedList = $this->combineList($updateList, $checkPoints);
        */
        $combinedList = $this->decideListPoint($combinedList);
        if ($this->selectedKey == 'level_three') {
            $points = $this->model->validateOrderList($combinedList);
        } else {
            $points = $this->model->validateList($combinedList);
        }

        $timeStamp = getTimestamp();

        if (empty($points)) {
            $this->file[$this->selectedKey] = [];
        } else {
            if ($timeStamp > $points['update_time'] & $timeStamp < $points['order_time']) {
                $points['path'] = $this->path;
                $this->file[$this->selectedKey] = $points;
            }
        }
    }
    /**
     * Fill in the blank
     * @return void
     */
    public function checkPoint()
    {
        $syncPoint = getSyncPoint($this->currency, $this->path);
        if (empty($syncPoint)) {
            return false;
        }

        $upPoint = $syncPoint[$this->selectedKey];
        $timeStamp = getTimestamp();

        $points = $this->decideListPoint($this->list);
        $checkPointAvg = $this->getCheckPoint($points);
        if ($timeStamp > $upPoint['check_start_time'] & $checkPointAvg > $upPoint['point'] & $points['middle']['openTime'] != $upPoint['close']['openTime']) {
            $this->deleteSync();
        }
        if (array_key_exists('level_three', $syncPoint)) {
            if (!empty($syncPoint) & (empty($syncPoint['level_three']))) {
                $this->deleteSync();
            }

            if (!empty($syncPoint['level_three']) & $timeStamp > $syncPoint['level_three']['order_time']) {
                $this->file = $syncPoint;
                $this->moveToTreshhold(true);
            }
            if ($timeStamp > $syncPoint['level_three']['expired_treshhold_time']) {
                $this->deleteSync();
            }
        }
    }
    public function moveToTreshhold($renamePath = false)
    {
        $path = $this->path;
        if ($renamePath) {
            $path = str_replace(SYNC_PATH . '/', TRESHHOLD_PATH . "/", $this->path);
        }
        if (!file_exists("$path")) {
            mkdir("$path", 0777, true);
        }
        $fullPath =   $path . "/" . $this->currency . ".json";
        if (!empty($this->file)) {
            file_put_contents($fullPath, json_encode($this->file));
        }

        $this->deleteSync();
    }
    public function deleteSync($treshhold = false)
    {
        //   $this->file[$this->side] = [];
        $deletePath = $this->path . "/" . $this->currency . ".json";
        if ($treshhold) {
            $deletePath = str_replace('/', "/" . TRESHHOLD_PATH . "/", $this->path) . "/" . $this->currency . ".json";
        }
        if (!file_exists("$deletePath")) {
            return false;
        }
        unlink($deletePath);
    }
    public function save()
    {
        // $this->saveTreshhold();
        if (!file_exists("$this->path")) {
            mkdir("$this->path", 0777, true);
        }

        $fullPath =   $this->path . "/" . $this->currency . ".json";
        if (!empty($this->file)) {
            file_put_contents($fullPath, json_encode($this->file));
        }
    }
}
