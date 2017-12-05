<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 29.11.2017
 * Time: 15.14
 */

require_once('config.php');
require_once('session.php');
require_once('status_response.php');

$response = new StatusResponse();

if (isset($_SESSION['logged_in'])) {
    if (isset($_POST['liked_id'])) {

        $liked_id = $_POST['liked_id'];
        $user_id = $_SESSION['user_id'];

        // käyttäjien pitää olla valideja
        if ($user_id != null && $liked_id != null) {
            // ei tykätä itsestä
            $result = $DBH->exec("INSERT INTO user_like(user_id, liked_id)VALUES('$user_id', '$liked_id')");

            // ei muuttuneita rivejä -> joku meni pieleen tai jo tykätty tästä henkilöstä
            if ($result === 0) {
                $response = new FailResponse("Tykkääminen ei onnistu");
            } else {
                $response = new OKResponse("OK");
            }
        } else {
            $response = new FailResponse("Jotain meni pieleen.");
        }
    } else {
        $response = new FailResponse("Parametrit väärin.");
    }
}

echo $response->getJSON();