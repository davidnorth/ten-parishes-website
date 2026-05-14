<?php

$nav_current = 'artists';

function ordinal(int $n): string
{
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
  'ORDER' => ['date', 'from_time'],
]);

$timesByDate = [];
foreach ($eventDates as $ed) {
  if (!$ed['from_time'])
    continue;
  $from = substr($ed['from_time'], 0, 5);
  $to = $ed['to_time'] ? substr($ed['to_time'], 0, 5) : '';
  $timesByDate[$ed['date']] = $from . ($to ? '–' . $to : '');
}

$festivalDays = [];
$start = new DateTime(FestivalOptions::START_DATE);
for ($i = 0; $i < 10; $i++) {
  $dt = (clone $start)->modify("+$i days");
  $festivalDays[] = [
    'day' => $dt->format('D'),
    'date' => ordinal((int) $dt->format('j')),
    'month' => $dt->format('F'),
    'times' => $timesByDate[$dt->format('Y-m-d')] ?? '',
  ];
}

$images = $db->select('images', '*', ['artist_id' => $artist['id']]);

$venue = $db->get('venues', '*', ['id' => $artist['venue_id']]);


$parish = $db->get('parishes', '*', ['id' => $venue['parish_id']]);

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

$otherArtistsInVenue = $artist['venue_id'] ? $db->select('artists', ['name', 'slug'], [
  'venue_id' => $artist['venue_id'],
  'id[!]' => $artist['id'],
  'approved' => 1,
  'ORDER' => 'name',
]) : [];


?>

<div class="page-grid">

  <section class="breadcrumb">
    <a href="/artists">Artists</a>
    <span>/</span>
    <span><a href="/parishes/<?= $parish['slug'] ?>"><?= htmlspecialchars($parish['name']) ?></a></span>
    <span>/</span>
    <?= htmlspecialchars($artist['name']) ?>
  </section>

  <section class="">

    <div class="hero-gallery spotlight-group">
      <?php foreach ($images as $img): ?>
        <figure>
          <a class="spotlight" href="<?= cloudinary_url($img['image_id'], 'w_1600,c_limit') ?>" <?= !empty($img['name']) ? ' data-title="' . htmlspecialchars($img['name']) . '"' : '' ?>>
            <img src="<?= cloudinary_url($img['image_id'], 'w_600,h_600,c_fill') ?>"
              alt="<?= htmlspecialchars($img['name'] ?? '') ?>" loading="lazy">
          </a>
        </figure>
      <?php endforeach ?>
    </div>

  </section>

  <section aria-labelledby="name-heading" class="flex flex-end border-bottom gap-2xl">
    <div>
      <h1 id="name-heading"><?= htmlspecialchars($artist['name']) ?></h1>
      <?php if (!empty($artist['disciplines'])): ?>
        <p class="flex gap-sm mt-md">
          <?php foreach (explode(',', $artist['disciplines']) as $discipline): ?>
            <span class="chip"><?= htmlspecialchars(trim($discipline)) ?></span>
          <?php endforeach ?>
        </p>
      <?php endif ?>

      <div class="prose readable-max-width">
        <?= $artist['body_html'] ?>
        <?php if (!empty($artist['website'])): ?>
          <p><a href="<?= htmlspecialchars($artist['website']) ?>" target="_blank" rel="noopener">Visit
              <?= formatURL($artist['website']) ?></a></p>
        <?php endif ?>
      </div>
    </div>
    <div>
      <?php if (!empty($artist['picture_id'])): ?>
        <?= cloudinary_image($artist['picture_id'], 400, 250, $artist['name']) ?>
      <?php endif ?>
    </div>
  </section>

  <section id="venue" aria-labelledby="venue-heading" class="border-bottom">
    <div>
      <?php if ($venue['latitude'] && $venue['longitude']): ?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <?php render_partial(__DIR__ . '/../_map.php', ['lat' => $venue['latitude'], 'lng' => $venue['longitude']]) ?>
      <?php endif ?>
    </div>
    <div>

      <?php if ($venue['address']): ?>
        <h3>Address</h3>
        <p class="text-md"><?= nl2br(htmlspecialchars($venue['address'])) ?></p>
      <?php endif ?>
      <?php if ($venue['directions']): ?>
        <h3>Directions</h3>
        <p><?= nl2br(htmlspecialchars($venue['directions'])) ?></p>
      <?php endif ?>

      <?php if ($venue['what_3_words']): ?>
        <h3>What3words</h3>
        <?php $w3w = ltrim($venue['what_3_words'], '/'); ?>
        <p>
          <a href="https://what3words.com/<?= htmlspecialchars(urlencode($w3w)) ?>">///<?= htmlspecialchars($w3w) ?></a>
        </p>
      <?php endif ?>


      <h3>Facilities</h3>
      <p class="flex gap-sm facilities">
        <?php if ($venue['refreshments']): ?>
          <span class="chip"><iconify-icon icon="ph:coffee" aria-hidden="true"></iconify-icon>
            <?= htmlspecialchars($venue['refreshments']) ?></span>
        <?php endif ?>
        <?php if ($venue['accessibility']): ?>
          <span class="chip"><iconify-icon icon="ph:wheelchair" aria-hidden="true"></iconify-icon>
            <?= htmlspecialchars($venue['accessibility']) ?></span>
        <?php endif ?>
        <?php if ($venue['parking']): ?>
          <span class="chip"><iconify-icon icon="ph:car" aria-hidden="true"></iconify-icon>
            <?= htmlspecialchars($venue['parking']) ?></span>
        <?php endif ?>
        <?php if ($venue['dog_policy']): ?>
          <span class="chip"><iconify-icon icon="ph:paw-print" aria-hidden="true"></iconify-icon>
            <?= htmlspecialchars($venue['dog_policy']) ?></span>
        <?php endif ?>
      </p>



    </div>
  </section>

  <?php if (count($otherArtistsInVenue) > 0): ?>
    <section aria-labelledby="other-venue-artists-heading" class="border-bottom">
      <h2 id="other-venue-artists-heading">Also at this venue</h2>
      <ul>
        <?php foreach ($otherArtistsInVenue as $other): ?>
          <li><a href="/artists/<?= htmlspecialchars($other['slug']) ?>"><?= htmlspecialchars($other['name']) ?></a></li>
        <?php endforeach ?>
      </ul>
    </section>
  <?php endif ?>


  <section aria-labelledby="event-dates-heading" class="border-bottom">
    <h2 id="event-dates-heading">When to visit</h2>
    <ul class="event-dates">
      <?php foreach ($festivalDays as $day): ?>
        <li class="<?= $day['times'] ? 'open' : 'closed' ?>">
          <div class="day"><?= htmlspecialchars($day['day']) ?></div>
          <div class="date"><?= htmlspecialchars($day['date']) ?></div>
          <div class="month"><?= htmlspecialchars($day['month']) ?></div>
          <div class="times"><?= $day['times'] ? htmlspecialchars($day['times']) : 'Closed' ?></div>
        </li>
      <?php endforeach ?>
    </ul>
  </section>

  <section aria-labelled-by="other-artists-heading">
    <h2 id="other-artists-heading">Other artists in this parish</h2>
    <ul>
      <?php foreach ($otherArtists as $other): ?>
        <li><a href="/artists/<?= htmlspecialchars($other['slug']) ?>"><?= htmlspecialchars($other['name']) ?></a></li>
      <?php endforeach ?>
    </ul>
  </section>
