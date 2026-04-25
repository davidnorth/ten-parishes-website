<?php
if (empty($reg['artist'])) {
    return redirect('/register/step-1');
}

$pageTitle = 'Register: Venue Details';
$parishes = $db->select('parishes', ['id', 'name'], ['ORDER' => 'name']);
$values   = $reg['venue'] ?? [];

if ($req->isPost()) {
    $venueData = $req->params['venue'] ?? [];
    $reg['venue'] = $venueData;

    if ($req->params['action'] === 'back') {
        return redirect('/register/step-1');
    }

    return redirect('/register/step-3');
}

$locationLat = $values['latitude'] ?? null;
$locationLng = $values['longitude'] ?? null;
$locationFieldPrefix = 'venue';
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


<div class="page-grid">
  <section>


<h1>Register: Step 2 of 4 — Venue Details</h1>

<p>Please enter the details of the venue where your event will take place. Any details you don't have yet, leave blank.</p>

<form method="post" action="/register/step-2">

  <?= text_field('Venue or studio name', 'venue[name]', $values['name'] ?? '') ?>

  <div>
    <label for="parish_id">Parish</label>
    <select id="parish_id" name="venue[parish_id]">
      <option value="">— Select parish —</option>
      <?php foreach ($parishes as $parish): ?>
      <option value="<?= $parish['id'] ?>" <?= ($values['parish_id'] ?? '') == $parish['id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($parish['name']) ?>
      </option>
      <?php endforeach ?>
    </select>
  </div>

  <div>
    <label>Location</label>
    <p><small>Click the map to pin your venue, or use the search box.</small></p>
    <?php require __DIR__ . '/_location_picker.php' ?>
  </div>

  <div class="field-pair">
    <?= text_field('Address', 'venue[address]', $values['address'] ?? '') ?>
    <?= text_field('What3Words', 'venue[what_3_words]', $values['what_3_words'] ?? '', ['placeholder' => 'e.g. ///word.word.word']) ?>
  </div>
  <div class="field-pair">
    <?= text_field('Directions', 'venue[directions]', $values['directions'] ?? '') ?>
    <?= text_field('Parking', 'venue[parking]', $values['parking'] ?? '') ?>
  </div>

  <div class="field-pair">
    <?= text_field('Refreshments', 'venue[refreshments]', $values['refreshments'] ?? '') ?>
    <?= text_field('Accessibility', 'venue[accessibility]', $values['accessibility'] ?? '') ?>
  </div>

  <div>
    <label for="dog_policy">Dog policy</label>
    <select id="dog_policy" name="venue[dog_policy]">
      <?php foreach (FormOptions::DOG_POLICIES as $opt): ?>
      <option value="<?= $opt ?>" <?= ($values['dog_policy'] ?? '') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
      <?php endforeach ?>
    </select>
  </div>

  <?= text_field('Venue contact name', 'venue[contact_name]', $values['contact_name'] ?? '') ?>
  <?= text_field('Venue contact phone', 'venue[contact_phone]', $values['contact_phone'] ?? '', ['type' => 'tel']) ?>

  <div class="form-actions">
    <button class="secondary" type="submit" name="action" value="back" formnovalidate>&larr; Back</button>
    <button class="primary" type="submit" name="action" value="next">Next &rarr;</button>
  </div>
</form>

  </section>
</div>
