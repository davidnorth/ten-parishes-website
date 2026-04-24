<?php
$pageTitle = 'Events';
$artists = $db->query("
    SELECT artists.name, artists.slug, images.image_id
    FROM artists
    LEFT JOIN images ON images.artist_id = artists.id AND images.main = 1
    WHERE artists.type = 'special'
    ORDER BY artists.name
")->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Events</h1>
<?php foreach ($artists as $artist): ?>
<article>
  <?php if ($artist['image_id']): ?>
  <a href="/artists/<?= htmlspecialchars($artist['slug']) ?>">
    <img src="<?= cloudinary_url($artist['image_id'], 'w_200,h_200,c_fill') ?>" alt="<?= htmlspecialchars($artist['name']) ?>">
  </a>
  <?php endif ?>
  <a href="/artists/<?= htmlspecialchars($artist['slug']) ?>"><?= htmlspecialchars($artist['name']) ?></a>
</article>
<?php endforeach ?>
