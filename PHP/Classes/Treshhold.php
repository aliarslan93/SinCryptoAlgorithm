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
    protected $file = [];
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

    public function pushPoint() {}
    /**
     * Fill in the blank
     * @param Array $points 
     * @return Array
     */
    private function coverPointToList($points) {}
    /**
     * Fill in the blank
     * @param Array $oldList 
     * @param Array $newList 
     * @return Array
     */
    private function combineList($oldList, $newList) {}
    /**
     * Fill in the blank
     */
    public function validateForSortOrder()
    {
        if (empty($this->file)) {
            return [];
        }
        //Exit Rule
        if (true) {
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
    public function updatePoint() {}
    /**
     * Fill in the blank
     * @return void
     */
    public function checkPoint() {}
    public function moveToTreshhold($renamePath = false) {}
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
