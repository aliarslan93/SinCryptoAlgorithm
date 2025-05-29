<?php

namespace Traits;

trait Formater
{
    public function clearArrayKey($array, $key)
    {
        $result = [];
        foreach ($array as $arrKey => $rows) {
            $newRow = [];
            foreach ($rows as $rowKey => $val) {
                if ($rowKey != $key) {
                    $newRow[$rowKey] = $val;
                }
            }
            $result[$arrKey] = $newRow;
        }
        return $result;
    }
    public function priceFormat($price, $side = 1)
    {
        $pointOrder = strpos("$price", ".");
        $intSide = (int) $price;
        $priceKusur = substr($price, $pointOrder + $side, $this->rules['price_length']);
        $p = "$intSide.$priceKusur";
        if ($price < 0) {
            $p = -1 * $p;
        }
        return floatval($p);
    }
    public function quantityFormat($quantity)
    {
        $pointOrder = strpos("$quantity", ".");
        $intSide = (int) $quantity;
        $pointSide = substr($quantity, $pointOrder + 1, $this->rules['quantity_length']);
        $p = "$intSide.$pointSide";
        return floatval($p);
    }
    public function calculateBuyQuantity($price, $balance)
    {
        if ($this->rules['quantity_int']) {
            return intval($balance / $price);
        } else {
            return $this->quantityFormat($balance / $price);
        }
    }
    public function calculateBuyBalance(float $buyPrice, float $quantity)
    {
        return $this->priceFormat($buyPrice * $quantity);
    }
    public function calculateMinSellPrice(float $buyBalance, float $sellQuantity, float $rate = 600)
    {
        return  $this->priceFormat((($buyBalance + $this->rules['rate'] * $rate) / $sellQuantity));
    }
    public function calculateSellCommission(float $buyQuantity)
    {
        if (!$this->rules['quantity_int']) {
            return $this->quantityFormat(round($buyQuantity - $this->calculateBuyCommission($buyQuantity), 2));
        } else {
            return $this->calculateBuyCommission($buyQuantity);
        }
    }
    public function getDepthPoint($depth)
    {
        $upPrice = $this->priceFormat(array_key_first($depth['asks']));
        $downPrice = $this->priceFormat(array_key_first($depth['bids']));
        return ['up' => $upPrice, 'down' => $downPrice];
    }
    private function calculateBuyCommission(float $buyQuantity)
    {

        if ($this->rules['quantity_int']) {
            return 1;
        } else {
            $lenght = $this->rules['quantity_length'] - 1;
            $string = '';
            for ($i = 0; $i < $lenght; $i++) {
                $string .= 0;
            }
            $format = "0.$string" . "1";
            return $buyQuantity - $format;
            //return $format;
        }
    }
}
