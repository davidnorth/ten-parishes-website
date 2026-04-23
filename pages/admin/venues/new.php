<?php
$parishes = $db->select('parishes', ['id', 'name'], ['ORDER' => 'name']);

if ($req->isPost()) {
    $pictureId = null;
    if (!empty($_FILES['image']['tmp_name'])) {
        $pictureId = cloudinary_upload($_FILES['image']['tmp_name'], $_FILES['image']['name']);
    }
    $db->insert('venues', [
        'name'       => $req->params['name'],
        'slug'       => unique_slug($db, 'venues', $req->params['name']),
        'parish_id'  => $req->params['parish_id'] ?: null,
        'latitude'   => $req->params['latitude'] ?: null,
        'longitude'  => $req->params['longitude'] ?: null,
        'picture_id' => $pictureId,
    ]);
    return redirect('/admin/venues');
}
?>
<h1 class="text-2xl font-semibold text-gray-900 mb-6">New Venue</h1>
<form method="post" action="/admin/venues/new" enctype="multipart/form-data" class="max-w-lg space-y-4">
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
    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
    <input type="file" name="image" accept="image/*"
           class="text-sm text-gray-500 file:mr-3 file:border-0 file:rounded-md file:bg-blue-600 file:text-white file:px-3 file:py-1.5 file:text-sm file:cursor-pointer">
  </div>
  <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2">
    Create venue
  </button>
</form>
