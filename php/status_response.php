<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 28.11.2017
 * Time: 22.52
 */

class StatusResponse {
    protected $response;

    public function __construct($message = "") {
        $this->response = array();
        $this->response["message"] = $message;
    }

    public function getJSON() {
        return json_encode($this->response);
    }
}

class FailResponse extends StatusResponse {

    public function __construct($message) {
        parent::__construct($message);
        $response["error"] = true;
    }
}

class OKResponse extends StatusResponse {

    public function __construct($message) {
        parent::__construct($message);
        $response["error"] = false;
    }
}