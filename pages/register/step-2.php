<?php
if (empty($reg['artist'])) {
    return redirect('/register/step-1');
}

$parishes = $db->select('parishes', ['id', 'name'], ['ORDER' => 'name']);
$values   = $reg['venue'] ?? [];

if ($req->isPost()) {
    $venueData = [
        'name'                => trim($req->params['venue_name'] ?? ''),
        'parish_id'           => $req->params['parish_id'] ?: null,
        'latitude'            => $req->params['latitude'] ?: null,
        'longitude'           => $req->params['longitude'] ?: null,
        'address'             => trim($req->params['address'] ?? ''),
        'what_3_words'        => trim($req->params['what_3_words'] ?? ''),
        'directions'          => trim($req->params['directions'] ?? ''),
        'parking'             => trim($req->params['parking'] ?? ''),
        'refreshments'        => trim($req->params['refreshments'] ?? ''),
        'accessibility'       => trim($req->params['accessibility'] ?? ''),
        'dogs_allowed'        => isset($req->params['dogs_allowed']) ? 1 : 0,
        'venue_contact_name'  => trim($req->params['venue_contact_name'] ?? ''),
        'venue_contact_phone' => trim($req->params['venue_contact_phone'] ?? ''),
    ];

    $reg['venue'] = $venueData;

    if ($req->params['action'] === 'back') {
        return redirect('/register/step-1');
    }

    return redirect('/register/step-3');
}

$locationLat = $values['latitude'] ?? null;
$locationLng = $values['longitude'] ?? null;
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<h1>Register: Step 2 of 4 — Venue Details</h1>

<p>Please enter the details of the venue where your event will take place. Any details you don't have yet, leave blank.</p>

<form method="post" action="/register/step-2">

  <div>
    <label for="venue_name">Venue or studio name *</label>
    <input type="text" id="venue_name" name="venue_name" value="<?= htmlspecialchars($values['name'] ?? '') ?>" required>
  </div>

  <div>
    <label for="parish_id">Parish</label>
    <select id="parish_id" name="parish_id">
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

  <div>
    <label for="address">Address</label>
    <textarea id="address" name="address" rows="3"><?= htmlspecialchars($values['address'] ?? '') ?></textarea>
  </div>

  <div>
    <label for="what_3_words">What3Words</label>
    <input type="text" id="what_3_words" name="what_3_words" value="<?= htmlspecialchars($values['what_3_words'] ?? '') ?>" placeholder="e.g. ///word.word.word">
  </div>

  <div>
    <label for="directions">Directions</label>
    <textarea id="directions" name="directions" rows="3"><?= htmlspecialchars($values['directions'] ?? '') ?></textarea>
  </div>

  <div>
    <label for="parking">Parking</label>
    <textarea id="parking" name="parking" rows="2"><?= htmlspecialchars($values['parking'] ?? '') ?></textarea>
  </div>

  <div>
    <label for="refreshments">Refreshments</label>
    <textarea id="refreshments" name="refreshments" rows="2"><?= htmlspecialchars($values['refreshments'] ?? '') ?></textarea>
  </div>

  <div>
    <label for="accessibility">Accessibility</label>
    <textarea id="accessibility" name="accessibility" rows="2"><?= htmlspecialchars($values['accessibility'] ?? '') ?></textarea>
  </div>

  <div>
    <label>
      <input type="checkbox" name="dogs_allowed" value="1" <?= ($values['dogs_allowed'] ?? 0) ? 'checked' : '' ?>>
      Dogs welcome
    </label>
  </div>

  <div>
    <label for="venue_contact_name">Venue contact name</label>
    <input type="text" id="venue_contact_name" name="venue_contact_name" value="<?= htmlspecialchars($values['venue_contact_name'] ?? '') ?>">
  </div>

  <div>
    <label for="venue_contact_phone">Venue contact phone</label>
    <input type="tel" id="venue_contact_phone" name="venue_contact_phone" value="<?= htmlspecialchars($values['venue_contact_phone'] ?? '') ?>">
  </div>

  <div>
    <button type="submit" name="action" value="back" formnovalidate>&larr; Back</button>
    <button type="submit" name="action" value="next">Next &rarr;</button>
  </div>
</form>
