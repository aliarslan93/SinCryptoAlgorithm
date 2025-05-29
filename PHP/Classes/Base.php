<?php

namespace Class;

use Binance\API;
use Traits\Filter;
use Traits\Formater;

abstract class Base extends Constant
{
    use Formater, Filter;
    protected $rules;
    protected $currency;
    protected string $path;
    protected $notification;
    public $api;
    protected $livePrice;
    protected $user;
    public function setPath($path)
    {
        $this->path = $path;
    }
    public function initApplication($apiKey, $secretKey, $test = false)
    {
        $this->api = new API($apiKey, $secretKey, false);
        if (!$test) {
            $this->rules = getRules($this->currency);
            $this->notification = new Notification($this->currency);
        }
    }
    public function initUser($user)
    {
        $this->user = $user;
    }
    public function getTimeGroup(string $type = '')
    {
        $result = [];
        for ($i = 0; $i < count(self::TIMES); $i++) {
            if (self::TIMES[$i]['type'] == "$type") {
                $result[$i] = self::TIMES[$i];
            }
        }
        return $result;
    }
    public static function getTimeDetail($params = [], $replateText = '')
    {
        if ($replateText != '') {
            foreach (self::TIMES as $time) {
                if ($time[$params['key']] == str_replace($replateText . "/", '', $params['value'])) {
                    return $time;
                }
            }
        } else {
            foreach (self::TIMES as $time) {
                if ($time[$params['key']] ==  $params['value']) {
                    return $time;
                }
            }
        }
        return [];
    }
    public function saveSortOrderPosition($position)
    {
        $positionPath = SORT_ORDER_PATH;
        if (!file_exists("$positionPath")) {
            mkdir("$positionPath", 0777, true);
        }

        $fullPath =   $positionPath . "/" . $this->currency . ".json";
        if (!empty($position)) {
            file_put_contents($fullPath, json_encode($position));
        }
    }
    public function saveProtective($protectiveData)
    {
        // $this->saveTreshhold();
        $positionPath = PROTECTIVE_PATH;

        if (!file_exists("$positionPath")) {
            mkdir("$positionPath", 0777, true);
        }

        $fullPath =   $positionPath . "/" . $this->currency . ".json";
        if (!empty($protectiveData) & !file_exists($fullPath)) {
            file_put_contents($fullPath, json_encode($protectiveData));
        }
    }
    public function saveOrderPosition($position)
    {
        // $this->saveTreshhold();
        $positionPath = POSITION_PATH;

        if (!file_exists("$positionPath")) {
            mkdir("$positionPath", 0777, true);
        }

        $fullPath =   $positionPath . "/" . $this->currency . ".json";
        if (!empty($position)) {
            file_put_contents($fullPath, json_encode($position));
        }
    }
    public function removeOrderPosition($fileName)
    {
        $deletePath = POSITION_PATH . "/" . $fileName . ".json";
        if (!file_exists("$deletePath")) {
            return false;
        }
        unlink($deletePath);
    }
    public function removeOrder($fileName)
    {
        if ($fileName == '') {
            return false;
        }
        $deletePath = ORDER_PATH . "/" . $fileName;
        if (!file_exists("$deletePath")) {
            return false;
        }

        unlink($deletePath);
    }
    public function removeTreshhold($subPath = '')
    {
        if ($subPath == '') {
            return false;
        }
        $deletePath = TRESHHOLD_PATH . "/" . $subPath . "/" . $this->currency . ".json";
        if (!file_exists("$deletePath")) {
            return false;
        }
        unlink($deletePath);
    }
    public function saveOrder($order)
    {
        $savePath = ORDER_PATH . "/" . $this->user['Token_Name'];
        if (!file_exists("$savePath")) {
            mkdir("$savePath", 0777, true);
        }
        $fullPath = $savePath . "/" . time() . "_" . $this->currency . ".json";

        if (!empty($order)) {
            file_put_contents($fullPath, json_encode($order));
        }
    }
    public function longOrderable($orderPrice)
    {
        $userOrders = getUserOrders($this->user, $this->currency);
        if (empty($userOrders)) {
            return -1;
        }

        $allSameOrder = [];
        foreach ($userOrders as $userOrder) {
            $fileName = str_replace('.json', '', $userOrder);

            $findSeperator = strpos($userOrder, '_') + 1;
            $removedText = substr($userOrder, $findSeperator, strlen($this->currency));
            if ($removedText == $this->currency) {
                $sameOrder = getUserOrder($this->user['Token_Name'], $fileName);
                if ($sameOrder['type'] == 'long') {
                    $allSameOrder[] = $sameOrder['buy'];
                }
            }
        }
        krsort($allSameOrder);
        if (empty($allSameOrder)) {
            return -1;
        }
        if (count($allSameOrder) == MAX_PER_USER_ORDER) {
            return false;
        }
        $filteredOrder = $this->filterListByValue($orderPrice, $allSameOrder, null, 'down');
        if (empty($filteredOrder)) {
            return -1;
        }
        return ($allSameOrder[array_key_first($allSameOrder)]) ? $allSameOrder[array_key_first($allSameOrder)] : false;
    }
    public function sortOrderable($orderPrice)
    {
        $userOrders = getUserOrders($this->user, $this->currency);
        if (empty($userOrders)) {
            return -1;
        }
        $allSameOrder = [];
        foreach ($userOrders as $userOrder) {
            $fileName = str_replace('.json', '', $userOrder);
            $findSeperator = strpos($userOrder, '_') + 1;
            $removedText = substr($userOrder, $findSeperator, strlen($this->currency));
            if ($removedText == $this->currency) {
                $sameOrder = getUserOrder($this->user['Token_Name'], $fileName);
                if ($sameOrder['type'] == 'sort') {
                    $allSameOrder[] = $sameOrder['buy'];
                }
            }
        }

        krsort($allSameOrder);
        if (empty($allSameOrder)) {
            return -1;
        }

        if (count($allSameOrder) == MAX_SORT_USER_ORDER) {
            return false;
        }

        $filteredOrder = $this->filterListByValue($orderPrice, $allSameOrder, null, 'down');
        if (empty($filteredOrder)) {
            return -1;
        }
        return ($allSameOrder[array_key_first($allSameOrder)]) ? $allSameOrder[array_key_first($allSameOrder)] : false;
    }
}
