<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 20.11.2017
 * Time: 11.22
 */

require_once('session.php');

$username = "TYHJÄ";
$password = "TYHJÄ";

if (isset($_POST["username"])) {
    $username = $_POST["username"];
}
if (isset($_POST["password"])) {
    $password = $_POST["password"];
}

$response = array();

if ($username === "Masa") {
    $_SESSION["username"] = $username;
    $_SESSION["password"] = $password;
    $_SESSION["logged_in"] = true;
    $response[] = "Masa loggas sisään";
} else {
    $_SESSION["username"] = $username;
    $_SESSION["password"] = $password;
    $_SESSION["logged_in"] = true;
    $response[] = "Username oli " . $username . ". Password oli " . $password;
}




$json = json_encode($response);
echo $json;

?>