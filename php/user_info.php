<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 27.11.2017
 * Time: 12.33
 */

require_once('config.php');
require_once('session.php');

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

function getUserInfo($dbh, $user_id) {
    $query = "SELECT username, email, image, funds, description, DATE_FORMAT(signup_date, '%d/%m/%Y') AS 'signup'
              FROM user_account
              WHERE id='$user_id'";
    $sql = $dbh->prepare($query);
    $sql->execute();

    $result = "";

    try
    {
        if ($sql->rowCount() > 0) {
            $row = $sql->fetch();
            $result = array("username" => $row["username"], "email" => $row["email"], "image" => $row["image"],
                "description" => $row["description"], "signup_date" => $row["signup"], "funds" => $row["funds"]);
        }
    }
    catch (PDOException $e)
    {
    }

    return $result;
}