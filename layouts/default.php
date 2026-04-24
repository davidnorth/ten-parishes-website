<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — Ten Parishes Festival' : 'Ten Parishes Festival' ?></title>
  <link rel="stylesheet" href="/style.css">
</head>
<body>
  <header>
    <a href="/" class="site-title">Ten Parishes Festival</a>
    <nav>
      <a href="/parishes">Parishes</a>
      <a href="/artists">Artists</a>
      <a href="/events">Events</a>
      <a href="/about-us">About</a>
    </nav>
  </header>
  <main>
    <?= $content ?>
  </main>
  <footer>
    <p>&copy; Ten Parishes Festival</p>
  </footer>
</body>
</html>
