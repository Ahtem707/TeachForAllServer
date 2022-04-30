<?php

class Status {
    public static $success = "SUCCESS";
    public static $failure = "FAILURE";
}

class Response {

    static function success(array $data) {
        $response = [];
        $response["status"] = Status::$success;
        $response["message"] = "";
        $response["data"] = $data;
        return $response;
    }

    static function failure(Exception $e) {
        $response = [];
        $response["status"] = Status::$failure;
        $response["message"] = $e->getMessage();
        $response["data"] = [];
        return $response;
    }
}

class Result {
    public $data;
    public ?Exception $error;

    function __construct($data, ?Exception $error) {
        $this->data = $data;
        $this->error = $error;
    }
}