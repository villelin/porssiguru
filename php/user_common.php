<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 1.12.2017
 * Time: 9.46
 */

// Yleisiä funktioita

// virhetilat kirjautumiselle
define('LOGIN_OK', 1);
define('LOGIN_WRONG_PASS', 2);
define('LOGIN_NO_ACCOUNT', 3);
define('LOGIN_DB_ERROR', 4);

/**
 * Kokeile voidaanko loggautua
 *
 * @param $dbh
 * @param $username
 * @param $hashed_password
 * @return array
 */
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



function leaderboardCompare($a, $b) {
    if ($a["assets"] > $b["assets"]) {
        return -1;
    } else if($a["assets"] < $b["assets"]) {
        return 1;
    } else {
        return 0;
    }
}

function getLeaderboard($dbh, $num)
{
    $user_assets = array();

    // käyttäjien osakeomistukset
    $query = "SELECT user_id, SUM(assets)
              FROM(
              SELECT user_id, stock.company, (buy_sum-sell_sum)*stock.price AS 'assets'
              FROM(
               SELECT user_id, stock_id, SUM(buy) AS buy_sum, SUM(sell) AS sell_sum
               FROM(
               (SELECT user_id, stock_id, amount AS 'buy', 0 AS 'sell'
                FROM stock_event
                WHERE transaction_type='Buy')
                UNION ALL
               (SELECT user_id, stock_id, 0 AS 'buy', amount AS 'sell'
                FROM stock_event
                WHERE transaction_type='Sell')
               ) AS summed
               GROUP BY user_id, stock_id
              ) AS final, stock
              WHERE final.stock_id=stock.id
              ) AS a
              GROUP BY user_id";

    $sql = $dbh->prepare($query);
    $sql->execute();

    try {
        while ($row = $sql->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            $user_id = $row[0];
            $assets = $row[1];

            $user_assets["$user_id"]["stock"] = $assets;
        }
    } catch (PDOException $e) {
    }

    // käyttäjien käteiset
    $query = "SELECT id, username, funds, image FROM user_account";
    $sql = $dbh->prepare($query);
    $sql->execute();

    $user_funds = array();

    try {
        while ($row = $sql->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            $user_id = $row[0];
            $username = $row[1];
            $funds = $row[2];
            $image = $row[3];

            $user_assets["$user_id"]["funds"] = $funds;
            $user_assets["$user_id"]["username"] = $username;
            $user_assets["$user_id"]["image"] = $image;
        }
    } catch (PDOException $e) {
    }

    $leaderboard = array();

    foreach ($user_assets as $user_id => $item) {
        $stock = 0;
        $funds = 0;
        if (isset($item["stock"])) {
            $stock = $item["stock"];
        }
        if (isset($item["funds"])) {
            $funds = $item["funds"];
        }

        $assets = $stock + $funds;

        $leaderboard[] = array("user_id" => $user_id, "username" => $item["username"], "assets" => $assets, "image" => $item["image"]);
    }

    // järjestetään assettien mukaan laskevasti
    usort($leaderboard, "leaderboardCompare");

    // jos haluttu määrä on määritelty, leikataan muut pois
    if ($num != 0) {
        $leaderboard = array_slice($leaderboard, 0, $num);
    }
    return $leaderboard;
}