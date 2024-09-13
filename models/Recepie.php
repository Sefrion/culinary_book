<?php

class Recepie
{
  public $title;
  public $ingredients;
  public $instructions;
  public $image;

  private $conn = null;

  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  public function getAll()
  {
    try {
      $stmt = $this->conn->prepare('SELECT * FROM recepies');
      $stmt->execute();
      $recepies = $stmt->fetchAll();
      if (count($recepies) > 0) {
        return $recepies;
      } else {
        return 'No recepies found';
      }
    } catch (PDOException $e) {
      echo 'Error: ' . $e->getMessage();
    }
  }

  public function create($title, $ingredients, $instructions, $image)
  {
    // Set variables
    $this->title = $title;
    $this->ingredients = $ingredients ?? null;
    $this->instructions = $instructions;
    $this->image = $image ?? null;

    // Create query
    $query = "INSERT INTO recepies(title, ingredients, instructions, image) VALUES (:title, :ingredients, :instructions, :image)";

    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':title', $title);
      $stmt->bindValue(':ingredients', $ingredients);
      $stmt->bindValue(':instructions', $instructions);
      $stmt->bindValue(':image', $image);
      $stmt->execute();
    } catch (PDOException $e) {
      echo 'Error: ' . $e->getMessage();
    }
  }

  public function show($id)
  {

    $query = "SELECT * FROM recepies WHERE id = :id";

    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':id', $id);
      $stmt->execute();
      return $stmt->fetch();
    } catch (PDOException $e) {
      echo 'Error: ' . $e->getMessage();
    }
  }

  public function update($id, $title, $ingredients, $instructions, $image)
  {
    // Set variables
    $this->title = $title;
    $this->ingredients = $ingredients ?? null;
    $this->instructions = $instructions;
    $this->image = $image ?? null;

    // Create query
    $query = "UPDATE recepies SET title = :title, ingredients = :ingredients, instructions = :instructions, image = :image WHERE id = :id";

    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':id', $id);
      $stmt->bindValue(':title', $title);
      $stmt->bindValue(':ingredients', $ingredients);
      $stmt->bindValue(':instructions', $instructions);
      $stmt->bindValue(':image', $image);
      $stmt->execute();
    } catch (PDOException $e) {
      echo 'Error: ' . $e->getMessage();
    }
  }

  public function delete($id, $image_path = null)
  {

    $query = "DELETE FROM recepies WHERE id = :id";

    if ($image_path !== null) {

      $dirname = dirname(__FILE__, 2);

      $file_path =  $dirname . $image_path;

      $directory_arr = explode('/', $image_path);

      $folder = $directory_arr[1] . '/' . $directory_arr[2];

      $folder_path =  $dirname . '/' . $folder;

      unlink($file_path);
      rmdir($folder_path);
    }

    try {
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':id', $id);
      $stmt->execute();
    } catch (PDOException $e) {
      echo 'Error: ' . $e->getMessage();
    }
  }

  public function search(string $value)
  {

    $value = '%' . $value . '%';

    $query = "SELECT * FROM recepies WHERE title LIKE :value OR ingredients LIKE :value OR instructions LIKE :value";

    try {
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(':value', $value);

      $stmt->execute();

      return $stmt->fetchAll();
    } catch (PDOException $e) {
      echo 'Error: ' . $e->getMessage();
    }
  }
}
