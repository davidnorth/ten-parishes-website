<?php
$parishes = $db->select('parishes', ['id', 'name'], ['ORDER' => 'name']);

if ($req->isPost()) {
    $db->insert('venues', [
        'name'                => $req->params['name'],
        'slug'                => unique_slug($db, 'venues', $req->params['name']),
        'parish_id'           => $req->params['parish_id'] ?: null,
        'latitude'            => $req->params['latitude'] ?: null,
        'longitude'           => $req->params['longitude'] ?: null,
        'address'             => $req->params['address'] ?: null,
        'what_3_words'        => $req->params['what_3_words'] ?: null,
        'directions'          => $req->params['directions'] ?: null,
        'parking'             => $req->params['parking'] ?: null,
        'refreshments'        => $req->params['refreshments'] ?: null,
        'accessibility'       => $req->params['accessibility'] ?: null,
        'dogs_allowed'        => isset($req->params['dogs_allowed']) ? 1 : 0,
        'venue_contact_name'  => $req->params['venue_contact_name'] ?: null,
        'venue_contact_phone' => $req->params['venue_contact_phone'] ?: null,
    ]);
    return redirect('/admin/venues');
}
?>
<h1 class="text-2xl font-semibold text-gray-900 mb-6">New Venue</h1>
<form method="post" action="/admin/venues/new" class="max-w-lg space-y-4">
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
    <input type="text" name="name" required
           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Parish</label>
    <select name="parish_id"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      <option value="">— None —</option>
      <?php foreach ($parishes as $parish): ?>
      <option value="<?= $parish['id'] ?>"><?= htmlspecialchars($parish['name']) ?></option>
      <?php endforeach ?>
    </select>
  </div>
  <?php $locationLat = null; $locationLng = null; require __DIR__ . '/../_location_picker.php'; ?>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
    <textarea name="address" rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">What3Words</label>
    <input type="text" name="what_3_words"
           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Directions</label>
    <textarea name="directions" rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Parking</label>
    <textarea name="parking" rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Refreshments</label>
    <textarea name="refreshments" rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Accessibility</label>
    <textarea name="accessibility" rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
  </div>
  <div class="flex items-center gap-2">
    <input type="checkbox" name="dogs_allowed" id="dogs_allowed" value="1">
    <label for="dogs_allowed" class="text-sm font-medium text-gray-700">Dogs allowed</label>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Contact name</label>
    <input type="text" name="venue_contact_name"
           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Contact phone</label>
    <input type="tel" name="venue_contact_phone"
           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
  </div>
  <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2">
    Create venue
  </button>
</form>
