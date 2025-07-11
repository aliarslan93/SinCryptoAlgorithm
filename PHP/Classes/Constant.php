<?php

namespace Class;

abstract class Constant
{
    const TIMES = [

        [
            'name' => '1m',
            'limit' => 7,
            'path' => PATH_1M,
            'type' => 'Probably Time',
        ],
        [
            'name' => '4h',
            'limit' => 4,
            'path' => PATH_4H,
            'type' => 'Long Time',
        ]
        //Filled.


    ];
    const CHAT = [
        'Telegram Push Signal' => [
            'chatId' => 'chatId',
            'tokenId' => "'Token'",
        ],
    ];
}
