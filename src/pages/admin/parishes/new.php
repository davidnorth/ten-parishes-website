<?php
if ($req->isPost()) {
    $pictureId = null;
    if (!empty($_FILES['image']['tmp_name'])) {
        $pictureId = cloudinary_upload($_FILES['image']['tmp_name'], $_FILES['image']['name']);
    }
    $db->insert('parishes', [
        'name'       => $req->params['name'],
        'slug'       => unique_slug($db, 'parishes', $req->params['name']),
        'latitude'   => $req->params['latitude'] ?: null,
        'longitude'  => $req->params['longitude'] ?: null,
        'picture_id' => $pictureId,
    ]);
    return redirect('/admin/parishes');
}

$record = [];
?>
<h1 class="text-2xl font-semibold text-gray-900 mb-6">New Parish</h1>
<form method="post" action="/admin/parishes/new" enctype="multipart/form-data" class="max-w-lg space-y-4">
  <?php require __DIR__ . '/_form.php' ?>
  <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2">
    Create parish
  </button>
</form>
