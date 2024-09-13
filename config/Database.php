<?php

class Database
{

  public static function connect()
  {
    try {
      $dbPath = __DIR__ . '/database.sqlite';
      $conn = new PDO('sqlite:' . $dbPath);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
      return $conn;
    } catch (PDOException $e) {
      echo 'Error occured: ' . $e->getMessage();
      return null;
    }
  }
}
