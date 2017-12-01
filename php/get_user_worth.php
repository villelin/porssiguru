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

if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];

    // osakkeiden arvo
    $query = "SELECT SUM(b.assets)
              FROM
              (SELECT (buy_sum-sell_sum)*stock.price AS 'assets'
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
              WHERE final.stock_id=stock.id) AS b";
    $sql = $DBH->prepare($query);
    $sql->execute();

    $stock_worth = 0;

    try
    {
        if ($sql->rowCount() != 0) {
            $row = $sql->fetch();
            if ($row[0] != null) {
                $stock_worth = $row[0];
            }
        }
    }
    catch (PDOException $e)
    {
    }

    // käteisvarat
    $funds = 0;

    $query = "SELECT funds FROM user_account WHERE id='$user_id'";
    $sql = $DBH->prepare($query);
    $sql->execute();
    
    try
    {
        if ($sql->rowCount() != 0) {
            $row = $sql->fetch();
            if ($row[0] != null) {
                $funds = $row[0];
            }
        }
    }
    catch (PDOException $e)
    {
    }

    $response["worth"] = $stock_worth + $funds;
}

$json = json_encode($response);
echo $json;