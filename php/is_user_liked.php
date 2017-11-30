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

        $query = "SELECT * FROM user_like WHERE user_id='$user_id' AND liked_id='$liked_id'";
        $sql = $DBH->prepare($query);
        $sql->execute();

        try {
            if ($sql->rowCount() > 0) {
                $response["liked"] = true;
            }
        } catch (PDOException $e) {
        }
    }
}

$json = json_encode($response);
echo $json;