<?php

include 'Auth.php';

class Request {

    public string $path = "";
    public string $protocol = "";
    public string $method = "";
    public array $header = [];
    public array $query = [];
    public array $body = [];

    function __construct() {
        $this->header["Content-Type"] = "application/json;charset=utf-8";
    }

    static function Server() {
        $self = new Request();

        $url = $_SERVER['REQUEST_URI'];
        $url = preg_replace('/^\//i', '', $url);

        $self->path = preg_replace('/\?.*$/i', '', $url);

        $self->method = $_SERVER['REQUEST_METHOD'];

        $self->protocol = $_SERVER['SERVER_PROTOCOL'];

        foreach($_SERVER as $key => $value) {
            if(preg_match("/^HTTP.*_/i",$key)) {
                $key = preg_replace('/^HTTP.*_/i', '', $key);
                $self->header[$key] = $value;
            }
        }

        $query = preg_replace('/^.*\?/i', '', $url);
        $query = preg_split ("/&/", $query);
        foreach($query as $item) {
            $key = preg_replace('/=.*$/i','',$item);
            $value = preg_replace('/^.*=/i','',$item);
            $self->query[$key] = $value;
        }
        
        $self->body = $_POST;

        return $self;
    }

    function toString() {
        Console::log($this->body);
        return $this->path;
    }
}

class Router {
    private $router = null;

    function __construct() {
        $this->router = new RouterFactory();

        $req = Request::Server();
        $res = new Request();

        switch($req->path) {
            case "auth/login":
                $this->router->makeLogin($req,$res);
                break;
            case "auth/register":
                $this->router->makeRegister($req,$res);
                break;
        }

        foreach($res->header as $key=>$value) {
            header($key.":".$value);
        }
        echo json_encode($res->body);
    }
}

class RouterFactory {
    private $sqlManager;

    function __construct() {
        $this->sqlManager = new SqlManager();
    }

    function makeLogin($req, &$res) {
        try {
            if($req->body["email"] ?? false) {
                $email = $req->body["email"];
                $login = "email='$email'";
            } else if($req->body["phone"] ?? false) {
                $phone = $req->body["phone"];
                $login = "phone='$phone'";
            } else {
                throw new Exception("No email or phone");
            }
            $password = $req->body["password"] ?? throw new Exception("No password");
        } catch(Exception $e) {
            Console::error("Router: Error: ".$e->getMessage());
            $res->body = Response::failure($e);
            return;
        }
        $reqString = "SELECT userId FROM Users
                      WHERE $login AND password='$password'";
        $result = $this->sqlManager->request($reqString);
        try {
            if($result->error ?? false) { throw $result->error; }
            $data = $result->data ?? throw new Exception("Not data");
            $result = $data[0] ?? throw new Exception("User not found");
            $userId = $result["userId"] ?? throw new Exception("User not found");
        } catch(Exception $e) {
            Console::error("Router: Error: ".$e->getMessage());
            $res->body = Response::failure($e);
            return;
        }
        $tokenId = tokenGenerator();
        $date = nowDate();
        $reqString = "UPDATE Users
                      SET tokenId='$tokenId', tokenCreateDate='$date'
                      WHERE userId='$userId'";
        $result = $this->sqlManager->request($reqString);
        if($result->error ?? false) {
            $res->body = Response::failure($result->error);
        } else {
            $data = array("tokenId"=>$tokenId);
            $res->body = Response::success($data);
        }
    }

    function makeRegister($req, &$res) {
        try {
            $body = $req->body;
            $name = $body["name"] ?? throw new Exception("User name not found");
            $surname = $body["surname"] ?? throw new Exception("User surname not found");
            $middlename = $body["middlename"] ?? throw new Exception("User middlename not found");
            $phone = $body["phone"] ?? throw new Exception("User phone not found");
            $position = $body["position"] ?? throw new Exception("User position not found");
            $password = $body["password"] ?? throw new Exception("User password not found");
        } catch(Exception $e) {
            Console::error("Router: Error: ".$e->getMessage());
            $res->body = Response::failure($e);
            return;
        }
        $reqString = "INSERT INTO Users(name, surname, middlename, phone, position, password)
                      VALUES('$name','$surname','$middlename','$phone','$position','$password')";
        $result = $this->sqlManager->request($reqString);
        if($result->error ?? false) {
            $res->body = Response::failure($result->error);
        } else {
            $res->body = Response::success($result->data);
        }
    }
}