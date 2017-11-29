<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 29.11.2017
 * Time: 9.32
 */

require_once('config.php');
require_once('session.php');
require_once('status_response.php');

$response = new StatusResponse();

if (isset($_SESSION['logged_in'])) {
    if (isset($_POST['commented_id']) && isset($_POST['comment'])) {
        $reply_to = "NULL";

        // laita reply-to, jos tämä on vastaus toiseen kommenttiin
        if (isset($_POST['reply_to'])) {
            $reply_to = $_POST['reply_to'];
        }

        $user_id = $_SESSION['user_id'];
        $commented_id = $_POST['commented_id'];
        $comment = $_POST['comment'];

        // mikään ei saa olla null
        if ($commented_id != null && $comment != null) {
            if (postComment($DBH, $user_id, $commented_id, $reply_to, $comment)) {
                $response = new OKResponse("OK");
            } else {
                $response = new FailResponse("Kommentin lisääminen ei onnistunut");
            }
        } else {
            $response = new FailResponse("Jotain meni pieleen.");
        }

    } else {
        $response = new FailResponse("Parametrit väärin.");
    }
} else {
    $response = new FailResponse("Ei olla logattuna sisään.");
}

echo $response->getJSON();


function postComment($dbh, $user_id, $commented_id, $reply_to, $comment) {
    $result = $dbh->exec("INSERT INTO comment(user_id, commenter_id, parent_id, comment_text)VALUES('$commented_id', '$user_id', $reply_to, '$comment')");

    // virhe jos mikään rivi ei muuttunut
    if ($result === 0)
        return false;
    else
        return true;
}