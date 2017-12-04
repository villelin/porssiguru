<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 4.12.2017
 * Time: 13.18
 */

require_once('config.php');
require_once('session.php');
require_once('user_common.php');

$response = array();

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $user_rank = getUserRank($DBH, $user_id);
    $user_worth = getUserWorth($DBH, $user_id);
    $user_comments = getUserComments($DBH, $user_id);
    $user_info = getUserInfo($DBH, $user_id);
    $user_likes = countUserLikes($DBH, $user_id);

    $is_liked = false;
    if (isset($_SESSION['logged_in'])) {
        $logged_id = $_SESSION['user_id'];
        $is_liked = doesUserLikeUser($DBH, $logged_id, $user_id);
    }

    $response["rank"] = $user_rank;
    $response["worth"] = $user_worth;
    $response["username"] = $user_info["username"];
    $response["image"] = $user_info["image"];
    $response["description"] = $user_info["description"];
    $response["signup"] = $user_info["signup_date"];
    $response["likes"] = $user_likes;
    $response["is_liked"] = $is_liked;
    $response["comments"] = $user_comments;
}

$json = json_encode($response);
echo $json;