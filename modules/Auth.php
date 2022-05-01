<?php

class Auth {
    function login() {
        
    }

    function getWords() {
        $queryString = "SELECT * FROM Words;";
        return $this->request($queryString);
    }

    function addWord($word, $translate = "",$description = "") {
        if($translate != "") {
            $queryString = "INSERT INTO Words('word','translate') VALUE('${word}','${translate}')";
        } else if($translate != "" && $description != "") {
            $queryString = "INSERT INTO Words('word','translate','description') VALUE('${word}','${translate}','${description}')";
        } else {
            $queryString = "INSERT INTO Words(word) VALUES('${word}')";
        }
        return $this->request($queryString);
    }
}