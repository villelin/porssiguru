<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 30.11.2017
 * Time: 9.56
 */

require_once('config.php');
require_once('session.php');

$response = array();
$response["liked"] = false;

if (isset($_SESSION['logged_in'])) {
    if (isset($_POST['liked_id'])) {
        $user_id = $_SESSION['user_id'];
        $liked_id = $_POST['liked_id'];

        $response["liked"] = doesUserLikeUser($user_id, $liked_id);
    }
}

$json = json_encode($response);
echo $json;