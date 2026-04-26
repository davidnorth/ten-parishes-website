<?php
if (empty($reg['artist_id'])) {
    return redirect('/register/step-1');
}

$pageTitle = 'Register: Images';
if ($req->isPost()) {
    if ($req->params['action'] === 'back') {
        return redirect('/register/step-3');
    }

    $artistId = $reg['artist_id'];

    if (!empty($_FILES['images']['tmp_name'])) {
        $hasMain = $db->has('images', ['artist_id' => $artistId, 'main' => 1]);
        foreach ($_FILES['images']['tmp_name'] as $idx => $tmpPath) {
            if (!empty($tmpPath) && is_uploaded_file($tmpPath)) {
                $publicId = cloudinary_upload($tmpPath, $_FILES['images']['name'][$idx]);
                $db->insert('images', [
                    'artist_id' => $artistId,
                    'main'      => $hasMain ? 0 : 1,
                    'name'      => $req->params['image_name'][$idx] ?? null,
                    'image_id'  => $publicId,
                ]);
                $hasMain = true;
            }
        }
    }

    unset($_SESSION['registration']);
    return redirect('/register/complete');
}

$artist = $reg['artist'] ?? [];
$venue  = $reg['venue'] ?? [];
if (!empty($venue['id']) && empty($venue['name'])) {
    $venue['name'] = $db->get('venues', 'name', ['id' => $venue['id']]);
}
?>



<div class="page-grid">
  <section>

<h1>Register: Step 4 of 4 — Images</h1>

<p>Upload images of your work here. Please ensure the image size is at least 800&times;600px and the file size is less than 5MB. This step is optional &mdash; you can add or change images later.</p>

<?php if (!empty($artist['name'])): ?>
<p><strong><?= htmlspecialchars($artist['name']) ?></strong>
<?php if (!empty($venue['name'])): ?> &mdash; <?= htmlspecialchars($venue['name']) ?><?php endif ?>
</p>
<?php endif ?>

<form method="post" action="/register/step-4" enctype="multipart/form-data">

  <div id="images"></div>
  <button type="button" onclick="addImage()">+ Add image</button>

  <div>
    <button type="submit" name="action" value="back">&larr; Back</button>
    <button type="submit" name="action" value="next">Submit registration</button>
  </div>
</form>

  </section>
</div>


<script>
let imageIndex = 0;
function addImage() {
    const idx = imageIndex++;
    const row = document.createElement('div');
    row.className = 'image-row';
    row.innerHTML = `
        <input type="file" name="images[${idx}]" accept="image/*">
        <input type="text" name="image_name[${idx}]" placeholder="Caption (optional)">
        <button type="button" onclick="this.closest('.image-row').remove()">Remove</button>
    `;
    document.getElementById('images').appendChild(row);
}
</script>
