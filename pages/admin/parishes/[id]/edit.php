<?php
$parish = $db->get('parishes', '*', ['id' => $req->params['id']]);

if (!$parish) {
    http_response_code(404);
    return;
}

if ($req->isPost()) {
    $data = [
        'name'      => $req->params['name'],
        'slug'      => unique_slug($db, 'parishes', $req->params['name'], (int) $parish['id']),
        'latitude'  => $req->params['latitude'] ?: null,
        'longitude' => $req->params['longitude'] ?: null,
    ];
    if (!empty($_FILES['image']['tmp_name'])) {
        $data['picture_id'] = cloudinary_upload($_FILES['image']['tmp_name'], $_FILES['image']['name']);
    }
    $db->update('parishes', $data, ['id' => $parish['id']]);
    return redirect('/admin/parishes');
}

$record = $parish;
?>
<h1 class="text-2xl font-semibold text-gray-900 mb-6">Edit Parish</h1>
<form method="post" action="/admin/parishes/<?= $parish['id'] ?>/edit" enctype="multipart/form-data" class="max-w-lg space-y-4">
  <?php require __DIR__ . '/../_form.php' ?>
  <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2">
    Save changes
  </button>
</form>
<div class="max-w-lg mt-8 pt-6 border-t border-gray-200">
  <form method="post" action="/admin/parishes/<?= $parish['id'] ?>/delete"
        onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($parish['name'])) ?>? This cannot be undone.')">
    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete parish</button>
  </form>
</div>
