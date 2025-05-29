<?php
//wget https://asscry.com/bot-v-3/files.php
//All cron adjusted 1 min because it's working 5m-15m-1h =?= Investment Module.

$serverUrl = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]";
define('DOMAIN', "$serverUrl");
define('ROOT_PATH', 'refcrypto');
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);
define('TIME_DIFF_VAL', 3);
define('LIVE_TIME_DIFF_VAL', 5);

define('LOST_LIMIT', -3);
define('MAX_PER_USER_ORDER', 100);
define('MAX_SORT_USER_ORDER', 200);

define('PATH_1M', 'Path Name');
define('PATH_3M', 'Path Name');
define('PATH_5M', 'Path Name');
define('PATH_15M', 'Path Name');
define('PATH_30M', 'Path Name');
define('PATH_1H', 'Path Name');
define('PATH_2H', 'Path Name');
define('PATH_4H', 'Path Name');
define('PATH_6H', 'Path Name');
define('PATH_8H', 'Path Name');
define('PATH_12H', 'Path Name');
define('PATH_1D', 'Path Name');
define('PATH_3D', 'Path Name');
define('PATH_1W', 'Path Name');

define('SYNC_PATH', 'Path Name');
define('SORT_ORDER_PATH', 'Path Name');
define('ORDER_PATH', 'Path Name');
define('TEMP_SYNC_PATH', 'Path Name');
define('TRESHHOLD_PATH', 'Path Name');
define('POSITION_PATH', 'Path Name');
define('PROTECTIVE_PATH', 'Path Name');

define('CURRENCY_PATH', 'currencies');
define('EXAMPLE_PATH', 'data-set');
