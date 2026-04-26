<?php
$pageTitle = 'Register: Artist Details';
$errors = [];
$values = $reg['artist'] ?? [];

if ($req->isPost()) {
    // Honeypot check
    if (!empty($req->params['website'])) {
        return redirect('/register/complete');
    }

    $isFirstSubmission = empty($reg['artist_id']);
    $lastSubmit = $reg['submitted_at'] ?? 0;
    if ($isFirstSubmission && !getenv('TEST_MODE') && $lastSubmit && (time() - $lastSubmit) < 60) {
        $errors[] = 'Please wait a moment before submitting again.';
    }

    $artist = $req->params['artist'] ?? [];

    if (!$errors) {
        if (($artist['name'] ?? '') === '') $errors[] = 'Name is required.';
        $email = $artist['email'] ?? '';
        if ($email === '') {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
    }

    if (!$errors) {
        $pictureId = $reg['artist']['picture_id'] ?? null;
        if (!empty($_FILES['picture_file']['tmp_name']) && is_uploaded_file($_FILES['picture_file']['tmp_name'])) {
            $pictureId = cloudinary_upload($_FILES['picture_file']['tmp_name'], $_FILES['picture_file']['name']);
        }

        $reg['artist'] = $artist + ['picture_id' => $pictureId];
        $reg['submitted_at'] = time();

        $row = [
            'name'       => $artist['name'],
            'email'      => $artist['email'] ?: null,
            'phone'      => $artist['phone'] ?: null,
            'body_html'  => $artist['body_html'] ?: null,
            'picture_id' => $pictureId,
        ];

        if ($isFirstSubmission) {
            $db->insert('artists', $row + [
                'type'     => 'exhibition',
                'slug'     => unique_slug($db, 'artists', $artist['name']),
                'approved' => 0,
            ]);
            $reg['artist_id'] = $db->id();
        } else {
            $row['slug'] = unique_slug($db, 'artists', $artist['name'], $reg['artist_id']);
            $db->update('artists', $row, ['id' => $reg['artist_id']]);
        }

        return redirect('/register/step-2');
    }

    $values = $artist;
}
?>

<div class="page-grid">
  <section class="register-wizard">


<div>

<?php $current = 1; require __DIR__ . '/_progress.php'; ?>

</div>
<div>



<h1>Artist Details</h1>



<?php if ($errors): ?>
<p role="alert"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></p>
<?php endif ?>

<form method="post" action="/register/step-1" enctype="multipart/form-data">
  <input type="text" name="website" value="" style="position:absolute;left:-9999px;top:-9999px;opacity:0" tabindex="-1" autocomplete="off" aria-hidden="true">


  <?= text_field('Name *', 'artist[name]', $values['name'] ?? '', ['required' => true]) ?>
  <div class="field-pair">
    <?= text_field('Email *', 'artist[email]', $values['email'] ?? '', ['type' => 'email', 'required' => true]) ?>
    <?= text_field('Phone', 'artist[phone]', $values['phone'] ?? '', ['type' => 'tel']) ?>
  </div>

  <?= text_field('Tell us about work', 'artist[body_html]', $values['body_html'] ?? '', ['textarea' => true, 'rows' => 6]) ?>

  <div>
    <label for="picture_file">Photo of you (optional)</label>
    <?php if (!empty($reg['artist']['picture_id'])): ?>
    <img src="<?= cloudinary_url($reg['artist']['picture_id'], 'w_160,h_160,c_fill') ?>" alt="Current profile picture">
    <p><small>Upload a new file to replace the current picture.</small></p>
    <?php endif ?>
    <input type="file" id="picture_file" name="picture_file" accept="image/*">
  </div>

  <div class="form-actions first">
    <button type="submit">Next &rarr;</button>
  </div>
</form>

    </div>
  </section>
</div>

