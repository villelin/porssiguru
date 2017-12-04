<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 27.11.2017
 * Time: 12.33
 */

require_once('config.php');
require_once('session.php');
require_once('user_common.php');

$response = array();

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $response["user_info"] = getUserInfo($DBH, $user_id);
} else if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];

    $response["user_info"] = getUserInfo($DBH, $user_id);
}

$json = json_encode($response);
echo $json;

