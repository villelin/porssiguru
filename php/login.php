<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 23.11.2017
 * Time: 9.38
 */

define('SALT','PörssigurunSalasananSuolaus40t9ert0e9rt8er');

// virhetilat kirjautumiselle
define('LOGIN_OK', 1);
define('LOGIN_WRONG_PASS', 2);
define('LOGIN_NO_ACCOUNT', 3);
define('LOGIN_DB_ERROR', 4);

require_once('session.php');
require_once('config.php');

$response = array();

if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $hashed_password = hash('sha256', $password.SALT);

    $result = tryLogin($DBH, $username, $hashed_password);
    switch ($result) {
        case LOGIN_OK: {
            // salasana täsmää, logataan sisään
            $_SESSION["username"] = $username;
            $_SESSION["password"] = $password;
            $_SESSION["logged_in"] = true;
            // TODO: lisää sessioparametrejä?

            insertLogin($DBH, $username);

            // vastaus Ajaxille
            $response["error"] = false;
            $response["message"] = "Sisäänkirjautuminen onnistui.";
            break;
        }
        case LOGIN_WRONG_PASS: {
            // salasana ei täsmää

            // vastaus Ajaxille
            $response["error"] = true;
            $response["message"] = "Väärä salasana.";
            break;
        }
        case LOGIN_NO_ACCOUNT: {
            // käyttäjätunnusta ei löytynyt

            // vastaus Ajaxille
            $response["error"] = true;
            $response["message"] = "Käyttäjätunnusta $username ei löydy.";
            break;
        }
        case LOGIN_DB_ERROR: {
            $response["error"] = true;
            $response["message"] = "Tietokantavirhe.";
            break;
        }
    }

    /*
    $query = "SELECT id, username, pass FROM user_account WHERE username='$username'";
    $sql = $DBH->prepare($query);
    $sql->execute();

    try {
        if ($sql->rowCount() != 0) {
            // käyttäjätunnus löytyi

            $row = $sql->fetch();

            // täsmääkö salasana?
            if ($row["pass"] === $hashed_password) {
                // salasana täsmää, logataan sisään
                $_SESSION["username"] = $username;
                $_SESSION["password"] = $password;
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["logged_in"] = true;
                // TODO: lisää sessioparametrejä?



                // vastaus Ajaxille
                $response["error"] = false;
                $response["message"] = "Sisäänkirjautuminen onnistui.";
            } else {
                // salasana ei täsmää

                // vastaus Ajaxille
                $response["error"] = true;
                $response["message"] = "Väärä salasana.";
            }
        } else {
            // käyttäjätunnusta ei löytynyt

            // vastaus Ajaxille
            $response["error"] = true;
            $response["message"] = "Käyttäjätunnusta $username ei löydy.";
        }
    } catch (PDOException $e) {
        $response["error"] = true;
        $response["message"] = "Tietokantavirhe.";
    }
    */
} else {
    $response["error"] = true;
    $response["message"] = "";
    if (!isset($_POST["username"])) {
        $response["message"] .= "Käyttäjätunnus puuttuu. ";
    }
    if (!isset($_POST["password"])) {
        $response["message"] .= "Salasana puuttuu. ";
    }
}

$json = json_encode($response);
echo $json;



function tryLogin($dbh, $username, $hashed_password) {
    $query = "SELECT id, username, pass FROM user_account WHERE username='$username'";
    $sql = $dbh->prepare($query);
    $sql->execute();

    try {
        if ($sql->rowCount() != 0) {
            // käyttäjätunnus löytyi

            $row = $sql->fetch();

            // täsmääkö salasana?
            if ($row["pass"] === $hashed_password) {
                return LOGIN_OK;
            } else {
                return LOGIN_WRONG_PASS;
            }
        } else {
            return LOGIN_NO_ACCOUNT;
        }
    } catch (PDOException $e) {
        return LOGIN_DB_ERROR;
    }
}

/*
 * Lisää uusi sisään loggautumine tauluun
 */

function insertLogin($dbh, $username) {
    // hae user id
    $user_query = "SELECT id FROM user_account WHERE username='$username'";
    $sql = $dbh->prepare($user_query);
    $sql->execute();

    $user_id = null;

    try {
        if ($sql->rowCount() != 0) {
            // löytyi

            $row = $sql->fetch();
            $user_id = $row["id"];
        } else {
            return;
        }
    } catch (PDOException $e) {
        return;
    }

    if ($user_id != null) {
        // lisää uusi login

        $login_query = "INSERT INTO user_login(user_id, login)VALUES('$user_id', CURRENT_TIMESTAMP())";
        $sql = $dbh->prepare($login_query);
        $sql->execute();
    }
}