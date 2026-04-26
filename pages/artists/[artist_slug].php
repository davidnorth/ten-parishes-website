<?php

$nav_current = 'artists';

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

$timesByDate = [];
foreach ($eventDates as $ed) {
    if (!$ed['from_time']) continue;
    $from = substr($ed['from_time'], 0, 5);
    $to   = $ed['to_time'] ? substr($ed['to_time'], 0, 5) : '';
    $timesByDate[$ed['date']] = $from . ($to ? '–' . $to : '');
}

$festivalDays = [];
$start = new DateTime(FestivalOptions::START_DATE);
for ($i = 0; $i < 10; $i++) {
    $dt = (clone $start)->modify("+$i days");
    $festivalDays[] = [
        'label' => $dt->format('M') . ' ' . ordinal((int) $dt->format('j')),
        'times' => $timesByDate[$dt->format('Y-m-d')] ?? '',
    ];
}

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

<section class="full-bleed-special">

  <div class="hero-gallery spotlight-group">
  <?php foreach ($images as $img): ?>
  <figure>
    <a class="spotlight" href="<?= cloudinary_url($img['image_id'], 'w_1600,c_limit') ?>"<?= !empty($img['name']) ? ' data-title="' . htmlspecialchars($img['name']) . '"' : '' ?>>
      <img src="<?= cloudinary_url($img['image_id'], 'w_600,h_600,c_fill') ?>" alt="<?= htmlspecialchars($img['name'] ?? '') ?>" loading="lazy">
    </a>
  </figure>
  <?php endforeach ?>
  </div>

</section>

<section aria-labelledby="name-heading" class="flex flex-end">
  <div>
    <h1 id="name-heading"><?= htmlspecialchars($artist['name']) ?></h1>
    <?php if (!empty($artist['disciplines'])): ?>
      <p class="weight-bold text-md">Disciplines: <?= htmlspecialchars(implode(', ', array_map('trim', explode(',', $artist['disciplines'])))) ?></p>
    <?php endif ?>
    <div class="readable-max-width">
    <?= $artist['body_html'] ?>
    </div>
  </div>
  <div>
    <?php if (!empty($artist['picture_id'])): ?>
      <?= cloudinary_image($artist['picture_id'], 400, 250, $artist['name']) ?>
    <?php endif ?>
  </div>
</section>

<section id="venue" aria-labelledby="venue-heading">
  <div>
    <?php if ($venue['latitude'] && $venue['longitude']): ?>
      <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
      <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
      <?php render_partial(__DIR__ . '/../_map.php', ['lat' => $venue['latitude'], 'lng' => $venue['longitude']]) ?>
    <?php endif ?>
  </div>
  <div>
    <h2 class="text-lg" id="venue-heading">Location</h2>
    <?php if ($venue['address']): ?>
      <p><?= nl2br(htmlspecialchars($venue['address'])) ?></p>
    <?php endif ?>

    <?php if ($venue['what_3_words']): ?>
      <?php $w3w = ltrim($venue['what_3_words'], '/'); ?>
      <p>
        <a href="https://what3words.com/<?= htmlspecialchars(urlencode($w3w)) ?>">///<?= htmlspecialchars($w3w) ?></a>
      </p>
    <?php endif ?>

    <?php if ($venue['directions']): ?>
      <p><?= nl2br(htmlspecialchars($venue['directions'])) ?></p>
    <?php endif ?>
  </div>
</section>

<section>
  <ul class="event-dates">
    <?php foreach ($festivalDays as $day): ?>
    <li>
      <div><?= $day['label'] ?></div>
      <?php if($day['times']): ?>
        <div class="times"><?= htmlspecialchars($day['times']) ?></div>
      <?php else: ?>
        <div>&nbsp;</div>
      <?php endif ?>
    </li>
    <?php endforeach ?>
  </ul>
</section>

<section>
  <dl class="amenity-list">
   <?php if ($venue['refreshments']): ?>
    <div>
      <dt><iconify-icon icon="ph:coffee" class="icon-medium" aria-hidden="true"></iconify-icon></dt>
      <dd><?= nl2br(htmlspecialchars($venue['refreshments'])) ?></dd>
    </div>
    <?php endif ?>
    <?php if ($venue['accessibility']): ?>
    <div>
      <dt><iconify-icon icon="ph:wheelchair" class="icon-medium" aria-hidden="true"></iconify-icon></dt>
      <dd><?= nl2br(htmlspecialchars($venue['accessibility'])) ?></dd>
    </div>
    <?php endif ?>
    <?php if ($venue['parking']): ?>
    <div>
      <dt><iconify-icon icon="ph:car" class="icon-medium" aria-hidden="true"></iconify-icon></dt>
      <dd><?= nl2br(htmlspecialchars($venue['parking'])) ?></dd>
    </div>
    <?php endif ?>
    <?php if ($venue['dog_policy']): ?>
    <div>
      <dt><iconify-icon icon="ph:paw-print" class="icon-medium" aria-hidden="true"></iconify-icon></dt>
      <dd><?= htmlspecialchars($venue['dog_policy']) ?></dd>
    </div>
    <?php endif ?>
  </dl>
</section>

<section aria-labelled-by="other-artists-heading">
<h3 id="other-artists-heading">Other artists in this parish</h3>
<ul>
  <?php foreach ($otherArtists as $other): ?>
  <li><a href="/artists/<?= htmlspecialchars($other['slug']) ?>"><?= htmlspecialchars($other['name']) ?></a></li>
  <?php endforeach ?>
</ul>
</section>
