<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use Class\Application;

$users = getUsers(['key' => 'Order_Type', 'value' => 'sort']);
$forFilterTrait =  new Application();
$orderableUser =  $forFilterTrait->filterListByValue(0, $users, 'Order_Limit', 'up');
$orderableCurrencies = getSortOrderPositions();
//$userOrderAverage = getUserOrderAverage($allUsers, $orderableCurrencies);
foreach ($orderableCurrencies as $_currency) {
    $currency = str_replace('.json', '', $_currency);
    foreach ($orderableUser as $userKey => $user) {
        if ($user['Order_Limit'] > 0) {
            $userOrders = getUserOrder($user['Token_Name'], $currency);
            $app = new Application($currency);
            $app->initApplication($user['API_KEY'], $user['SECRET_KEY']);
            $app->initUser($user);
            $result[] =  $app->pushUserSortOrder();
        }
    }
    $successResult = array_count_values($result);
    if (array_key_exists(1, $successResult)) {
        $successCount = intval($successResult[1] / 2);
        if ($successCount >= count($orderableUser)) {
            deleteSortOrderPositionDetail($currency);
            removeProtectiveDetail($currency);
        }
    }
}
