<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 27.11.2017
 * Time: 10.08
 */

require_once('config.php');

function leaderboardCompare($a, $b) {
    if ($a["assets"] > $b["assets"]) {
        return -1;
    } else if($a["assets"] < $b["assets"]) {
        return 1;
    } else {
        return 0;
    }
}

$response = array();

$num = 0;
if (isset($_POST['num'])) {
    $num = $_POST['num'];
}

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

$sql = $DBH->prepare($query);
$sql->execute();

try {
    while ($row = $sql->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
        $user_id = $row[0];
        $assets = $row[1];

        $user_assets["$user_id"]["stock"] = $assets;
    }
}
catch (PDOException $e) {
}

// käyttäjien käteiset
$query = "SELECT id, username, funds FROM user_account";
$sql = $DBH->prepare($query);
$sql->execute();

$user_funds = array();

try {
    while ($row = $sql->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
        $user_id = $row[0];
        $username = $row[1];
        $funds = $row[2];

        $user_assets["$user_id"]["funds"] = $funds;
        $user_assets["$user_id"]["username"] = $username;
    }
}
catch (PDOException $e) {
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

    $leaderboard[] = array("user_id" => $user_id, "username" => $item["username"], "assets" => $assets);
}

// järjestetään assettien mukaan laskevasti
usort($leaderboard, "leaderboardCompare");

// jos haluttu määrä on määritelty, leikataan muut pois
if ($num != 0) {
    $leaderboard = array_slice($leaderboard, 0, $num);
}

$json = json_encode($leaderboard);
echo $json;