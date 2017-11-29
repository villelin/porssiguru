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


/*
 * Tarkistaa tietokannasta onko käyttäjätunnus jo olemassa
 *
 * Palauttaa true tai false. Null jos tietokannan käsittelyssä on virhe.
 */
function doesUsernameExist($dbh, $username) {
    $find_query = "SELECT id FROM user_account WHERE username='$username'";
    $sql = $dbh->prepare($find_query);
    $sql->execute();

    try {
        if ($sql->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    } catch (PDOException $e) {
        return null;
    }
}

/*
 * Rekisteröi käyttäjänimen tietokantaan
 */
function registerUsername($dbh, $username, $password, $funds) {
    $insert_query = "INSERT INTO user_account(username, pass, funds, description, image, signup_date)";
    $insert_query .= "VALUES('$username', '$password', '$funds', '', '', CURRENT_TIMESTAMP)";
    $sql = $dbh->prepare($insert_query);
    $sql->execute();
}

?>