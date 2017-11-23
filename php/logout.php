<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 22.11.2017
 * Time: 22.21
 */

require_once('session.php');
require_once('config.php');

$response = array();

if (isset($_SESSION["logged_in"])) {
    $username = $_SESSION["username"];

    // lisää ulosloggautuminen tauluun
    insertLogout($DBH, $username);

    $response["error"] = false;
    $response["message"] = "$username loggas ulos";
} else {
    $response["error"] = false;
    $response["message"] = "Ei oltu sisällä";
}

session_unset();
session_destroy();

$json = json_encode($response);
echo $json;


/*
 * Lisää uusi ulos loggautuminen tauluun
 */
function insertLogout($dbh, $username) {
    // hae viimeisin login
    $latest_query = "SELECT l.id FROM user_account AS u, user_login AS l WHERE u.username='$username' AND u.id=l.user_id ORDER BY l.login DESC LIMIT 1";
    $sql = $dbh->prepare($latest_query);
    $sql->execute();

    $last_id = null;

    try {
        if ($sql->rowCount() != 0) {
            // löytyi

            $row = $sql->fetch();
            $last_id = $row[0];
        } else {
            return;
        }
    } catch (PDOException $e) {
        return;
    }

    // päivitä uloskirjautumisaika
    if ($last_id != null) {
        $logout_query = "UPDATE user_login SET logout=CURRENT_TIMESTAMP() WHERE id='$last_id'";
        $sql = $dbh->prepare($logout_query);
        $sql->execute();
    }
}
