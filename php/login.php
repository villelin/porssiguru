<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 23.11.2017
 * Time: 9.38
 */

define('SALT','PörssigurunSalasananSuolaus40t9ert0e9rt8er');

require_once('session.php');
require_once('config.php');

$response = array();

if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $hashed_password = hash('sha256', $password.SALT);

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