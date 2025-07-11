<?php

if (!function_exists('dd')) {

    function dd($params, $str = false)
    {
        echo "<pre>";
        if ($str) {
            var_dump($params);
        } else {
            print_r($params);
        }
        die;
    }
}
if (!function_exists('getPositions')) {
    function getPositions()
    {
        $syncs = scandir(__DIR__ . "/" . POSITION_PATH);
        unset($syncs[0]);
        unset($syncs[1]);
        return $syncs;
    }
}
if (!function_exists('getCurrencies')) {
    function getCurrencies($fileName = null)
    {
        $currencies = [];
        if (is_null($fileName)) {
            $fileName = 'rules.json';
        }

        $rules = json_decode(file_get_contents(__DIR__ . '/' . CURRENCY_PATH . '/' . $fileName), true);
        foreach ($rules as $rule) {
            $currencies[] = $rule['currency'];
        }
        return $currencies;
    }
}
if (!function_exists('getUser')) {
    function getUser($searchParams = [])
    {
        $users = json_decode(file_get_contents(__DIR__ . '/user.json'), true);
        if (empty($searchParams)) {
            return $users;
        }
        foreach ($users as $user) {
            if ($user['Token_Name'] == $searchParams['Token_Name']) {
                return $user;
            }
        }
        return [];
    }
}
if (!function_exists('getBalance')) {
    function getBalance($searchParams = [])
    {
        $users = json_decode(file_get_contents(__DIR__ . '/balance.json'), true);
        if (empty($searchParams)) {
            return $users;
        }
        foreach ($users as $user) {
            if ($user['Token_Name'] == $searchParams['Token_Name']) {
                return $user;
            }
        }
        return [];
    }
}

if (!function_exists('saveBalanceJson')) {
    function saveBalanceJson($balance)
    {
        $allBalance = getBalance();
        $allBalance[$balance['UserKey']] = $balance;
        file_put_contents(__DIR__ . '/balance.json', json_encode($allBalance));
    }
}
if (!function_exists('getUsers')) {
    function getUsers($searchParams = ['key' => '', 'value' => ''])
    {
        $users = json_decode(file_get_contents(__DIR__ . '/user.json'), true);
        $result = [];
        foreach ($users as $userKey => $user) {
            if ($user[$searchParams['key']] == $searchParams['value']) {
                $result[$userKey] = $user;
            }
        }
        return $result;
    }
}
if (!function_exists('downUserOrderLimit')) {
    function downUserOrderLimit($user)
    {
        if ($user['Order_Limit'] > 0) {
            $user['Order_Limit']--;
        }
        saveUserJson($user);
    }
}
if (!function_exists('saveUserJson')) {
    function saveUserJson($user)
    {

        $users = getUsers();
        foreach ($users as $userKey => $u) {

            if ($u['UserKey'] == $user['UserKey']) {
                $users[$userKey] = $user;
            }
        }
        $savePath = __DIR__ . "/user.json";
        file_put_contents($savePath, json_encode($users));
    }
}

if (!function_exists('deleteSortOrderPositionDetail')) {
    function deleteSortOrderPositionDetail($currency)
    {
        $path = __DIR__ . "/" . SORT_ORDER_PATH . "/" . $currency . ".json";
        unlink($path);
    }
}
if (!function_exists('getSortOrderPositionDetail')) {
    function getSortOrderPositionDetail($currency)
    {
        $position = file_get_contents(__DIR__ . "/" . SORT_ORDER_PATH . "/" . $currency . ".json");
        return json_decode($position, true);
    }
}
if (!function_exists('getSortOrderPositions')) {
    function getSortOrderPositions()
    {
        $paths = scandir(SORT_ORDER_PATH);
        unset($paths[0]);
        unset($paths[1]);
        return $paths;
    }
}
if (!function_exists('getPositionDetail')) {
    function getPositionDetail(string $currency = '')
    {
        $position = file_get_contents(__DIR__ . "/" . POSITION_PATH . "/" . $currency . ".json");
        return json_decode($position, true);
    }
}

