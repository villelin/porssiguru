<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 2.12.2017
 * Time: 21.40
 */

require_once('config.php');
require_once('session.php');
require_once('user_common.php');

$response = array();
$response["rank"] = null;

// onko haluttu user_id määritelty?
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $response["rank"] = getUserRank($DBH, $user_id);
} else if (isset($_SESSION['logged_in'])) {
    // käyttäjän id, jos ollaan loggauduttu sisään
    $user_id = $_SESSION['user_id'];

    $response["rank"] = getUserRank($DBH, $user_id);
} else {
    // ei tehdä mitään
}

$json = json_encode($response);
echo $json;