<?php
if (empty($reg['artist'])) {
    return redirect('/register/step-1');
}

if ($req->isPost()) {
    if ($req->params['action'] === 'back') {
        return redirect('/register/step-3');
    }

    // Create venue
    $venueData = $reg['venue'] ?? [];
    $db->insert('venues', [
        'name'                => $venueData['name'],
        'slug'                => unique_slug($db, 'venues', $venueData['name']),
        'parish_id'           => $venueData['parish_id'] ?: null,
        'latitude'            => $venueData['latitude'] ?: null,
        'longitude'           => $venueData['longitude'] ?: null,
        'address'             => $venueData['address'] ?: null,
        'what_3_words'        => $venueData['what_3_words'] ?: null,
        'directions'          => $venueData['directions'] ?: null,
        'parking'             => $venueData['parking'] ?: null,
        'refreshments'        => $venueData['refreshments'] ?: null,
        'accessibility'       => $venueData['accessibility'] ?: null,
        'dogs_allowed'        => $venueData['dogs_allowed'] ?? 0,
        'venue_contact_name'  => $venueData['venue_contact_name'] ?: null,
        'venue_contact_phone' => $venueData['venue_contact_phone'] ?: null,
    ]);
    $venueId = $db->id();

    // Create artist
    $artistData = $reg['artist'];
    $db->insert('artists', [
        'venue_id'   => $venueId,
        'type'       => 'exhibition',
        'name'       => $artistData['name'],
        'slug'       => unique_slug($db, 'artists', $artistData['name']),
        'body_html'  => $artistData['body_html'] ?: null,
        'email'      => $artistData['email'] ?: null,
        'phone'      => $artistData['phone'] ?: null,
        'picture_id' => $artistData['picture_id'] ?: null,
        'approved'   => 0,
    ]);
    $artistId = $db->id();

    // Insert event dates
    foreach ($reg['event_dates'] ?? [] as $ed) {
        if (!empty($ed['date'])) {
            $db->insert('event_dates', [
                'artist_id' => $artistId,
                'date'      => $ed['date'],
                'from_time' => $ed['from_time'] ?: null,
                'to_time'   => $ed['to_time'] ?: null,
            ]);
        }
    }

    // Upload images
    if (!empty($_FILES['images']['tmp_name'])) {
        $firstImage = true;
        foreach ($_FILES['images']['tmp_name'] as $idx => $tmpPath) {
            if (!empty($tmpPath) && is_uploaded_file($tmpPath)) {
                $publicId = cloudinary_upload($tmpPath, $_FILES['images']['name'][$idx]);
                $db->insert('images', [
                    'artist_id' => $artistId,
                    'main'      => $firstImage ? 1 : 0,
                    'name'      => $req->params['image_name'][$idx] ?? null,
                    'image_id'  => $publicId,
                ]);
                $firstImage = false;
            }
        }
    }

    unset($_SESSION['registration']);
    return redirect('/register/complete');
}

$artist = $reg['artist'] ?? [];
$venue  = $reg['venue'] ?? [];
?>



<div class="page-grid">
  <section>

<h1>Register: Step 4 of 4 — Images</h1>

<p>Upload images of your work here. Please ensure the image size is at least 800&times;600px and the file size is less than 5MB. You can add more images later.</p>

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
