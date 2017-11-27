<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 27.11.2017
 * Time: 10.32
 */

require_once('config.php');
require_once('session.php');

$response = array();

if (isset($_SESSION["logged_in"])) {

    $user_id = $_SESSION["user_id"];

    $query = "SELECT user_id, stock.id, stock.company, stock.price, buy_sum-sell_sum AS 'assets'
          FROM(
           SELECT user_id, stock_id, SUM(buy) AS buy_sum, SUM(sell) AS sell_sum
           FROM(
            (SELECT user_id, stock_id, amount AS 'buy', 0 AS 'sell'
            FROM stock_event
            WHERE transaction_type='Buy' AND user_id='$user_id')
            UNION
            (SELECT user_id, stock_id, 0 AS 'buy', amount AS 'sell'
            FROM stock_event
            WHERE transaction_type='Sell' AND user_id='$user_id')
           ) AS summed
           GROUP BY user_id, stock_id
          ) AS final, stock
          WHERE final.stock_id=stock.id";

    $sql = $DBH->prepare($query);
    $sql->execute();

    try {
        while ($row = $sql->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            $response[] = array("user_id" => $row[0], "stock_id" => $row[1], "company" => $row[2], "price" => $row[3], "assets" => $row[4]);
        }
    } catch (PDOException $e) {
        // TODO: palauta jotain?
    }
} else {
    // TODO: palauta jotain?
}

$json = json_encode($response);
echo $json;