<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 25.11.2017
 * Time: 19.27
 */

require_once('config.php');
require_once('session.php');
require_once('user_common.php');

$response = array();

if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];

    $user_funds = getUserFunds($DBH, $user_id);

    $response["funds"] = $user_funds;

    $query = "SELECT id, symbol, company, category, price, variety FROM stock ORDER BY company";
    $sql = $DBH->prepare($query);
    $sql->execute();

    try {
        while ($row = $sql->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            $response["stock"][] = array("stock_id" => $row[0], "company" => $row[2], "price" => $row[4], "variety" => $row[5]);
        }
    } catch (PDOException $e) {
    }
}

$json = json_encode($response);
echo $json;