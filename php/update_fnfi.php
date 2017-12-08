<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 8.12.2017
 * Time: 9.25
 */

require_once('config.php');
require_once('stock_update.php');

$time_start = microtime(true);

// First North lista
$fnfi_list = parseStockData("https://beta.kauppalehti.fi/porssi/kurssit/FNFI", "First North");

$time_end = microtime(true);
$total_time = $time_end - $time_start;

// päivitetään tietokanta
updateStockDatabase($DBH, $fnfi_list);

echo "OK";