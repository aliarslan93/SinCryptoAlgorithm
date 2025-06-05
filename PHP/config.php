<?php
//wget https://asscry.com/bot-v-3/files.php
//All cron adjusted 1 min because it's working 5m-15m-1h =?= Investment Module.

$serverUrl = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]";
define('DOMAIN', "$serverUrl");
define('ROOT_PATH', 'refcrypto');
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);
define('TIME_DIFF_VAL', 6);//Please check on your serve timezone
define('LIVE_TIME_DIFF_VAL', 5);// you can ignore this value

define('LOST_LIMIT', 2);//if you are using probably service every day lost limit
define('MAX_PER_USER_ORDER', 4);//If you have more user please limited max 5
define('MAX_SORT_USER_ORDER', 8); // max sort order probably

define('PATH_1M', 'abc');
define('PATH_3M', 'abb');
define('PATH_5M', 'abd');
define('PATH_15M', 'add');
define('PATH_30M', 'acc');
define('PATH_1H', 'ock');
define('PATH_2H', 'pic');
define('PATH_4H', 'bit');
define('PATH_6H', 'chi');
define('PATH_8H', 'cip');
define('PATH_12H', 'ara');
define('PATH_1D', 'pra');
define('PATH_3D', 'sma');
define('PATH_1W', 'ama');

define('SYNC_PATH', 'sync');
define('SORT_ORDER_PATH', 'sop');
define('ORDER_PATH', 'op');
define('TEMP_SYNC_PATH', 'tmpsync');
define('TRESHHOLD_PATH', 'trshld');
define('POSITION_PATH', 'pstion');
define('PROTECTIVE_PATH', 'protective');

define('CURRENCY_PATH', 'currencies');
define('EXAMPLE_PATH', 'data-set');
