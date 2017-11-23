<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 20.11.2017
 * Time: 20.17
 */

require_once('session.php');

$response = array();

if (isset($_SESSION["logged_in"])) {
    $username = $_SESSION["username"];
    $password = $_SESSION["password"];

    $response["error"] = false;
    $response["message"] = "Hei $username, salasanasi on $password";
} else {
    $response["error"] = true;
    $response["message"] = "Et ole loggautunut sisään ";
}

$json = json_encode($response);
echo $json;

?>