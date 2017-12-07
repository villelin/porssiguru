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


/*
 * Palauttaa käyttäjän nykyisen rankingin
 */
function getUserRank($dbh, $user_id) {
    $leaderboard = getLeaderboard($dbh, 0);

    foreach($leaderboard as $index => $item) {
        if ($item["user_id"] == $user_id) {
            return $index+1;
        }
    }
    return null;
}



function getUserInfo($dbh, $user_id) {
    $query = "SELECT username, image, funds, description, DATE_FORMAT(signup_date, '%d.%m.%Y') AS 'signup'
              FROM user_account
              WHERE id='$user_id'";
    $sql = $dbh->prepare($query);
    $sql->execute();

    $result = "";

    try
    {
        if ($sql->rowCount() > 0) {
            $row = $sql->fetch();
            $result = array("username" => $row["username"], "image" => $row["image"],
                "description" => $row["description"], "signup_date" => $row["signup"], "funds" => $row["funds"]);
        }
    }
    catch (PDOException $e)
    {
    }

    return $result;
}


function getUserFunds($dbh, $user_id) {
    $query = "SELECT funds FROM user_account WHERE id='$user_id'";

    $sql = $dbh->prepare($query);
    $sql->execute();

    $user_funds = 0;

    try
    {
        if ($sql->rowCount() != 0) {
            $row = $sql->fetch();
            if ($row[0] != null) {
                $user_funds = $row[0];
            }
        }
    }
    catch (PDOException $e)
    {
    }
    return $user_funds;
}



function getUserWorth($dbh, $user_id) {
    // käyttäjän osakkeet
    $query = "SELECT SUM(assets)
              FROM(
               SELECT user_id, (buy_sum-sell_sum)*stock.price AS 'assets'
               FROM(
                SELECT user_id, stock_id, SUM(buy) AS buy_sum, SUM(sell) AS sell_sum
                FROM(
                (SELECT user_id, stock_id, amount AS 'buy', 0 AS 'sell'
                 FROM stock_event
                 WHERE transaction_type='Buy' AND user_id='$user_id')
                 UNION ALL
                (SELECT user_id, stock_id, 0 AS 'buy', amount AS 'sell'
                 FROM stock_event
                 WHERE transaction_type='Sell' AND user_id='$user_id')
                ) AS summed
                GROUP BY user_id, stock_id
               ) AS final, stock
               WHERE final.stock_id=stock.id
              ) AS a";
    $sql = $dbh->prepare($query);
    $sql->execute();

    $user_stock = 0;

    try
    {
        if ($sql->rowCount() != 0) {
            $row = $sql->fetch();
            if ($row[0] != null) {
                $user_stock = $row[0];
            }
        }
    }
    catch (PDOException $e)
    {
    }

    // käyttäjän käteiset
    $query = "SELECT funds FROM user_account WHERE id='$user_id'";

    $sql = $dbh->prepare($query);
    $sql->execute();

    $user_funds= 0;

    try
    {
        if ($sql->rowCount() != 0) {
            $row = $sql->fetch();
            if ($row[0] != null) {
                $user_funds = $row[0];
            }
        }
    }
    catch (PDOException $e)
    {
    }

    return $user_stock + $user_funds;
}



function getUserComments($dbh, $user_id) {
    $query = "SELECT c.id, c.username, m.comment_text, m.parent_id, DATE_FORMAT(m.comment_date, '%d.%m.%Y %k:%i:%s') AS 'comment_date'
              FROM user_account AS u, user_account AS c, comment AS m
              WHERE m.user_id=u.id AND m.commenter_id=c.id AND u.id='$user_id'";

    $sql = $dbh->prepare($query);
    $sql->execute();

    $results = array();

    try {
        while ($row = $sql->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $results[] = array("commenter_id" => $row["id"], "username" => $row["username"], "text" => $row["comment_text"],
                "reply_to" => $row["parent_id"], "date" => $row["comment_date"]);
        }
    } catch (PDOException $e) {
    }

    return $results;
}


function countUserLikes($dbh, $user_id) {
    $query = "SELECT COUNT(liked_id) FROM user_like WHERE liked_id='$user_id'";
    $sql = $dbh->prepare($query);
    $sql->execute();

    $count = 0;

    try {
        if ($sql->rowCount() > 0) {
            $row = $sql->fetch();

            $count = $row[0];
        }
    } catch (PDOException $e) {
    }

    return $count;
}


function getBuyHistory($dbh, $user_id) {
    $query = "SELECT s.company, amount, DATE_FORMAT(tst, '%d.%m.%Y %k:%i:%s') AS 'tst'
              FROM stock_event, stock AS s
              WHERE transaction_type='Buy' AND s.id=stock_id AND user_id='$user_id'
              ORDER BY tst DESC";
    $sql = $dbh->prepare($query);
    $sql->execute();

    $buy_list = array();

    try {
        while ($row = $sql->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $buy_list[] = array("company" => $row["company"], "amount" => $row["amount"], "date" => $row["tst"]);
        }
    } catch (PDOException $e) {
    }

    return $buy_list;
}


function getSellHistory($dbh, $user_id) {
    $query = "SELECT s.company, amount, DATE_FORMAT(tst, '%d.%m.%Y %k:%i:%s') AS 'tst'
              FROM stock_event, stock AS s
              WHERE transaction_type='Sell' AND s.id=stock_id AND user_id='$user_id'
              ORDER BY tst DESC";
    $sql = $dbh->prepare($query);
    $sql->execute();

    $sell_list = array();

    try {
        while ($row = $sql->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $sell_list[] = array("company" => $row["company"], "amount" => $row["amount"], "date" => $row["tst"]);
        }
    } catch (PDOException $e) {
    }

    return $sell_list;
}


function doesUserLikeUser($dbh, $user_id, $liked_id)
{
    $query = "SELECT * FROM user_like WHERE user_id='$user_id' AND liked_id='$liked_id'";
    $sql = $dbh->prepare($query);
    $sql->execute();

    $result = false;

    try {
        if ($sql->rowCount() > 0) {
            $result = true;
        }
    } catch (PDOException $e) {
    }

    return $result;
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