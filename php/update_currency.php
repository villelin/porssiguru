<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 8.12.2017
 * Time: 9.26
 */

require_once('config.php');
require_once('stock_update.php');

// haettavien valuuttojen lista
$currency_type_list = array();
$currency_type_list[] = array('symbol' => 'XAU', 'name' => 'Kulta (unssi)');
$currency_type_list[] = array('symbol' => 'XAG', 'name' => 'Hopea (unssi)');
$currency_type_list[] = array('symbol' => 'BTC', 'name' => 'Bitcoin');
$currency_type_list[] = array('symbol' => 'BCH', 'name' => 'Bitcoin-Cash');
$currency_type_list[] = array('symbol' => 'ETH', 'name' => 'Ethereum');
$currency_type_list[] = array('symbol' => 'LTC', 'name' => 'Litecoin');
$currency_type_list[] = array('symbol' => 'XMR', 'name' => 'Monero');
$currency_type_list[] = array('symbol' => 'XRP', 'name' => 'Ripples');
$currency_type_list[] = array('symbol' => 'DASH', 'name' => 'Dash');

$time_start = microtime(true);

// Valuutat
$currencies = getCurrencyData($currency_type_list, "Valuutta");

$time_end = microtime(true);
$total_time = $time_end - $time_start;

// päivitetään tietokanta
updateStockDatabase($DBH, $currencies);

echo "OK";