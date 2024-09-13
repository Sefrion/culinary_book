<?php
require('partials/header.php');
require_once('config/Database.php');
require_once('models/Recepie.php');

$conn = Database::connect();

$recepie = new Recepie($conn);

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

$recepie_item = $recepie->show($id);

$ingredients_arr = !empty($recepie_item->ingredients) ? explode(',', $recepie_item->ingredients) : [];

if (isset($_POST['_method']) && $_POST['_method'] === 'DELETE') {
  $recepie->delete($id, $recepie_item->image);
  header('Location: /');
};
?>
<div class="add-btn">
  <a href="/">Назад к рецептам</a>
</div>
<div class="page">
  <div class="box-image">
    <?php if (!empty($recepie_item->image)) : ?>
      <img src="<?= $recepie_item->image; ?>" alt="food">
    <?php else : ?>
      <!-- <img src="https://placehold.co/400" alt="No image available"> -->
      <p></p>
    <?php endif; ?>
  </div>
  <div class="info-box">
    <div class="actions">
      <div>
        <a href="recepie-update.php?id=<?= $recepie_item->id ?>" class="btn-update">Изменить рецепт</a>
      </div>
      <form method="POST">
        <input type="hidden" name="_method" value="DELETE">
        <button class="btn" type="submit">Удалить рецепт</button>
      </form>
    </div>
    <h2><?= $recepie_item->title; ?></h2>
    <?php if (!empty($ingredients_arr)) : ?>
      <ol>
        <?php foreach ($ingredients_arr as $ingredient) : ?>
          <li><?= ucfirst(trim($ingredient)) ?></li>
        <?php endforeach; ?>
      </ol>
    <?php else : ?>
      <p style="text-align: center; margin-bottom: 1rem;">Нет списка ингредиентов</p>
    <?php endif; ?>
    <p><?= $recepie_item->instructions; ?></p>
  </div>
</div>
<?php require('partials/footer.php') ?>