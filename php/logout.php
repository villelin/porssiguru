<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 22.11.2017
 * Time: 22.21
 */

require_once('session.php');

$response = array();

session_unset();
session_destroy();

$response["error"] = false;
$response["message"] = "Loggas ulos";

$json = json_encode($response);
echo $json;
