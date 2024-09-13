<?php
require('partials/header.php');
require_once('config/Database.php');
require_once('models/Recepie.php');

$conn = Database::connect();

$recepie = new Recepie($conn);

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

$recepie_item = $recepie->show($id);

if (isset($_POST['_method'])  && $_POST['_method'] === 'PUT') {
  // Check if the title input is empty
  if (empty($_POST['title'])) {
    $title_error = 'Название блюда обязательно';
  }

  // Check if the instructions input is empty
  if (empty($_POST['instructions'])) {
    $instructions_error = 'Инструкция как готовить обязательна';
  }

  if (empty($title_error) && empty($instructions_error)) {
    $id = $recepie_item->id;
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
    $ingredients = filter_input(INPUT_POST, 'ingredients', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
    $instructions = filter_input(INPUT_POST, 'instructions', FILTER_SANITIZE_SPECIAL_CHARS);
    $image = $recepie_item->image;

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

      // Create new directory and put an image there

      $directory = __DIR__ . '/images/' . $randomString2;

      if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
      }

      $image_name = $randomString . basename($imageName);

      $destination = $directory . '/' . $image_name;

      move_uploaded_file($imageTmpPath, $destination);

      $image = '/images/' . $randomString2 . '/' . $image_name;

      // Delete previous image
      if ($recepie_item->image) {
        $image_path = $recepie_item->image;

        $dirname = __DIR__;

        $file_path =  $dirname . $image_path;

        $directory_arr = explode('/', $image_path);

        $folder = $directory_arr[1] . '/' . $directory_arr[2];

        $folder_path =  $dirname . '/' . $folder;

        unlink($file_path);
        rmdir($folder_path);
      }
    }

    $recepie->update($id, $title, $ingredients, $instructions, $image);
    header('Location: /');
  }
}



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
  <div class="box-image-update">
    <?php if (!empty($recepie_item->image)) : ?>
      <img src="<?= $recepie_item->image; ?>" alt="food">
    <?php else : ?>
      <!-- <img src="https://placehold.co/400" alt="No image available"> -->
      <p></p>
    <?php endif; ?>
  </div>
  <h2 class="title">Измените рецепт <?= $recepie_item->title ?></h2>
  <div>
    <label for="title">Название блюда</label>
    <?php if (isset($title_error)) : ?>
      <span style="color: red;"><?= $title_error ?></span>
    <?php endif; ?>
    <input type="text" name="title" id="title" value="<?= $recepie_item->title ?>">
  </div>
  <div>
    <label for="ingredients">Введите ингредиенты через запятую</label>
    <input type="text" name="ingredients" id="ingredients" value="<?= $recepie_item->ingredients ?>">
  </div>
  <div>
    <label for="instructions">Опишите как готовить</label>
    <?php if (isset($instructions_error)) : ?>
      <span style="color: red;"><?= $instructions_error ?></span>
    <?php endif; ?>
    <textarea name="instructions" id="instructions"><?= $recepie_item->instructions ?></textarea>
  </div>
  <div>
    <label for="image">Изображение блюда</label>
    <input type="file" name="image" id="image"></input>
  </div>
  <input type="hidden" name="_method" value="PUT">
  <button type="submit">Изменить рецепт</button>
</form>
<?php require('partials/footer.php') ?>