<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 23.11.2017
 * Time: 9.38
 *
 *
 * login.php:n kutsuminen yrittää logata käyttäjän sisään.
 * Ottaa POST-parametreina arvot "username" ja "password".
 *
 * Palauttaa JSON objektin jonka rakenne on:
 * {
 *   "error": true tai false (false jos ei ollut virheitä)
 *   "message": virheilmoitus tai joku muu teksti
 * }
 */

define('SALT','PörssigurunSalasananSuolaus40t9ert0e9rt8er');

// virhetilat kirjautumiselle
define('LOGIN_OK', 1);
define('LOGIN_WRONG_PASS', 2);
define('LOGIN_NO_ACCOUNT', 3);
define('LOGIN_DB_ERROR', 4);

require_once('session.php');
require_once('config.php');
require_once('status_response.php');

$response = new StatusResponse();

if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $hashed_password = hash('sha256', $password.SALT);

    $result = tryLogin($DBH, $username, $hashed_password);
    switch ($result["status"]) {
        case LOGIN_OK: {
            // salasana täsmää, logataan sisään
            $_SESSION["username"] = $username;
            $_SESSION["password"] = $password;
            $_SESSION["user_id"] = $result["user_id"];
            $_SESSION["logged_in"] = true;
            // TODO: lisää sessioparametrejä?

            insertLogin($DBH, $_SESSION["user_id"]);

            // vastaus Ajaxille
            $response = new OKResponse("Sisäänkirjautuminen onnistui.");
            break;
        }
        case LOGIN_WRONG_PASS: {
            // salasana ei täsmää

            // vastaus Ajaxille
            $response = new FailResponse("Väärä salasana.");
            break;
        }
        case LOGIN_NO_ACCOUNT: {
            // käyttäjätunnusta ei löytynyt

            // vastaus Ajaxille
            $response = new FailResponse("Käyttäjätunnusta $username ei löydy.");
            break;
        }
        case LOGIN_DB_ERROR: {
            $response = new FailResponse("Tietokantavirhe.");
            break;
        }
    }
} else {
    if (!isset($_POST["username"])) {
        $response = new FailResponse("Käyttäjätunnus puuttuu.");
    }
    if (!isset($_POST["password"])) {
        $response = new FailResponse("Salasana puuttuu.");
    }
}

echo $response->getJSON();



function tryLogin($dbh, $username, $hashed_password) {
    $query = "SELECT id, username, pass FROM user_account WHERE username='$username'";
    $sql = $dbh->prepare($query);
    $sql->execute();

    $result = array();
    $result["user_id"] = null;

    try {
        if ($sql->rowCount() != 0) {
            // käyttäjätunnus löytyi

            $row = $sql->fetch();

            // täsmääkö salasana?
            if ($row["pass"] === $hashed_password) {
                $result["status"] = LOGIN_OK;
                $result["user_id"] = $row["id"];
            } else {
                $result["status"] = LOGIN_WRONG_PASS;
            }
        } else {
            $result["status"] = LOGIN_NO_ACCOUNT;
        }
    } catch (PDOException $e) {
        $result["status"] = LOGIN_DB_ERROR;
    }

    return $result;
}

/*
 * Lisää uusi sisään loggautuminen tauluun
 */

function insertLogin($dbh, $id) {
    if ($id != null) {
        // lisää uusi login
        $login_query = "INSERT INTO user_login(user_id, login)VALUES('$id', CURRENT_TIMESTAMP())";
        $sql = $dbh->prepare($login_query);
        $sql->execute();
    }
}