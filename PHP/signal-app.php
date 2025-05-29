<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use Class\Application;

$currencies = getCurrencies();

$user = getUser(['Token_Name' => 'Gived UserToken Name']);
foreach ($currencies as $currency) {
    //$currency='BNBUSDT';
    $app = new Application($currency);
    $app->setPath(SYNC_PATH);
    $app->initApplication($user['API_KEY'], $user['SECRET_KEY']);
    $app->startSync('up');
    $app->startSync('down');
}
