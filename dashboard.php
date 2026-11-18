<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
require __DIR__ . '/catalog.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$sections = [];
foreach ($books as $book) {
    $sections[$book['section']][] = $book;
}
$activeNav = 'home';
?>
<!DOCTYPE html>
<html lang="en" <?= theme_attrs() ?>>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app">
  <header class="app-header">
    <h2>Home</h2>
    <div class="app-header-actions">
      <button type="button" class="icon-btn" aria-label="Help">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.7.3-1 .9-1 1.7"/><line x1="12" y1="17" x2="12" y2="17.01"/></svg>
      </button>
      <details class="menu">
        <summary class="icon-btn" aria-label="More options">
          <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>
        </summary>
        <div class="menu-panel">
          <p class="menu-user"><?= e($_SESSION['user_name']) ?></p>
          <a href="edit_profile.php" class="menu-item menu-link">Edit profile</a>
          <a href="theme.php" class="menu-item menu-link">Appearance</a>
          <form method="post" action="logout.php">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <button type="submit" class="menu-item">Sign out</button>
          </form>
        </div>
      </details>
    </div>
  </header>

  <main class="app-main">
    <?php foreach ($sections as $title => $sectionBooks): ?>
      <section class="shelf">
        <div class="shelf-head">
          <h3><?= e($title) ?></h3>
          <a href="search.php" class="more">more <span aria-hidden="true">&#8599;</span></a>
        </div>
        <div class="shelf-rule"></div>
        <div class="books">
          <?php foreach ($sectionBooks as $book): ?>
            <a href="#" class="book">
              <div class="cover" style="background: linear-gradient(160deg, <?= e($book['color']) ?>, #0b1d2b);">
                <span><?= e($book['label']) ?></span>
              </div>
              <p><?= e($book['title']) ?></p>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </main>

  <?php require __DIR__ . '/partials/bottom_nav.php'; ?>
</div>
</body>
</html>
