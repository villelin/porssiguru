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

if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];

    $query = "SELECT username, email, image, funds, description, signup_date
              FROM user_account
              WHERE id='$user_id'";
    $sql = $DBH->prepare($query);
    $sql->execute();

    try
    {
        if ($sql->rowCount() > 0) {
            $row = $sql->fetch();
            $response["user_info"] = array("username" => $row["username"], "email" => $row["email"], "image" => $row["image"],
                "description" => $row["description"], "signup_date" => $row["signup_date"], "funds" => $row["funds"]);
        }
    }
    catch (PDOException $e)
    {
    }
}

$json = json_encode($response);
echo $json;