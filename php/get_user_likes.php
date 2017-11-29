<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 29.11.2017
 * Time: 22.24
 */

require_once('config.php');
require_once('session.php');

$response = array();
$response["likes"] = 0;

if (isset($_POST['user_id'])) {
    // jos post-parametrissa on käyttäjä-id, palautetaan siitä käyttäjästä tykkäykset
    $user_id = $_POST['user_id'];

    $response["likes"] = countUserLikes($DBH, $user_id);

} else if (isset($_SESSION['logged_in'])) {
    // jos ollaan logattuna sisään, näytetään käyttäjästä tykkäykset
    $user_id = $_SESSION['user_id'];

    $response["likes"] = countUserLikes($DBH, $user_id);

} else {
    // muuten ei tehdä mitään
}

$json = json_encode($response);
echo $json;


function countUserLikes($dbh, $user_id) {
    $query = "SELECT COUNT(liked_id) FROM user_like WHERE liked_id='$user_id'";
    $sql = $dbh->prepare($query);
    $sql->execute();

    $count = 0;

    try {
        if ($sql->rowCount() > 0) {
            $row = $sql->fetch();

            error_log("row = $row[0]");

            $count = $row[0];
        }
    } catch (PDOException $e) {
    }

    return $count;
}