<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 29.11.2017
 * Time: 10.16
 */

require_once('config.php');
require_once('session.php');
require_once('user_common.php');

$response = array();

if (isset($_SESSION['logged_in'])) {
    // onko parametrina joku toinen käyttäjä?
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];

        $response = getUserComments($DBH, $user_id);
    } else {
        // muuten logatun käyttäjän kommentit
        $user_id = $_SESSION['user_id'];

        $response = getUserComments($DBH, $user_id);
    }
}

$json = json_encode($response);
echo $json;