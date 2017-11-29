<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 29.11.2017
 * Time: 10.16
 */

require_once('config.php');
require_once('session.php');
require_once('status_response.php');

$response = array();

if (isset($_SESSION['logged_in'])) {
    // onko parametrina joku toinen käyttäjä?
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
    } else {
        // muuten logatun käyttäjän kommentit
        $user_id = $_SESSION['user_id'];
    }

    $query = "SELECT c.id, c.username, m.comment_text, m.parent_id, m.comment_date
              FROM user_account AS u, user_account AS c, comment AS m
              WHERE m.user_id=u.id AND m.commenter_id=c.id AND u.id='$user_id'";

    $sql = $DBH->prepare($query);
    $sql->execute();

    try {
        while ($row = $sql->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $response[] = array("commenter_id" => $row["id"], "username" => $row["username"], "text" => $row["comment_text"],
                    "reply_to" => $row["parent_id"], "date" => $row["comment_date"]);
        }
    } catch (PDOException $e) {
    }
}

$json = json_encode($response);
echo $json;