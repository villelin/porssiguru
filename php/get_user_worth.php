<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 1.12.2017
 * Time: 11.15
 */

require_once('config.php');
require_once('session.php');

$response = array();
$response["worth"] = 0;

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $response["worth"] = getUserWorth($DBH, $user_id);
} else if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];

    $response["worth"] = getUserWorth($DBH, $user_id);
}

$json = json_encode($response);
echo $json;


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