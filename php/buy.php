<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 27.11.2017
 * Time: 13.02
 */

require_once('config.php');
require_once('session.php');

$response = array();

if (isset($_SESSION['logged_in'])) {
    if (isset($_POST['stock_id']) && isset($_POST['amount'])) {
        $amount = intval($_POST['amount']);
        $stock_id = $_POST['stock_id'];
        $user_id = $_SESSION['user_id'];

        // haetaan osakkeen hinta
        $stock_price = getStockPrice($DBH, $stock_id);

        // haetaan käyttäjän käteiset
        $user_funds = getUserFunds($DBH, $user_id);

        error_log("stock price = $stock_price, user funds = $user_funds");

        if ($stock_price != null && $user_funds != null) {
            // testataan onko tarpeeksi varaa
            $needed_funds = $stock_price * $amount;

            error_log("needed funds = $needed_funds");

            if ($needed_funds <= $user_funds) {

                // kaikki OK, ostetaan
                buyStock($DBH, $user_id, $stock_id, $amount, $needed_funds);

                $response["error"] = false;
                $response["message"] = "OK";
            } else {
                $response["error"] = true;
                $response["message"] = "Ei tarpeeksi käteistä.";
            }
        } else {
            $response["error"] = true;
            $response["message"] = "Jotain meni pieleen.";
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

function buyStock($dbh, $user_id, $stock_id, $amount, $needed_funds) {
    // lisää osto ja poista käyttäjältä rahaa
    $query = "START TRANSACTION;
              INSERT INTO stock_event(user_id, stock_id, amount, transaction_type)VALUES('$user_id', '$stock_id', '$amount', 'Buy');
              UPDATE user_account SET funds=funds-'$needed_funds' WHERE id='$user_id';
              COMMIT";
    $sql = $dbh->prepare($query);
    $sql->execute();
}