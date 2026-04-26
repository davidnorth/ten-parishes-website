<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — Ten Parishes Festival' : 'Ten Parishes Festival' ?></title>
  <link rel="stylesheet" href="/style.css">
  <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/spotlight.js@0.7.8/dist/spotlight.bundle.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <a href="/" class="site-title">
Ten Parishes 
<span class="color-primary">
Festival
</span>

  </a>
    <nav>
      <a href="/parishes" <?= ($nav_current ?? '') === 'parishes' ? 'class="active"' : '' ?>>Parishes</a>
      <a href="/artists"  <?= ($nav_current ?? '') === 'artists'  ? 'class="active"' : '' ?>>Artists</a>
      <a href="/events"   <?= ($nav_current ?? '') === 'events'   ? 'class="active"' : '' ?>>Events</a>
      <a href="/about-us" <?= ($nav_current ?? '') === 'about'    ? 'class="active"' : '' ?>>About</a>
    </nav>
  </header>
  <main>
    <?= $content ?>
  </main>
  <footer>
    <p>&copy; Ten Parishes Festival</p>

    <p>
    The 10 Parishes Festival is operated by Wiveliscombe Area Partnership.
    Registered in England and Wales No. 4351175.
    Charity No. 1132983			
    </p>

  </footer>
</body>
</html>
