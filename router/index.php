<?php

include 'Auth.php';
include 'Discipline.php';

class Request {

    public string $path = "";
    public string $protocol = "";
    public string $method = "";
    public array $header = [];
    public array $query = [];
    public array $body = [];

    function __construct() {}

    static function Server(): Request {

        $request = new Request();

        $url = $_SERVER['REQUEST_URI'];
        $url = preg_replace('/^\//i', '', $url);

        $request->path = preg_replace('/\?.*$/i', '', $url);

        $request->method = $_SERVER['REQUEST_METHOD'];

        $request->protocol = $_SERVER['SERVER_PROTOCOL'];

        foreach($_SERVER as $key => $value) {
            if(preg_match("/^HTTP.*_/i",$key)) {
                $key = preg_replace('/^HTTP.*_/i', '', $key);
                $request->header[$key] = $value;
            }
        }

        $query = preg_replace('/^.*\?/i', '', $url);
        $query = preg_split ("/&/", $query);
        foreach($query as $item) {
            $key = preg_replace('/=.*$/i','',$item);
            $value = preg_replace('/^.*=/i','',$item);
            $request->query[$key] = $value;
        }
        
        $request->body = $_POST;

        return $request;
    }
}

class Router {

    function __construct() {

        $req = Request::Server();
        $res = new Request();

        $auth = new Auth($req, $res);
        $discipline = new Discipline($req, $res);
        
        // Для тестирования запросов
        // Console::logRequest();
        
        switch($req->path) {
            case "auth/login":
                $auth->makeLogin($req,$res);
                break;
            case "auth/register":
                $auth->makeRegister($req,$res);
                break;
            case "disciplines":
                $discipline->makeDisciplines($req,$res);
                break;
            default:
                $res->body["Start page"] = "page";
                break;
        }

        // foreach($res->header as $key=>$value) {
        //     header($key.":".$value);
        // }
        echo json_encode($res->body);
    }
}

class RouterModule {
    protected Request $req;
    protected Request $res;

    function __construct($req,&$res) {
        $this->req = $req;
        $this->res = $res;
    }
}
