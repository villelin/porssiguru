<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 30.11.2017
 * Time: 10.23
 */

require_once('config.php');
require_once('session.php');
require_once('status_response.php');

// enintään megan kuva
define('MAX_FILE_SIZE', '1000000');

// thumbnailin haluttu koko
define('THUMB_WIDTH', '100');
define('THUMB_HEIGHT', '100');

$response = new StatusResponse();

// pitää olla logattuna sisään
if (isset($_SESSION['logged_in'])) {
    if (isset($_POST["image"])) {
        $user_id = $_SESSION['user_id'];

        $data = $_POST["image"];

        $image_type = null;

        // profiilikuvan nimi on muotoa: "uploads/user_1.jpg"
        $target_file = "user_$user_id.jpg";
        $target_path = "../uploads/" . $target_file;

        // tunnistetaan kuvan tyyppi BASE64 datasta
        if (stripos($data, 'data:image/jpeg;base64,') !== false) {
            // kuva on jpeg
            $image_type = "jpeg";
            $data = str_replace('data:image/jpeg;base64,', '', $data);
            $data = str_replace(' ', '+', $data);
        } else if (stripos($data, 'data:image/png;base64,') !== false) {
            // kuva on png
            $image_type = "png";
            $data = str_replace('data:image/png;base64,', '', $data);
            $data = str_replace(' ', '+', $data);
        } else if (stripos($data, 'data:image/gif;base64,') != false) {
            // kuva on gif
            $image_type = "gif";
            $data = str_replace('data:image/gif;base64,', '', $data);
            $data = str_replace(' ', '+', $data);
        }

        if ($image_type !== null) {
            $data = base64_decode($data);

            // luodaan kuva
            $image = imagecreatefromstring($data);
            if ($image !== false) {
                $width = imagesx($image);
                $height = imagesy($image);

                // onko kuva jo olemassa? poistetaan
                if (file_exists($target_path)) {
                    error_log("Poistetaan $target_path");
                    unlink($target_path);
                }

                if ($width != THUMB_WIDTH || $height != THUMB_HEIGHT) {
                    $new_image = cropAndScaleImage($image);

                    // tallennetaan serverille
                    imagejpeg($new_image, $target_path, '90');

                    imagedestroy($new_image);
                } else {
                    // tallennetaan serverille
                    imagejpeg($image, $target_path, '90');
                }

                $response = new OKResponse("OK");

                // päivitetään tietokantaan
                updateDB($DBH, $target_file, $user_id);
            } else {
                $response = new FailResponse("Ei toimi");
            }

            imagedestroy($image);
        } else {
            $response = new FailResponse("Kuva ei ole JPG, PNG tai GIF.");
        }
    } else {
        $response = new FailResponse("Parametrit puuttuu.");
    }
} else {
    $response = new FailResponse("Ei olla logattuna sisään");
}

echo $response->getJSON();


function cropAndScaleImage($image) {
    $orig_width = imagesx($image);
    $orig_height = imagesy($image);

    // alkuperäisen kuvan koordinaatit
    $src_x = 0;
    $src_y = 0;
    $src_width = $orig_width;
    $src_height = $orig_height;

    // lopputuloksen koordinaatit
    $dst_x = 0;
    $dst_y = 0;
    $dst_width = THUMB_WIDTH;
    $dst_height = THUMB_HEIGHT;

    if ($orig_height >= $orig_width) {
        $src_y = ($src_height / 2) - ($src_width / 2);
        $src_height = $src_width;
    } else {
        $src_x = ($src_width / 2) - ($src_height / 2);
        $src_width = $src_height;
    }

    $new_image = imagecreatetruecolor($dst_width, $dst_height);

    // resamplataan uuteen kokoon
    imagecopyresampled($new_image, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_width, $dst_height, $src_width, $src_height);
    return $new_image;
}


function updateDB($dbh, $url, $user_id) {
    $insert_query = "UPDATE user_account SET image='$url' WHERE id='$user_id'";
    $sql = $dbh->prepare($insert_query);
    $sql->execute();
}