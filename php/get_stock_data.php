<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 25.11.2017
 * Time: 19.27
 */

require_once('config.php');

$response = array();

$query = "SELECT symbol, company, category, price, variety FROM stock ORDER BY category, symbol";
$sql = $DBH->prepare($query);
$sql->execute();

try
{
    while ($row = $sql->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
        $response[] = array("symbol" => $row[0], "company" => $row[1], "price" => $row[3], "variety" => $row[4], "category" => $row[2]);
    }
}
catch (PDOException $e)
{
    $response[] = "";
}

$json = json_encode($response);
echo $json;