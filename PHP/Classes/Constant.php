<?php

namespace Class;

abstract class Constant
{
    const TIMES = [

        [
            'name' => 'Example Time Name',
            'limit' => -3,
            'path' => 'Path Choose in Config.php',
            'type' => 'give specific name of time',
        ],
        [
            'name' => 'Example Time Name',
            'limit' => 10,
            'path' => 'Path Choose in Config.php',
            'type' => 'give specific name of time',
        ],


    ];
    const CHAT = [
        'Telegram Push Signal' => [
            'chatId' => 'chatId',
            'tokenId' => "'Token'",
        ],
    ];
}
