<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 20.11.2017
 * Time: 11.22
 */

require_once('config.php');
require_once('session.php');

define('SALT','PörssigurunSalasananSuolaus40t9ert0e9rt8er');

// uudella käyttäjällä on 10000€ käteistä
define('NEW_USER_BASE_FUNDS', 10000);

$response = array();

if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    // TODO: validoi tiedot

    // onko käyttäjätunnus jo olemassa?
    $exists = doesUsernameExist($DBH, $username);
    if ($exists === null) {
        // tietokantavirhe
        $response["error"] = true;
        $response["message"] = "Tietokantavirhe";
    } else if ($exists) {
        // on olemassa
        // vastaus Ajaxille
        $response["error"] = true;
        $response["message"] = "Käyttäjätunnus $username on jo olemassa.";
    } else {
        // ei ole
        $hashed_password = hash('sha256', $password.SALT);

        registerUsername($DBH, $username, $hashed_password, $email, NEW_USER_BASE_FUNDS);

        // vastaus Ajaxille
        $response["error"] = false;
        $response["message"] = "Käyttäjätunnuksen luonti onnistui.";
    }
} else {
    $response["error"] = true;
    $response["message"] = "Rekisteröinti ei toimi.";
}




$json = json_encode($response);
echo $json;

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
function registerUsername($dbh, $username, $password, $email, $funds) {
    $insert_query = "INSERT INTO user_account(username, email, pass, funds, description, image, signup_date)";
    $insert_query .= "VALUES('$username', '$email', '$password', '$funds', '', '', CURRENT_TIMESTAMP)";
    $sql = $dbh->prepare($insert_query);
    $sql->execute();
}

?>