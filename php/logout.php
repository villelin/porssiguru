<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 22.11.2017
 * Time: 22.21
 *
 * logout.php:n kutsuminen loggaa nykyisen käyttäjän ulos.
 *
 * Palauttaa JSON objektin jonka rakenne on:
 * {
 *   "error": true tai false (false jos ei ollut virheitä)
 *   "message": virheilmoitus tai joku muu teksti
 * }
 *
 */

require_once('session.php');
require_once('config.php');
require_once('status_response.php');

$response = new StatusResponse();

if (isset($_SESSION["logged_in"])) {
    $user_id = $_SESSION["user_id"];
    $username = $_SESSION["username"];

    // lisää ulosloggautuminen tauluun
    insertLogout($DBH, $user_id);

    $response = new OKResponse("($user_id) $username loggas ulos");
} else {
    $response = new OKResponse("Ei oltu sisällä");
}

session_unset();
session_destroy();

echo $response->getJSON();


/*
 * Lisää uusi ulos loggautuminen tauluun
 */
function insertLogout($dbh, $id) {
    // hae viimeisin login
    $latest_query = "SELECT id FROM user_login WHERE user_id='$id' ORDER BY login DESC LIMIT 1";
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
