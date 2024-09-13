<?php
require_once('partials/header.php');
require_once('config/Database.php');
require_once('models/Recepie.php');

$conn = Database::connect();

$recepie = new Recepie($conn);

function truncateText($text, $maxLength = 50)
{
  if (strlen($text) > $maxLength) {
    // Truncate the text without cutting words in half
    $text = substr($text, 0, $maxLength);
    // Find the last space in the truncated string
    $lastSpace = strrpos($text, ' ');
    // Truncate at the last space if possible
    return substr($text, 0, $lastSpace) . '...';
  } else {
    return $text;
  }
}

if (isset($_GET['search'])) {
  $value = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);
  $recepies = $recepie->search($value);
} else {
  $recepies = $recepie->getAll();
}

?>
<div class="header">
  <h1 class="title">Книга для вкуснейших рецептов</h1>
  <form class="search-box">
    <input type="text" name="search" id="search" placeholder="Найдите рецепт" class="search-input">
    <button class="search-btn" type="submit">Поиск</button>
  </form>
  <!-- <div class="links">
    <a href="#" class="active">Все рецепты</a>
    <a href="#" class="">Мои рецепты</a>
  </div> -->
</div>

<div class='add-btn'>
  <a href="create.php">Добавить рецепт</a>
</div>

<div class="recepies-box">
  <?php if (is_string($recepies)) : ?>
    <p>No recepies available</p>
  <?php else : ?>
    <?php foreach ($recepies as $recepie) : ?>
      <a href="recepie-show.php?id=<?= $recepie->id ?>">
        <article class="recepie">
          <div class="info">
            <h4 class="recepie__title"><?= $recepie->title ?></h4>
            <?php if (isset($recepie->ingredients)) : ?>
              <p class="recepie__ingredients"><?= truncateText($recepie->ingredients) ?></p>
            <?php endif; ?>
            <p class="recepie__description"><?= truncateText($recepie->instructions, 100) ?></p>
          </div>
          <?php if (isset($recepie->image)) : ?>
            <div class="image-box">
              <img src="<?= $recepie->image ?>" alt="food">
            </div>
          <?php endif; ?>
        </article>
      </a>
    <?php endforeach; ?>
  <?php endif; ?>
  <!-- Aricle 1
  <article class="recepie">
    <div class="info">
      <h4 class="recepie__title">Аджика</h4>
      <p class="recepie__ingredients">Острый перец, сахар, уксус</p>
      <p class="recepie__description">Вкуснейшая аджика</p>
    </div>
    <div class="image-box">
      <img src="assets/lily-banse--YHSwy6uqvk-unsplash.jpg" alt="food">
    </div>
  </article> -->
</div>
<?php require_once('partials/footer.php') ?>