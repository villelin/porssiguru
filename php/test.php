<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 20.11.2017
 * Time: 20.17
 */

require_once('session.php');

$result = array();

if (isset($_SESSION["logged_in"])) {
    $username = $_SESSION["username"];
    $password = $_SESSION["password"];

    $result["result"] = "Hei $username, salasanasi on $password";
} else {
    $result["result"] = "Et ole loggautunut sisään ";
}

$json = json_encode($result);
echo $json;

?>