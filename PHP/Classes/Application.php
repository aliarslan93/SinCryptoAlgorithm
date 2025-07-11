<?php

namespace Class;


class Application extends Base
{
    protected $oldTreshhold;
    protected $config;
    public function __construct(string $currency = '')
    {
        $this->currency = $currency;
        $this->config = getConfig();
    }
    public function calculateBuySell()
    {

        $chat  = Constant::CHAT['signal_channel'];
        $url = "https://api.telegram.org/" . $chat['tokenId'] . "/getUpdates";
        //https://api.telegram.org/bot123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11/getMe

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
            CURLOPT_ENCODING       => "",     // handle compressed
            CURLOPT_USERAGENT      => "test", // name of client
            CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
            CURLOPT_TIMEOUT        => 120,
        ));
        $content  = curl_exec($curl);
        curl_close($curl);
        dd($content);
    }
    public function startManipulation($sideOf = 'up')
    {
        $manipulation = new Manipulation($this->currency, '4h');
        if (getTimestamp() < $this->config['END_OF']) {
            $this->livePrice = $this->priceFormat($this->api->price($this->currency));
            if ($sideOf == 'up') {
                $manipulation->upStart(4,$this->livePrice);
            } else {
                $manipulation->downStart(2,$this->livePrice);
            }
        } else {
        }
    }
    public function startSync(string $side)
    {
        $times = $this->getTimeGroup($this->config['APP_TYPE']);
        foreach ($times as $time) {
            $limit = $time['limit'];
            $name = $time['name'];
            $path = $time['path'];
            $path = $this->path . "/$path";
            $treshhold = new Treshhold($this->currency, $side, $path);

            $priceList = $this->api->candlesticks($this->currency, "$name", $limit);

            // $priceList = getExampleFile($this->currency);

            $liste = new Liste($this->currency);

            $liste->setList($priceList);

            $liste = $liste->coverListItems();

            $list = $liste->getList();
            $treshhold = new Treshhold($this->currency, $side, $path);
            $treshhold->setList($list);

            $treshhold->pushPoint();
            $treshhold->save();
        }
    }
    public function updateSync(string $side)
    {
        $times = $this->getTimeGroup($this->config['APP_TYPE']);
        foreach ($times as $time) {
            $limit = $time['limit'];
            $name = $time['name'];
            $path = $time['path'];

            $priceList = $this->api->candlesticks($this->currency, "$name", $limit);
            //$priceList = getExampleFile($this->currency);

            $list = new Liste($this->currency);
            $list->setList($priceList);

            $list = $list->coverListItems();
            $path = $this->path . "/$path";
            $treshhold = new Treshhold($this->currency, $side, $path);
            $treshhold->setList($list->list);
            $treshhold->updatePoint();
            $treshhold->save();
        }
    }
    public function checkSync(string $side)
    {
        $times = $this->getTimeGroup($this->config['APP_TYPE']);
        foreach ($times as $time) {
            $limit = $time['limit'];
            $name = $time['name'];
            $path = $time['path'];
            $path = $this->path . "/$path";
            $priceList = $this->api->candlesticks($this->currency, "$name", $limit);
            //$priceList = getExampleFile($this->currency);
            $list = new Liste($this->currency);
            $list->setList($priceList);
            //            $list = $list->coverListItems();
            $treshhold = new Treshhold($this->currency, $side, $path);
            $treshhold->setList($list->list);
            $treshhold->checkPoint();
        }
    }
    public function checkUserOrder($orderPath)
    {

        $this->livePrice = $this->priceFormat($this->api->price($this->currency));
        $orderDetail = getUserOrder($this->user['Token_Name'], str_replace('.json', '', $orderPath));
        $userOrderPath = $this->user['Token_Name'] . "/" . $orderPath;
        $balance = getBalance(['Token_Name' => $this->user['Token_Name']]);
        $Profit = $balance['Profit'];
        $syncPoint = getSyncPoint($this->currency, SYNC_PATH . '/pob');
        if ($this->livePrice > $orderDetail['sell']) {
            $orderDetail['sell'] = $this->livePrice;
            $sellBalance = $this->priceFormat($orderDetail['sellQuantity'] * $orderDetail['sell']);
            $profit = floatval($sellBalance - $this->user['Balance']);
            // $binanceOrder =  $this->api->sell($this->currency,$orderDetail['sellQuantity'],$this->livePrice);
            $Profit = $Profit + $profit;
            $this->removeOrder($userOrderPath);
            if ($this->notification instanceof Notification) {
                if ($this->notification instanceof Notification) {
                    $this->notification->setToken(Constant::CHAT['signal_channel']['tokenId']);
                    $this->notification->setChat(Constant::CHAT['signal_channel']['chatId']);
                    $this->notification->sendTelegramMessage($this->currency . " Sell:" . $orderDetail['sell'] . " Buy:" . $orderDetail['buy'] . " Profit:" . $profit . " Message: Sussess Sell");
                    //$this->notification->pushOrder($orderOutput);
                }
            }
            $balanceProperty = [
                'UserKey' => $this->user['UserKey'],
                'Token_Name' => $this->user['Token_Name'],
                'Profit' => $Profit,
                'Last_Order' => json_encode($orderDetail),
            ];
            saveBalanceJson($balanceProperty);
        }
        if ($this->livePrice < $orderDetail['stoploss']) {
            // $binanceOrder =  $this->api->sell($this->currency,$orderDetail['sellQuantity'],$this->livePrice);
            $this->removeOrder($userOrderPath);
            $stoplossBalance = $this->priceFormat($this->livePrice * $orderDetail['sellQuantity']);
            $lost = floatval($this->user['Balance'] - $stoplossBalance);
            $Profit = $Profit - $lost;

            if ($this->notification instanceof Notification) {
                $this->notification->setToken(Constant::CHAT['signal_channel']['tokenId']);
                $this->notification->setChat(Constant::CHAT['signal_channel']['chatId']);
                $this->notification->sendTelegramMessage($this->currency . " Buy:" . $orderDetail['buy'] . " Sell:" . $this->livePrice . " Lost:" . $stoplossBalance  . " Message: Lost: " . $lost);
                //$this->notification->pushOrder($orderOutput);
            }
            $balanceProperty = [
                'UserKey' => $this->user['UserKey'],
                'Token_Name' => $this->user['Token_Name'],
                'Profit' => $Profit,
                'Last_Order' => json_encode($orderDetail),
            ];
            saveBalanceJson($balanceProperty);
            $activeCount = $syncPoint['up']['active'];
            $activeCount = $activeCount - 1;
            $syncPoint['up']['active'] = $activeCount;
            $fullPath =  SYNC_PATH . '/pob' . '/' . $this->currency . ".json";
            file_put_contents($fullPath, json_encode($syncPoint));
            removeProtectiveDetail($this->currency);
            deleteSortOrderPositionDetail($this->currency);
        }
        $this->configurationApp();
    }

    /**
     * Fill in the blank
     */
    public function pushUserSortOrder()
    {

        //Exit Rule
        if (true) {
            return true;
        }
        return false;
    }
    public function configurationApp()
    {
        $balance = getBalance(['Token_Name' => $this->user['Token_Name']]);
        if (empty($balance)) {
            $balanceProperty = [
                'UserKey' => $this->user['UserKey'],
                'Token_Name' => $this->user['Token_Name'],
                'Profit' => 0,
                'Last_Order' => '',
            ];
            saveBalanceJson($balanceProperty);
        }
    }
}