if (!function_exists('getProtectiveDetail')) {
    function getProtectiveDetail(string $currency = '')
    {
        $position = file_get_contents(__DIR__ . "/" . PROTECTIVE_PATH . "/" . $currency . ".json");
        return json_decode($position, true);
    }
}
if (!function_exists('removeProtectiveDetail')) {
    function removeProtectiveDetail(string $currency = '')
    {
        $path = file_get_contents(__DIR__ . "/" . PROTECTIVE_PATH . "/" . $currency . ".json");
        unlink($path);
    }
}
if (!function_exists('getUserOrder')) {
    function getUserOrder(string $userToken = '', string $currency = '')
    {
        $order = file_get_contents(__DIR__ . "/" . ORDER_PATH . "/$userToken" . "/" . $currency . ".json");
        return json_decode($order, true);
    }
}

if (!function_exists('getUserOrders')) {
    function getUserOrders($user)
    {
        $scanPath = __DIR__ . "/" . ORDER_PATH . "/" . $user['Token_Name'];
        if (!file_exists("$scanPath")) {

            mkdir("$scanPath", 0777, true);
        }
        $syncs = scandir($scanPath);
        unset($syncs[0]);
        unset($syncs[1]);
        return $syncs;
    }
}
if (!function_exists('getRules')) {
    function getRules(string $currency, $fileName = null)
    {
        if ($currency == '')
            return false;

        $result = [];
        if (is_null($fileName)) {
            $fileName = 'rules.json';
        }
        $rules = json_decode(file_get_contents(__DIR__ . '/' . CURRENCY_PATH . '/' . $fileName), true);

        foreach ($rules as $rule) {
            if ($rule['currency'] == $currency) {
                $result = (array)$rule;
                break;
            }
        }
        return $result;
    }
}
if (!function_exists('getTrehholdPoint')) {
    function getTrehholdPoint(string $currency, string $path)
    {
        $fileName =  TRESHHOLD_PATH . "/" . $path . "/$currency.json";
        $fileExist = file_exists($fileName);
        if ($fileExist) {
            $treshhold = json_decode(file_get_contents($fileName), true);
            return  $treshhold;
        }
        return [];
    }
}
if (!function_exists('getSyncPoint')) {
    function getSyncPoint(string $currency, string $path)
    {
        $fileName =  $path . "/$currency.json";
        $fileExist = file_exists($fileName);
        if ($fileExist) {
            $treshhold = json_decode(file_get_contents($fileName), true);
            return  $treshhold;
        }
        return [];
    }
}
if (!function_exists('calculateTimestampDiff')) {
    function calculateTimestampDiff($endTime, $startTime)
    {
        $response = $endTime - $startTime;
        return $response / 60;
    }
}
if (!function_exists('getSortOrderable')) {
    function getSortOrderable($currency, $sortPath)
    {
        $syncs = scandir(__DIR__ . "/" . SYNC_PATH . "/" . $sortPath);
        unset($syncs[0]);
        unset($syncs[1]);
        foreach ($syncs as $path => $sync) {
            $path = SYNC_PATH . "/" . $sync;
            return getSyncPoint($currency, $path);
        }
    }
}
if (!function_exists('getOrderable')) {
    function getOrderable($currency)
    {
        $syncs = scandir(__DIR__ . "/" . SYNC_PATH);
        unset($syncs[0]);
        unset($syncs[1]);
        foreach ($syncs as $path => $sync) {
            $path = SYNC_PATH . "/" . $sync;
            return getSyncPoint($currency, $path);
        }
    }
}
if (!function_exists('setConfig')) {
    function setConfig($data)
    {
        file_put_contents('app.json', json_encode($data));
    }
}
if (!function_exists('getConfig')) {
    function getConfig()
    {
        return json_decode(file_get_contents('app.json'), true);
    }
}
if (!function_exists('getExampleFile')) {

    function getExampleFile($currency)
    {
        //$currency = 'BTCUSDT';
        $fileName = file_get_contents(EXAMPLE_PATH . "/" . "$currency.json");
        return json_decode($fileName, true);
    }
}
if (!function_exists('getTimestamp')) {
    function getTimestamp()
    {
        return (time() + (60 * 60 * 3));
    }
}
