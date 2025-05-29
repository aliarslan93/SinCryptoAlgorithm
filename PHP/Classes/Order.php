<?php

namespace Class;

use Binance;

class Order extends Base
{
    protected $points;
    public function __construct($currency, $user, $points)
    {
        $this->currency = $currency;
        $this->user = $user;
        $this->points = $points;
        $this->rules = getRules($this->currency);
        $this->initApplication($user['API_KEY'], $user['SECRET_KEY']);
    }
    public function pushOrder($side = 'BUY')
    {
        $orderSide = strtoupper($side);
        $buyBalance = $this->user['Balance'];
        $saveBalanceDiff = $buyBalance + 1;
        $buy = $this->priceFormat($this->points['buy']);
        $buyQuantity = $this->calculateBuyQuantity($buy, $buyBalance);
        $buyBalance = $this->calculateBuyBalance($buy, $buyQuantity);
        $sellCommission = $this->calculateSellCommission($buyQuantity);
        $sellQuantity = $buyQuantity - $sellCommission;
        $sellPrice = $this->points['sell']; //$this->points['min_sell']
        $sellBalance = $this->priceFormat($sellQuantity * $sellPrice);
        $this->points['stoploss'] =  $this->points['buy'] - $this->priceFormat(($this->points['sell'] - $this->points['buy']) * 3);
        if ($sellBalance > $saveBalanceDiff) {

            if ($buy > $this->points['stoploss'] & $this->points['sell'] > $this->points['buy'] & $this->points['sell'] > $this->points['stoploss']) {
                $localOrder = [
                    'side' => $orderSide,
                    'currency' => $this->currency,
                    'path' => $this->user['Token_Name'],
                    'buy' => $this->points['buy'],
                    'sell' => $this->points['sell'],
                    'stoploss' => $this->points['stoploss'],
                    'buyQuantity' => $buyQuantity,
                    'sellQuantity' => $sellQuantity,
                    'profit' => $this->priceFormat($sellBalance - $buyBalance),
                    'status' => 'FILLED',
                    'type' => 'sort',
                    'date' => date('Y-m-d H:i:s', strtotime("+" . TIME_DIFF_VAL . "hours"))
                ];
                $binanceOrder =  $this->api->buy($this->currency, $buyQuantity, $buy);
                $orderOutput = array_merge($localOrder, ['orderId' => $binanceOrder['orderId']]);
                if ($this->notification instanceof Notification) {
                    $this->notification->setToken(Constant::CHAT['signal_channel']['tokenId']);
                    $this->notification->setChat(Constant::CHAT['signal_channel']['chatId']);
                    $this->notification->pushOrder($orderOutput);
                }
                $this->saveOrder($orderOutput);
                downUserOrderLimit($this->user);
            }
        }
    }
}
