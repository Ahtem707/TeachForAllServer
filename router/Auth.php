<?php

class Auth extends RouterModule {
    function makeLogin() {
        try {
            if($this->req->body["email"] ?? false) {
                $email = $this->req->body["email"];
                $login = "email='$email'";
            } else if($this->req->body["phone"] ?? false) {
                $phone = $this->req->body["phone"];
                $login = "phone='$phone'";
            } else {
                throw new Exception("No email or phone");
            }
            $password = $this->req->body["password"] ?? throw new Exception("No password");
        } catch(Exception $e) {
            Console::error("Router: Error: ".$e->getMessage());
            $this->res->body = Response::failure($e);
            return;
        }
        $reqString = "SELECT userId, avatar, surname, name, middlename, phone, email, position
                      FROM Users
                      WHERE $login AND password='$password'";
        $result = SqlManager::$share->request($reqString);
        try {
            if($result->error ?? false) { throw $result->error; }
            $data = $result->data ?? throw new Exception("Not data");
            $user = $data[0] ?? throw new Exception("User not found");
            $userId = $user["userId"] ?? throw new Exception("User userId not found");
            $avatar = $user["avatar"] ?? "";
            $surname = $user["surname"] ?? throw new Exception("User surname not found");
            $name = $user["name"] ?? throw new Exception("User name not found");
            $middlename = $user["middlename"] ?? "";
            $phone = $user["phone"] ?? throw new Exception("User phone not found");
            $email = $user["email"] ?? "";
            $position = $user["position"] ?? "";
        } catch(Exception $e) {
            Console::error("Router: Error: ".$e->getMessage());
            $this->res->body = Response::failure($e);
            return;
        }
        $tokenId = tokenGenerator();
        $date = nowDate();
        $reqString = "UPDATE Users
                      SET tokenId='$tokenId', tokenCreateDate='$date'
                      WHERE userId='$userId'";
        $result = SqlManager::$share->request($reqString);
        if($result->error ?? false) {
            $this->res->body = Response::failure($result->error);
        } else {
            $data = [];
            $data["userId"] = $userId;
            $data["avatar"] = $avatar;
            $data["surname"] = $surname;
            $data["name"] = $name;
            $data["middlename"] = $middlename;
            $data["phone"] = $phone;
            $data["email"] = $email;
            $data["position"] = $position;
            $data["tokenId"] = $tokenId;
            $this->res->body = Response::success($data);
        }
    }

    function makeRegister() {
        try {
            $body = $this->req->body;
            $name = $body["name"] ?? throw new Exception("User name not found");
            $surname = $body["surname"] ?? throw new Exception("User surname not found");
            $middlename = $body["middlename"] ?? throw new Exception("User middlename not found");
            $phone = $body["phone"] ?? throw new Exception("User phone not found");
            $position = $body["position"] ?? throw new Exception("User position not found");
            $password = $body["password"] ?? throw new Exception("User password not found");
        } catch(Exception $e) {
            Console::error("Router: Error: ".$e->getMessage());
            $this->res->body = Response::failure($e);
            return;
        }
        $reqString = "INSERT INTO Users(name, surname, middlename, phone, position, password)
                      VALUES('$name','$surname','$middlename','$phone','$position','$password')";
        $result = SqlManager::$share->request($reqString);
        if($result->error ?? false) {
            $this->res->body = Response::failure($result->error);
        } else {
            $this->res->body = Response::success($result->data);
        }
    }
}