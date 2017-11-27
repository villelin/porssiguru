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

        if ($stock_price != null && $user_funds != null) {
            // testataan onko tarpeeksi varaa
            $needed_funds = $stock_price * $amount;

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
    $dbh->beginTransaction();

    $result1 = $dbh->exec("INSERT INTO stock_event(user_id, stock_id, amount, transaction_type)VALUES('$user_id', '$stock_id', '$amount', 'Buy')");
    $result2 = $dbh->exec("UPDATE user_account SET funds=funds-'$needed_funds' WHERE id='$user_id'");

    if ($result1 === 0 || $result2 === 0) {
        // virhe -> rollback
        $dbh->rollBack();
    } else {
        // kaikki ok -> commit
        $dbh->commit();
    }
}