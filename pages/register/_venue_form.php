<?php
$values = $reg['venue'] ?? [];
$locationLat = $values['latitude'] ?? null;
$locationLng = $values['longitude'] ?? null;
$locationFieldPrefix = 'venue';
?>
<form method="post" action="/register/step-2">
  <input type="hidden" name="mode" value="create">

  <p>Please enter the details of the venue where your event will take place. Any details you don't have yet, leave blank.</p>

  <?= text_field('Venue or studio name', 'venue[name]', $values['name'] ?? '', ['required' => true]) ?>

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

  <?= text_field('Address', 'venue[address]', $values['address'] ?? '', ['required' => true]) ?>

  <div>
    <label>Location <small>(optional — we'll look it up from the address if you skip this)</small></label>
    <p><small>Click the map to pin your venue, or use the search box.</small></p>
    <?php require __DIR__ . '/_location_picker.php' ?>
  </div>

  <div class="field-pair">
    <?= text_field('What3Words', 'venue[what_3_words]', $values['what_3_words'] ?? '', ['placeholder' => 'e.g. ///word.word.word']) ?>
    <?= text_field('Directions', 'venue[directions]', $values['directions'] ?? '') ?>
  </div>

  <div class="field-pair">
    <?= text_field('Parking', 'venue[parking]', $values['parking'] ?? '') ?>
    <?= text_field('Refreshments', 'venue[refreshments]', $values['refreshments'] ?? '') ?>
  </div>

  <?= text_field('Accessibility', 'venue[accessibility]', $values['accessibility'] ?? '') ?>

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
