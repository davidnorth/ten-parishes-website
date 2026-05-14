<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
  <input type="text" name="name" value="<?= htmlspecialchars($record['name'] ?? '') ?>" required
         class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
</div>
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Parish</label>
  <select name="parish_id"
          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    <option value="">— None —</option>
    <?php foreach ($parishes as $parish): ?>
    <option value="<?= $parish['id'] ?>" <?= ($record['parish_id'] ?? '') == $parish['id'] ? 'selected' : '' ?>>
      <?= htmlspecialchars($parish['name']) ?>
    </option>
    <?php endforeach ?>
  </select>
</div>
<?php $locationLat = $record['latitude'] ?? null; $locationLng = $record['longitude'] ?? null; require __DIR__ . '/../_location_picker.php'; ?>
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
  <textarea name="address" rows="3"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($record['address'] ?? '') ?></textarea>
</div>
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">What3Words</label>
  <input type="text" name="what_3_words" value="<?= htmlspecialchars($record['what_3_words'] ?? '') ?>"
         class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
</div>
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Directions</label>
  <textarea name="directions" rows="3"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($record['directions'] ?? '') ?></textarea>
</div>
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Parking</label>
  <textarea name="parking" rows="3"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($record['parking'] ?? '') ?></textarea>
</div>
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Refreshments</label>
  <textarea name="refreshments" rows="3"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($record['refreshments'] ?? '') ?></textarea>
</div>
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Accessibility</label>
  <textarea name="accessibility" rows="3"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($record['accessibility'] ?? '') ?></textarea>
</div>
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Dog policy</label>
  <select name="dog_policy"
          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    <option value="">— Not specified —</option>
    <?php foreach (FormOptions::DOG_POLICIES as $opt): ?>
    <option value="<?= $opt ?>" <?= ($record['dog_policy'] ?? '') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
    <?php endforeach ?>
  </select>
</div>
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Contact name</label>
  <input type="text" name="venue_contact_name" value="<?= htmlspecialchars($record['venue_contact_name'] ?? '') ?>"
         class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
</div>
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Contact phone</label>
  <input type="tel" name="venue_contact_phone" value="<?= htmlspecialchars($record['venue_contact_phone'] ?? '') ?>"
         class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
</div>
