<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 27.11.2017
 * Time: 10.08
 */

require_once('config.php');

$response = array();

$query = "SELECT user_id, username, SUM(buy_sum-sell_sum) AS 'assets'
          FROM(
           SELECT user_id, stock_id, SUM(buy) AS buy_sum, SUM(sell) AS sell_sum
           FROM(
           (SELECT user_id, stock_id, amount*s.price  AS 'buy', 0 AS 'sell'
            FROM stock_event, stock AS s
            WHERE transaction_type='Buy' AND s.id=stock_id)
            UNION ALL
           (SELECT user_id, stock_id, 0 AS 'buy', amount*s.price AS 'sell'
            FROM stock_event, stock AS s
            WHERE transaction_type='Sell' AND s.id=stock_id)
           ) AS summed
           GROUP BY user_id, stock_id
          ) AS final, stock, user_account AS u
          WHERE final.stock_id=stock.id AND user_id=u.id
          GROUP BY user_id
          ORDER BY assets DESC";

if (isset($_POST["num"])) {
    $num = $_POST["num"];
    $query .= " LIMIT $num";
}

$sql = $DBH->prepare($query);
$sql->execute();

try
{
    while ($row = $sql->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
        $response[] = array("user_id" => $row[0], "username" => $row[1], "assets" => $row[2]);
    }
}
catch (PDOException $e)
{
    $response[] = "";
}

$json = json_encode($response);
echo $json;