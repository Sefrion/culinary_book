<?php
require('partials/header.php');
require_once('config/Database.php');
require_once('models/Recepie.php');

$conn = Database::connect();

$recepie = new Recepie($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Check if the title input is empty
  if (empty($_POST['title'])) {
    $title_error = 'Название блюда обязательно';
  }

  // Check if the instructions input is empty
  if (empty($_POST['instructions'])) {
    $instructions_error = 'Инструкция как готовить обязательна';
  }

  // If there are no errors, process the form (this part can be expanded as needed)
  if (empty($title_error) && empty($instructions_error)) {
    // Set data from input to variables and sanitize them
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
    $ingredients = filter_input(INPUT_POST, 'ingredients', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
    $instructions = filter_input(INPUT_POST, 'instructions', FILTER_SANITIZE_SPECIAL_CHARS);
    $image;

    // If passed, getting image and saving it
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
      // Get the image details
      $imageTmpPath = $_FILES['image']['tmp_name'];
      $imageName = $_FILES['image']['name'];
      $imageType = $_FILES['image']['type'];

      // if ($imageType != 'image/jpeg' || $imageType != 'image/png') {
      //   return;
      // }

      $randomString = generateRandomString();
      $randomString2 = generateRandomString(5);

      $directory = __DIR__ . '/images/' . $randomString2;

      if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
      }

      $image_name = $randomString . basename($imageName);

      $destination = $directory . '/' . $image_name;

      move_uploaded_file($imageTmpPath, $destination);

      $image = '/images/' . $randomString2 . '/' . $image_name;
    }

    $recepie->create($title, $ingredients, $instructions, $image);
    header('Location: /');
  }
};

function generateRandomString($length = 10)
{
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  return substr(str_shuffle($characters), 0, $length);
}
?>
<div class="add-btn">
  <a href="/">Назад к рецептам</a>
</div>
<form method="POST" class="add-form" enctype="multipart/form-data">
  <h2 class="title">Добавьте свой рецепт</h2>
  <div>
    <label for="title">Название блюда</label>
    <?php if (isset($title_error)) : ?>
      <span style="color: red;"><?= $title_error ?></span>
    <?php endif; ?>
    <input type="text" name="title" id="title">
  </div>
  <div>
    <label for="ingredients">Введите ингредиенты через запятую</label>
    <input type="text" name="ingredients" id="ingredients">
  </div>
  <div>
    <label for="instructions">Опишите как готовить</label>
    <?php if (isset($instructions_error)) : ?>
      <span style="color: red;"><?= $instructions_error ?></span>
    <?php endif; ?>
    <textarea name="instructions" id="instructions"></textarea>
  </div>
  <div>
    <label for="image">Изображение блюда</label>
    <input type="file" name="image" id="image"></input>
  </div>
  <button type="submit">Добавить рецепт</button>
</form>
<?php require('partials/footer.php') ?>