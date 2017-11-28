<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 27.11.2017
 * Time: 13.02
 *
 * Osta ja myy osakkeita.
 * Käyttäjän pitää olla loggautuneena sisään ($_SESSION['logged_in'])
 * POST-parametrit:
 *  stock_id: Ostettavan/myytävän osakkeen ID
 *  amount: Montako osaketta myydään
 *  type: Operaation tyyppi ('buy' tai 'sell')
 *
 */

require_once('config.php');
require_once('session.php');

$response = array();

if (isset($_SESSION['logged_in'])) {
    if (isset($_POST['stock_id']) && isset($_POST['amount']) && isset($_POST['type'])) {
        $amount = intval($_POST['amount']);
        $stock_id = $_POST['stock_id'];
        $user_id = $_SESSION['user_id'];

        $transaction_type = $_POST['type'];

        if ($transaction_type == "buy") {
            // OSTAMINEN
            $response = attemptToBuy($DBH, $user_id, $stock_id, $amount);
        } else if ($transaction_type == "sell") {
            // MYYMINEN
            $response = attemptToSell($DBH, $user_id, $stock_id, $amount);
        } else {
            $response["error"] = true;
            $response["message"] = "Type parametri väärin.";
        }
    } else {
        $response["error"] = true;
        $response["message"] = "Parametrit väärin.";
    }
} else {
    $response["error"] = true;
    $response["message"] = "Ei olla logattuna.";
}

$json = json_encode($response);
echo $json;



function attemptToBuy($dbh, $user_id, $stock_id, $amount) {
    $buy_response = array();

    // haetaan osakkeen hinta
    $stock_price = getStockPrice($dbh, $stock_id);

    // haetaan käyttäjän käteiset
    $user_funds = getUserFunds($dbh, $user_id);

    if ($stock_price !== null && $user_funds !== null) {
        // testataan onko tarpeeksi varaa
        $needed_funds = $stock_price * $amount;

        if ($needed_funds <= $user_funds) {

            // kaikki OK, ostetaan
            if (buyStock($dbh, $user_id, $stock_id, $amount, $needed_funds)) {
                // onnistui
                $buy_response["error"] = false;
                $buy_response["message"] = "OK";
            } else {
                $buy_response["error"] = true;
                $buy_response["message"] = "Ostotapahtuma epäonnistui.";
            }
        } else {
            $buy_response["error"] = true;
            $buy_response["message"] = "Ei tarpeeksi käteistä.";
        }
    } else {
        $buy_response["error"] = true;
        $buy_response["message"] = "Jotain meni pieleen.";
    }

    return $buy_response;
}


function attemptToSell($dbh, $user_id, $stock_id, $amount) {
    $sell_response = array();

    // haetaan osakkeen hinta
    $stock_price = getStockPrice($dbh, $stock_id);

    // haetaan käyttäjän omistukset
    $user_stock = getUserStock($dbh, $user_id, $stock_id);

    if ($stock_price !== null && $user_stock !== null) {
        $sell_price = $stock_price * $amount;

        if ($amount <= $user_stock) {
            // kaikki OK -> myydään

            if (sellStock($dbh, $user_id, $stock_id, $amount, $sell_price)) {
                // onnistui
                $sell_response["error"] = false;
                $sell_response["message"] = "OK";
            } else {
                $sell_response["error"] = true;
                $sell_response["message"] = "Myyntitapahtuma epäonnistui.";
            }
        } else {
            $sell_response["error"] = true;
            $sell_response["message"] = "Ei tarpeeksi omistuksia.";
        }
    } else {
        $sell_response["error"] = true;
        $sell_response["message"] = "Jotain meni pieleen.";
    }

    return $sell_response;
}



function getStockPrice($dbh, $stock_id) {
    $query = "SELECT price FROM stock WHERE id='$stock_id'";
    $sql = $dbh->prepare($query);
    $sql->execute();

    $stock_price = null;

    try {
        if ($sql->rowCount() != 0) {
            $row = $sql->fetch();

            $stock_price = $row[0];
        }
    } catch (PDOException $e) {
    }
    return $stock_price;
}

function getUserFunds($dbh, $user_id) {
    $query = "SELECT funds FROM user_account WHERE id='$user_id'";
    $sql = $dbh->prepare($query);
    $sql->execute();

    $user_funds = null;

    try {
        if ($sql->rowCount() != 0) {
            $row = $sql->fetch();

            $user_funds = doubleval($row[0]);
        }
    } catch (PDOException $e) {
    }
    return $user_funds;
}

function getUserStock($dbh, $user_id, $stock_id) {
    $query = "SELECT buy_sum-sell_sum AS 'assets'
              FROM(
               SELECT user_id, stock_id, SUM(buy) AS buy_sum, SUM(sell) AS sell_sum
               FROM(
                (SELECT user_id, stock_id, amount AS 'buy', 0 AS 'sell'
                FROM stock_event
                WHERE transaction_type='Buy' AND user_id='$user_id' AND stock_id='$stock_id')
                UNION ALL
                (SELECT user_id, stock_id, 0 AS 'buy', amount AS 'sell'
                FROM stock_event
                WHERE transaction_type='Sell' AND user_id='$user_id' AND stock_id='$stock_id')
               ) AS summed
               GROUP BY user_id, stock_id
              ) AS final, stock
              WHERE final.stock_id=stock.id";
    $sql = $dbh->prepare($query);
    $sql->execute();

    $user_stock = 0;

    try {
        if ($sql->rowCount() != 0) {
            $row = $sql->fetch();

            $user_stock = intval($row[0]);
        }
    } catch (PDOException $e) {
    }

    return $user_stock;
}

function buyStock($dbh, $user_id, $stock_id, $amount, $needed_funds) {
    // lisää osto ja poista käyttäjältä rahaa
    $dbh->beginTransaction();

    $result1 = $dbh->exec("INSERT INTO stock_event(user_id, stock_id, amount, transaction_type)VALUES('$user_id', '$stock_id', '$amount', 'Buy')");
    $result2 = $dbh->exec("UPDATE user_account SET funds=funds-'$needed_funds' WHERE id='$user_id'");

    if ($result1 === 0 || $result2 === 0) {
        // virhe -> rollback
        $dbh->rollBack();
        return false;
    } else {
        // kaikki ok -> commit
        $dbh->commit();
        return true;
    }
}

function sellStock($dbh, $user_id, $stock_id, $amount, $sell_price) {
    // lisää myynti ja anna käyttäjälle rahaa
    $dbh->beginTransaction();

    $result1 = $dbh->exec("INSERT INTO stock_event(user_id, stock_id, amount, transaction_type)VALUES('$user_id', '$stock_id', '$amount', 'Sell')");
    $result2 = $dbh->exec("UPDATE user_account SET funds=funds+'$sell_price' WHERE id='$user_id'");

    if ($result1 === 0 || $result2 === 0) {
        // virhe -> rollback
        $dbh->rollBack();
        return false;
    } else {
        // kaikki ok -> commit
        $dbh->commit();
        return true;
    }
}