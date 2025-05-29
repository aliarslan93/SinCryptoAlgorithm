<?php

namespace Class;


class Notification extends Base
{
    const BASE_URL = 'https://api.telegram.org/bot';

    protected $chatId;
    protected $tokenId;
    public function setToken($tokenId)
    {
        $this->tokenId = $tokenId;
    }

    public function setChat($chatId)
    {
        $this->chatId = $chatId;
    }
    public function pushOrder($orderInput)
    {
        $message = "Currency:" . $orderInput['currency'] .
            " Buy:" . $orderInput['buy'] .
            " Sell:" . $orderInput['sell'] .
            " Stoploss:" . $orderInput['stoploss'] .
            " Profit:" . $orderInput['profit'];

        $this->sendTelegramMessage($message);
    }
    public function sendTelegramMessage($message)
    {
        $this->send($message);
    }
    private function send($text)
    {
        $query = http_build_query([
            'chat_id' => $this->chatId,
            'text' => $text,
        ]);
        $url = "https://api.telegram.org/bot{$this->tokenId}/sendMessage?{$query}";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        curl_exec($curl);
        curl_close($curl);
    }
}
