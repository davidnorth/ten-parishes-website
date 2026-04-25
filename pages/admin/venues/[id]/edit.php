<?php
$venue = $db->get('venues', '*', ['id' => $req->params['id']]);

if (!$venue) {
    http_response_code(404);
    return;
}

$parishes = $db->select('parishes', ['id', 'name'], ['ORDER' => 'name']);

if ($req->isPost()) {
    $data = [
        'name'                => $req->params['name'],
        'slug'                => unique_slug($db, 'venues', $req->params['name'], (int) $venue['id']),
        'parish_id'           => $req->params['parish_id'] ?: null,
        'latitude'            => $req->params['latitude'] ?: null,
        'longitude'           => $req->params['longitude'] ?: null,
        'address'             => $req->params['address'] ?: null,
        'what_3_words'        => $req->params['what_3_words'] ?: null,
        'directions'          => $req->params['directions'] ?: null,
        'parking'             => $req->params['parking'] ?: null,
        'refreshments'        => $req->params['refreshments'] ?: null,
        'accessibility'       => $req->params['accessibility'] ?: null,
        'dog_policy'          => $req->params['dog_policy'] ?: null,
        'venue_contact_name'  => $req->params['venue_contact_name'] ?: null,
        'venue_contact_phone' => $req->params['venue_contact_phone'] ?: null,
    ];
    $db->update('venues', $data, ['id' => $venue['id']]);
    return redirect('/admin/venues');
}
?>
<h1 class="text-2xl font-semibold text-gray-900 mb-6">Edit Venue</h1>
<form method="post" action="/admin/venues/<?= $venue['id'] ?>/edit" class="max-w-lg space-y-4">
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($venue['name']) ?>" required
           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Parish</label>
    <select name="parish_id"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      <option value="">— None —</option>
      <?php foreach ($parishes as $parish): ?>
      <option value="<?= $parish['id'] ?>" <?= $venue['parish_id'] == $parish['id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($parish['name']) ?>
      </option>
      <?php endforeach ?>
    </select>
  </div>
  <?php $locationLat = $venue['latitude']; $locationLng = $venue['longitude']; require __DIR__ . '/../../_location_picker.php'; ?>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
    <textarea name="address" rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($venue['address'] ?? '') ?></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">What3Words</label>
    <input type="text" name="what_3_words" value="<?= htmlspecialchars($venue['what_3_words'] ?? '') ?>"
           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Directions</label>
    <textarea name="directions" rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($venue['directions'] ?? '') ?></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Parking</label>
    <textarea name="parking" rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($venue['parking'] ?? '') ?></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Refreshments</label>
    <textarea name="refreshments" rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($venue['refreshments'] ?? '') ?></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Accessibility</label>
    <textarea name="accessibility" rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($venue['accessibility'] ?? '') ?></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Dog policy</label>
    <select name="dog_policy"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      <option value="">— Not specified —</option>
      <?php foreach (FormOptions::DOG_POLICIES as $opt): ?>
      <option value="<?= $opt ?>" <?= $venue['dog_policy'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
      <?php endforeach ?>
    </select>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Contact name</label>
    <input type="text" name="venue_contact_name" value="<?= htmlspecialchars($venue['venue_contact_name'] ?? '') ?>"
           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Contact phone</label>
    <input type="tel" name="venue_contact_phone" value="<?= htmlspecialchars($venue['venue_contact_phone'] ?? '') ?>"
           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
  </div>
  <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2">
    Save changes
  </button>
</form>
<div class="max-w-lg mt-8 pt-6 border-t border-gray-200">
  <form method="post" action="/admin/venues/<?= $venue['id'] ?>/delete"
        onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($venue['name'])) ?>? This cannot be undone.')">
    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete venue</button>
  </form>
</div>
