<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 27.11.2017
 * Time: 10.08
 */

require_once('config.php');
require_once('user_common.php');

$num = 0;
if (isset($_POST['num'])) {
    $num = $_POST['num'];
}

$leaderboard = getLeaderboard($DBH, $num);

$json = json_encode($leaderboard);
echo $json;