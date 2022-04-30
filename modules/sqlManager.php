<?php
$sqlManager = new SqlManager();

class SqlManager {

  private $host = "server29.hosting.reg.ru";
  private $user = "u1615416_user";
  private $password = "user1234";
  private $database = "u1615416_teachforall";

  private $db;

  function __construct() {
    $this->firstConfigure();
  }

  # Первоначальная настройка
  private function firstConfigure() {
    /* Вы должны включить отчёт об ошибках для mysqli, прежде чем пытаться установить соединение */
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $this->db = new mysqli($this->host, $this->user, $this->password, $this->database);
    /* Установите желаемую кодировку после установления соединения, есть поддержка русского языка */
    $this->db->set_charset('utf8mb4');
  }

  function tokenCheck(string $token) {
    $requestResult = $this->db->query($queryString);
  }

  function request(string $queryString) {
    try {
      $requestResult = $this->db->query($queryString);
    } catch(Exception $e) {
      Console::error("SqlManager: Error: ".$e->getMessage());
      return new Result(null, $e);
    }
    $type = gettype($requestResult);
    if($type == "boolean") {
      return new Result([], null);
    }
    if($type == "object") {
      $result = [];
      
      if ($requestResult->num_rows > 0) {
        while($row = $requestResult->fetch_assoc()) {
          array_push($result, $row);
        }
      }
      return new Result($result, null);
    }
  }
}