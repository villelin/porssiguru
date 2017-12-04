<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 29.11.2017
 * Time: 12.25
 */

require_once('config.php');
require_once('session.php');
require_once('user_common.php');

$response = array();

if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['bought'])) {
        $response["bought"] = getBuyHistory($DBH, $user_id);
    }

    if (isset($_POST['sold'])) {
        $response["sold"] = getSellHistory($DBH, $user_id);
    }
}

$json = json_encode($response);
echo $json;
