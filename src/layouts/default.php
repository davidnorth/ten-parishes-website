<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — 10 Parishes Festival' : '10 Parishes Festival' ?></title>
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
10 Parishes
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
    <p>&copy; 10 Parishes Festival</p>

    <p>
    The 10 Parishes Festival is operated by Wiveliscombe Area Partnership.
    Registered in England and Wales No. 4351175.
    Charity No. 1132983			
    </p>

    <div>
      <ul>
        <li><a href="/artists">Artists</a></li>
        <li><a href="/events">Events</a></li>
        <li><a href="/parishes">Parishes</a></li>
        <li><a href="/about-us">About</a></li>
        <li><a href="/contact">Contact</a></li>
</ul>

    </div>

    <div>
    <h4>Participating Parishes</h4>
    <ul>
        <li><a href="/parishes/ashbrittle">Ashbrittle</a></li>
        <li><a href="/parishes/bathealton">Bathealton</a></li>
        <li><a href="/parishes/brompton-ralph">Brompton Ralph</a></li>
        <li><a href="/parishes/chipstable">Chipstable</a></li>
        <li><a href="/parishes/clatworthy">Clatworthy</a></li>
        <li><a href="/parishes/fitzhead">Fitzhead</a></li>
        <li><a href="/parishes/huish-champflower">Huish Champflower</a></li>
        <li><a href="/parishes/milverton">Milverton</a></li>
        <li><a href="/parishes/stawley">Stawley</a></li>
        <li><a href="/parishes/wiveliscombe">Wiveliscombe</a></li>
      </ul>
    </p>
</div>

  </footer>
</body>
</html>
