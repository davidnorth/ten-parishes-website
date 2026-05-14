<?php
$pageTitle = 'Artists';
$artists = $db->query("
    SELECT artists.name, artists.slug, images.image_id
    FROM artists
    LEFT JOIN images ON images.artist_id = artists.id AND images.main = 1
    WHERE artists.approved = 1
    ORDER BY artists.name
")->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="page-grid">
<section>


<h1>Artists</h1>


<nav class="icon-tabs">
  <a href="/artists" class="current">
    <iconify-icon icon="ph:list" class="icon-medium" aria-hidden="true"> </iconify-icon>
    <span>List view</span>
  </a>
  <a href="/map">
    <iconify-icon icon="ph:map-pin" class="icon-medium" aria-hidden="true" /></iconify-icon>
    <span>Map view</span>
  </a>
</nav>

<div class="image-grid">
<?php foreach ($artists as $artist): ?>
<article>
  <?php if ($artist['image_id']): ?>
  <a href="/artists/<?= htmlspecialchars($artist['slug']) ?>">
    <?= cloudinary_image($artist['image_id'], 200, 200, $artist['name']) ?>
  </a>
  <?php endif ?>
  <a href="/artists/<?= htmlspecialchars($artist['slug']) ?>"><?= htmlspecialchars($artist['name']) ?></a>
</article>
<?php endforeach ?>
</div>


</section>
</div>





