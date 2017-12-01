<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 20.11.2017
 * Time: 11.22
 */

require_once('config.php');
require_once('session.php');
require_once('status_response.php');
require_once('user_common.php');

define('SALT','PörssigurunSalasananSuolaus40t9ert0e9rt8er');

// uudella käyttäjällä on 10000€ käteistä
define('NEW_USER_BASE_FUNDS', 10000);

$response = new StatusResponse("");

if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // onko käyttäjätunnus jo olemassa?
    $exists = doesUsernameExist($DBH, $username);
    if ($exists === null) {
        // tietokantavirhe
        $response = new FailResponse("Tietokantavirhe");
    } else if ($exists) {
        // on olemassa
        // vastaus Ajaxille
        $response = new FailResponse("Käyttäjätunnus $username on jo olemassa.");
    } else {
        // ei ole

        // validoi tiedot
        if (preg_match("/^[A-Za-z_][A-Za-z0-9_]{3,14}$/", $username)) {
            // käyttäjätunnus ja E-mail OK
            $hashed_password = hash('sha256', $password.SALT);

            registerUsername($DBH, $username, $hashed_password, NEW_USER_BASE_FUNDS);

            // logataan sisään
            $result = tryLogin($DBH, $username, $hashed_password);
            if ($result["status"] == LOGIN_OK) {
                $_SESSION["username"] = $username;
                $_SESSION["password"] = $password;
                $_SESSION["user_id"] = $result["user_id"];
                $_SESSION["logged_in"] = true;

                insertLogin($DBH, $_SESSION["user_id"]);
            }

            // vastaus Ajaxille
            $response = new OKResponse("Käyttäjätunnuksen luonti onnistui.");
        } else {
            // käyttäjätunnus ei OK
            $response = new FailResponse("Käyttäjätunnuksessa sallitaan vain aakkoset, numerot ja alaviivat ja se saa olla 4-15 merkkiä pitkä.");
        }
    }
} else {
    $response = new FailResponse("Rekisteröinti ei toimi.");
}

echo $response->getJSON();




?>