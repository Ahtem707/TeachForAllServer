<?php

    function setupHeader() {
        error_reporting(-1);
        ini_set('display_errors',1);
        header('Content-Type: application/json;charset=utf-8');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
    }
?>