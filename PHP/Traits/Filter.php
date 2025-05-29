<?php

namespace Traits;

trait Filter
{
    public function getColData(string $col, array $list, $withOut = false)
    {
        $result = [];
        foreach ($list as $time => $row) {
            if ($withOut) {
                $result[] =  $row["$col"];
            } else {
                $result[$time] = $row["$col"];
            }
        }
        return $result;
    }
    public function getKeyData($list, $withOut = '')
    {
        $result = [];
        foreach ($list as $k => $row) {
            if ($withOut == '') {
                $result[] = $k;
            } else {
                if ($k != $withOut) {
                    $result[] = $k;
                }
            }
        }
        return $result;
    }
    public function deleteArrayDiff($deleteArray, $fullList)
    {

        foreach ($deleteArray as $deleteKey => $value) {
            unset($fullList[$deleteKey]);
        }
        return $fullList;
    }
    public function deleteArrayDiffWithValue($deleteArray, $compareKey, $fullList)
    {

        foreach ($deleteArray as $deleteKey => $deleteRow) {
            foreach ($fullList as $fullKey => $fullValue) {
                if ($deleteKey == $fullKey) {
                    if ($deleteRow[$compareKey] == $fullValue) {
                        $deleteArray[$deleteKey];
                    }
                }
            }
        }
        return $deleteArray;
    }
    /**
     * @param array $list
     * @return array $formated
     */
    public function depthListFormat($list, $selectedPrice = null, $type = 'up')
    {
        $result = [];
        foreach ($list as $price => $row) {
            if (!is_null($selectedPrice)) {
                $selectedPrice = $this->priceFormat($selectedPrice);
                $price = $this->priceFormat($row['price']);
                if ($type == 'down') {
                    if ($selectedPrice >= $price) {
                        $result[] = [
                            'price' =>  $this->priceFormat($price),
                            'volume' =>  $row['volume']
                        ];
                    }
                }
                if ($type == 'up') {
                    if ($selectedPrice <= $price) {
                        $result[] = [
                            'price' =>  $this->priceFormat($price),
                            'volume' =>  $row['volume']
                        ];
                    }
                }
            }
        }
        return $result;
    }
    public function filterPriceInDepth($orderCols, array $depthList)
    {
        $depthList = $this->coverDepth($depthList);

        $result['bids'] = $this->depthListFormat($depthList['bids'], $orderCols['low'], 'up');
        $result['asks'] = $this->depthListFormat($depthList['asks'], $orderCols['high'], 'down');

        return $result;
    }
    public function getDepthPrice($depth, $formated = false)
    {
        if ($formated) {
            $depth = $this->coverDepth($depth);
        }
        $bids = $depth['bids'];
        $asks = $depth['asks'];

        if (!empty($bids) || !empty($asks)) {
            $depth['bids'] = $this->getListAverage($bids, "volume", 'down');
            $depth['asks'] = $this->getListAverage($asks, "volume", 'down');
            $depthPrices['buy'] = $this->priceFormat($this->averageValue("price", $bids));
            $depthPrices['sell'] = $this->priceFormat($this->averageValue('price', $asks, 'down'));
        }
        if (count($depthPrices) > 1) {
            $depthPrices['buy'] = min($depthPrices);
            $depthPrices['sell'] = max($depthPrices);
        }

        return ['price' => $depthPrices, 'volume' => $this->getDepthVolumeDiff($depth)];
        //$asks = $this->depthListFormat($depthList['asks'], $upPrice, 'down');
    }
    public function coverDepth($depth)
    {
        $result = [];
        foreach ($depth as $side => $prices) {
            foreach ($prices as $price => $volume) {
                $result[$side][] = [
                    'price' => $this->priceFormat($price),
                    'volume' => $volume
                ];
            }
        }
        return $result;
    }
    public function getDepthVolumeDiff($depthList)
    {
        $bidsVolume = $this->sumArrayCol($depthList['bids'], 'volume');
        $asksVolume = $this->sumArrayCol($depthList['asks'], 'volume');
        $result = $asksVolume - $bidsVolume;
        return $result;
    }
    /**
     * @param string $col
     * @param array $priceList
     * @param string $orderType
     */
    public function orderByColName(string $col, array $priceList, $orderType = 'DESC')
    {
        $colData = $this->getColData($col, $priceList);
        switch ($orderType) {
            case 'DESC':
                arsort($colData);
                break;
            case 'ASC':
                asort($colData);
                break;
        }

        return $this->combineData($colData, $priceList);
    }
    /**
     * @param array $filteredData search key in $allData
     * @return array $result $filteredData's key in $allData
     * 
     */
    public function combineData(array $filteredData, array $allData, $limit = false)
    {
        $result = [];
        foreach ($filteredData as $time => $filteredRow) {
            if (array_key_exists($time, $allData)) {
                $result[$time] = $allData[$time];
            }
        }

        return $result;
    }
    public function getListAverage($list = [], $col = 'volume', $param = 'up')
    {

        $average = $this->averageValue($col, $list);
        return $this->filterListByValue($average, $list, $col, $param);
    }

    public function sumArrayCol(array $array, string $col)
    {
        $total = 0;
        foreach ($array as $arr) {
            if ($arr[$col] < 0) {
                $total -= $arr[$col];
            } else {
                $total += $arr[$col];
            }
        }
        return $total;
    }
    public function averageValue(string $col = '', array $rows)
    {
        $sum = 0;
        foreach ($rows as $row) {
            if ($col != '') {
                if (array_key_exists($col, $row)) {
                    $sum += $row[$col];
                }
            } else {
                $sum += $row;
            }
        }
        return $sum / count($rows);
    }
    public function filterListByValue($value, array $rows, $col = null, string $type = 'up')
    {
        $result = [];
        foreach ($rows as $key => $row) {
            switch ($type) {
                case 'up':
                    if (is_null($col)) {
                        if ($row > $value) {
                            $result[$key] = $row;
                        }
                    } else {
                        if ($row[$col] > $value) {
                            $result[$key] = $row;
                        }
                    }

                    break;
                case 'down':
                    if (is_null($col)) {
                        if ($row <= $value) {
                            $result[$key] = $row;
                        }
                    } else {
                        if ($row[$col] <= $value) {
                            $result[$key] = $row;
                        }
                    }
                    break;
            }
        }
        return $result;
    }
}
