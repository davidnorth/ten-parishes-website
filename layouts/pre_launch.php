<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — Ten Parishes Festival' : 'Ten Parishes Festival' ?></title>
  <link rel="stylesheet" href="/style.css">
  <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
</head>

<body>

<header>
  <a href="/" class="site-title">Ten Parishes 
  <span class="color-primary"> Festival </span> </a>
  <nav>
  </nav>
</header>

<main>
  <?= $content ?>
</main>

<footer>
  <div>
    <div class="brand-foot">Ten Parishes <em>Festival</em></div>
    <p>An open‑studios festival across ten parishes of West Somerset. Organised by the Wiveliscombe Area Partnership, charity 1132983.</p>
  </div>
  <div>
    <div class="col-label">Contact</div>
    <ul>
      <li><a href="mailto:hello@tenparishes.org">hello@tenparishes.org</a></li>
      <li><a href="#">Volunteer with us</a></li>
      <li><a href="#">Press &amp; partnerships</a></li>
    </ul>
  </div>
  <div>
    <div class="col-label">Festival</div>
    <ul>
      <li><a href="#">Previous editions</a></li>
      <li><a href="#">Sponsors</a></li>
      <li><a href="#">Privacy</a></li>
    </ul>
  </div>
  <div class="legal">
    <div>© 2026 Ten Parishes Festival · Wiveliscombe Area Partnership</div>
    <div>Site goes fully live · May 2026</div>
  </div>
</footer>

</body>
