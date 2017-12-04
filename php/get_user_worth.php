<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 1.12.2017
 * Time: 11.15
 */

require_once('config.php');
require_once('session.php');
require_once('user_common.php');

$response = array();
$response["worth"] = 0;

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $response["worth"] = getUserWorth($DBH, $user_id);
} else if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];

    $response["worth"] = getUserWorth($DBH, $user_id);
}

$json = json_encode($response);
echo $json;


