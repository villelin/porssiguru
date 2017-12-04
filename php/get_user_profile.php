<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 4.12.2017
 * Time: 12.19
 */

require_once('config.php');
require_once('session.php');
require_once('user_common.php');

$response = array();

if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];

    $user_rank = getUserRank($DBH, $user_id);
    $user_worth = getUserWorth($DBH, $user_id);
    $user_comments = getUserComments($DBH, $user_id);
    $user_info = getUserInfo($DBH, $user_id);
    $user_likes = countUserLikes($DBH, $user_id);
    $buy_history = getBuyHistory($DBH, $user_id);
    $sell_history = getSellHistory($DBH, $user_id);

    $response["rank"] = $user_rank;
    $response["worth"] = $user_worth;
    $response["username"] = $user_info["username"];
    $response["image"] = $user_info["image"];
    $response["description"] = $user_info["description"];
    $response["signup"] = $user_info["signup_date"];
    $response["likes"] = $user_likes;
    $response["comments"] = $user_comments;
    $response["buy_history"] = $buy_history;
    $response["sell_history"] = $sell_history;
}

$json = json_encode($response);
echo $json;