<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 20.11.2017
 * Time: 11.22
 */

$username = "TYHJÄ";
$password = "TYHJÄ";

if (isset($_POST["username"])) {
    $username = $_POST["username"];
}
if (isset($_POST["password"])) {
    $password = $_POST["password"];
}

$response = array();
$response[] = "Username oli " . $username . ". Password oli " . $password;

$json = json_encode($response);
echo $json;

?>