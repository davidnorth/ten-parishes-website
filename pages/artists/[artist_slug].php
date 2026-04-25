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

$pageTitle = $artist['name'];

$eventDates = $db->select('event_dates', '*', [
    'artist_id' => $artist['id'],
    'ORDER'     => ['date', 'from_time'],
]);

$images = $db->select('images', '*', ['artist_id' => $artist['id']]);

$venue = $db->get('venues', '*', ['id' => $artist['venue_id']]);

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

<div class="page-grid">

<section aria-labelledby="name-heading">
  <h1 id="name-heading"><?= htmlspecialchars($artist['name']) ?></h1>
  <?= $artist['body_html'] ?>
</section>

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

<section aria-labelledby="gallery-heading">
  <h2 id="gallery-heading">Featured Artworks</h2>
  <?php require __DIR__ . '/../_gallery.php' ?>
</section>

<section id="venue" aria-labelledby="venue-heading">
  <h2 id="venue-heading">Venue: <?= htmlspecialchars($venue['name']) ?></h2>
  <dl>
    <?php if ($venue['address']): ?>
    <div>
      <dt><iconify-icon icon="ph:map-pin" aria-hidden="true"></iconify-icon> Address</dt>
      <dd><?= nl2br(htmlspecialchars($venue['address'])) ?></dd>
    </div>
    <?php endif ?>
    <?php if ($venue['directions']): ?>
    <div>
      <dt><iconify-icon icon="ph:signpost" aria-hidden="true"></iconify-icon> Directions</dt>
      <dd><?= nl2br(htmlspecialchars($venue['directions'])) ?></dd>
    </div>
    <?php endif ?>
    <?php if ($venue['refreshments']): ?>
    <div>
      <dt><iconify-icon icon="ph:coffee" aria-hidden="true"></iconify-icon> Refreshments</dt>
      <dd><?= nl2br(htmlspecialchars($venue['refreshments'])) ?></dd>
    </div>
    <?php endif ?>
    <?php if ($venue['accessibility']): ?>
    <div>
      <dt><iconify-icon icon="ph:wheelchair" aria-hidden="true"></iconify-icon> Accessibility</dt>
      <dd><?= nl2br(htmlspecialchars($venue['accessibility'])) ?></dd>
    </div>
    <?php endif ?>
    <?php if ($venue['what_3_words']): ?>
    <?php $w3w = ltrim($venue['what_3_words'], '/'); ?>
    <div>
      <dt><iconify-icon icon="ph:hash" aria-hidden="true"></iconify-icon> What3Words</dt>
      <dd><a href="https://what3words.com/<?= htmlspecialchars(urlencode($w3w)) ?>">///<?= htmlspecialchars($w3w) ?></a></dd>
    </div>
    <?php endif ?>
    <?php if ($venue['parking']): ?>
    <div>
      <dt><iconify-icon icon="ph:car" aria-hidden="true"></iconify-icon> Parking</dt>
      <dd><?= nl2br(htmlspecialchars($venue['parking'])) ?></dd>
    </div>
    <?php endif ?>
    <?php if ($venue['dog_policy']): ?>
    <div>
      <dt><iconify-icon icon="ph:paw-print" aria-hidden="true"></iconify-icon> Dogs</dt>
      <dd><?= htmlspecialchars($venue['dog_policy']) ?></dd>
    </div>
    <?php endif ?>
  </dl>
</section>

<section aria-labelled-by="other-artists-heading">
<h2 id="other-artists-heading">Other artists in this parish</h2>
<ul>
  <?php foreach ($otherArtists as $other): ?>
  <li><a href="/artists/<?= htmlspecialchars($other['slug']) ?>"><?= htmlspecialchars($other['name']) ?></a></li>
  <?php endforeach ?>
</ul>
</section>
