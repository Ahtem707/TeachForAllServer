<?php

class Discipline extends RouterModule {

    function makeDisciplines() {
        switch($this->req->method) {
            case RequestMethod::$GET:
                $this->get();
                break;
            case RequestMethod::$POST:
                $this->add();
                break;
        }
    }

    private function get() {
        try {
            $query = $this->req->query;
            $limit = $query["limit"] ?? null;
            $limitString = $limit ? "Limit $limit" : "";
        } catch(Exception $e) {
            Console::error("Router: Error: ".$e->getMessage());
            $this->res->body = Response::failure($e);
            return;
        }

        $reqString = "SELECT d.disciplineId, d.logo, d.fullName, d.shortName, u.name as author, d.numberOfHour, d.format
                      FROM Discipline as d, Users as u
                      WHERE d.authorId = u.userId
                      $limitString";
        $result = SqlManager::$share->request($reqString);
        if($result->error ?? false) {
            $this->res->body = Response::failure($result->error);
        } else {
            $this->res->body = Response::success($result->data);
        }
    }

    private function add() {
        try {
            $body = $this->req->body;
            $logo = $body['logo'] ?? null;
            $fullName = $body['fullName'] ?? throw new Exception("Discipline 'fullName' not found");
            $shortName = $body['shortName'] ?? throw new Exception("Discipline 'shortName' not found");
            $authorId = $body['authorId'] ?? throw new Exception("Discipline 'authorId' not found");
            $numberOfHour = $body['numberOfHour'] ?? null;
            $format = $body['format'] ?? null;

            $logo = toValue($logo);
            $fullName = toValue($fullName);
            $shortName = toValue($shortName);
            $authorId = toValue($authorId);
            $numberOfHour = toValue($numberOfHour);
            $format = toValue($format);
        } catch(Exception $e) {
            Console::error("Router: Error: ".$e->getMessage());
            $this->res->body = Response::failure($e);
            return;
        }

        $reqString = "INSERT INTO
                        Discipline(logo, fullName, shortName, authorId, numberOfHour, format)
                      VALUE($logo,$fullName,$shortName,$authorId,$numberOfHour,$format)";
        $result = SqlManager::$share->request($reqString);
        if($result->error ?? false) {
            $this->res->body = Response::failure($result->error);
        } else {
            $this->res->body = Response::success($result->data);
        }
    }
}