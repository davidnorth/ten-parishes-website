<?php
function ordinal(int $n): string {
    $s = ['th', 'st', 'nd', 'rd'];
    $v = $n % 100;
    return $n . ($s[($v - 20) % 10] ?? $s[$v] ?? $s[0]);
}

$artist = $db->get('artists', '*', ['slug' => $req->params['artist_slug']]);

if (!$artist || !$artist['approved']) {
    http_response_code(404);
    return;
}

$eventDates = $db->select('event_dates', '*', [
    'artist_id' => $artist['id'],
    'ORDER'     => ['date', 'from_time'],
]);

$images = $db->select('images', '*', ['artist_id' => $artist['id']]);

$otherArtists = $artist['venue_id'] ? $db->query("
    SELECT a2.name, a2.slug
    FROM artists a2
    JOIN venues v2 ON a2.venue_id = v2.id
    WHERE v2.parish_id = (
        SELECT parish_id FROM venues WHERE id = " . (int) $artist['venue_id'] . "
    )
    AND a2.id != " . (int) $artist['id'] . "
    AND a2.approved = 1
    ORDER BY a2.name
")->fetchAll(PDO::FETCH_ASSOC) : [];
?>
<h1><?= htmlspecialchars($artist['name']) ?></h1>
<?php if ($artist['body_html']): ?>
<?= $artist['body_html'] ?>
<?php endif ?>
<?php if ($eventDates): ?>
<section aria-labelledby="dates-heading">
  <h2 id="dates-heading">Dates</h2>
  <ul>
    <?php foreach ($eventDates as $ed):
      $dt = new DateTime($ed['date']);
      $label = $dt->format('l') . ' ' . ordinal((int) $dt->format('j'));
      $from = $ed['from_time'] ? substr($ed['from_time'], 0, 5) : '';
      $to   = $ed['to_time']   ? substr($ed['to_time'],   0, 5) : '';
    ?>
    <li>
      <?= $label ?>
      <?php if ($from): ?><br><?= $from ?><?= $to ? '–' . $to : '' ?><?php endif ?>
    </li>
    <?php endforeach ?>
  </ul>
</section>
<?php endif ?>
<?php if ($images): ?>
<section aria-labelledby="gallery-heading">
  <h2 id="gallery-heading">Featured Artworks</h2>
  <div class="gallery-grid">
    <?php foreach ($images as $img): ?>
    <figure>
      <img src="<?= cloudinary_url($img['image_id'], 'w_400,h_400,c_fill') ?>" alt="<?= htmlspecialchars($img['name'] ?? '') ?>" loading="lazy">
      <?php if ($img['name']): ?><figcaption><?= htmlspecialchars($img['name']) ?></figcaption><?php endif ?>
    </figure>
    <?php endforeach ?>
  </div>
</section>
<?php endif ?>
<?php if ($otherArtists): ?>
<h2>Other artists in this parish</h2>
<ul>
  <?php foreach ($otherArtists as $other): ?>
  <li><a href="/artists/<?= htmlspecialchars($other['slug']) ?>"><?= htmlspecialchars($other['name']) ?></a></li>
  <?php endforeach ?>
</ul>
<?php endif ?>
