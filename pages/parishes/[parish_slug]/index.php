<?php
$parish = $db->get('parishes', '*', ['slug' => $req->params['parish_slug']]);

if (!$parish) {
    http_response_code(404);
    return;
}

$artists = $db->query("
    SELECT artists.name, artists.slug
    FROM artists
    JOIN venues ON artists.venue_id = venues.id
    WHERE venues.parish_id = " . (int) $parish['id'] . "
    ORDER BY artists.name
")->fetchAll(PDO::FETCH_ASSOC);
?>
<h1><?= htmlspecialchars($parish['name']) ?></h1>
<?php if ($parish['picture_id']): ?>
<img src="<?= cloudinary_url($parish['picture_id'], 'w_800,c_limit') ?>" alt="<?= htmlspecialchars($parish['name']) ?>">
<?php endif ?>
<?php if ($artists): ?>
<ul>
  <?php foreach ($artists as $artist): ?>
  <li><a href="/artists/<?= htmlspecialchars($artist['slug']) ?>"><?= htmlspecialchars($artist['name']) ?></a></li>
  <?php endforeach ?>
</ul>
<?php endif ?>
