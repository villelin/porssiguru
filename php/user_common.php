<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 1.12.2017
 * Time: 9.46
 */

/**
 * Kokeile voidaanko loggautua
 *
 * @param $dbh
 * @param $username
 * @param $hashed_password
 * @return array
 */

// virhetilat kirjautumiselle
define('LOGIN_OK', 1);
define('LOGIN_WRONG_PASS', 2);
define('LOGIN_NO_ACCOUNT', 3);
define('LOGIN_DB_ERROR', 4);

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