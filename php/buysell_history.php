<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 29.11.2017
 * Time: 12.25
 */

require_once('config.php');
require_once('session.php');

$response = array();

if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['bought'])) {
        $response["bought"] = getBuyHistory($DBH, $user_id);
    }

    if (isset($_POST['sold'])) {
        $response["sold"] = getSellHistory($DBH, $user_id);
    }
}

$json = json_encode($response);
echo $json;


function getBuyHistory($dbh, $user_id) {
    $query = "SELECT s.company, amount, tst
              FROM stock_event, stock AS s
              WHERE transaction_type='Buy' AND s.id=stock_id AND user_id='$user_id'";
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
    $query = "SELECT s.company, amount, tst
              FROM stock_event, stock AS s
              WHERE transaction_type='Sell' AND s.id=stock_id AND user_id='$user_id'";
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