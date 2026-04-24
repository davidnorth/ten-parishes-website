<?php
$errors = [];
$values = $reg['artist'] ?? [];

if ($req->isPost()) {
    // Honeypot check
    if (!empty($req->params['website'])) {
        return redirect('/register/complete');
    }

    // Rate limit: 60 seconds between submissions
    $lastSubmit = $reg['submitted_at'] ?? 0;
    if ($lastSubmit && (time() - $lastSubmit) < 60) {
        $errors[] = 'Please wait a moment before submitting again.';
    }

    $name  = trim($req->params['name'] ?? '');
    $email = trim($req->params['email'] ?? '');
    $phone = trim($req->params['phone'] ?? '');
    $body  = trim($req->params['body_html'] ?? '');

    if (!$errors) {
        if ($name === '') $errors[] = 'Name is required.';
        if ($email === '') {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif ($db->has('artists', ['email' => $email])) {
            $errors[] = 'An artist with this email address is already registered.';
        }
    }

    if (!$errors) {
        $pictureId = $reg['artist']['picture_id'] ?? null;
        if (!empty($_FILES['picture_file']['tmp_name']) && is_uploaded_file($_FILES['picture_file']['tmp_name'])) {
            $pictureId = cloudinary_upload($_FILES['picture_file']['tmp_name'], $_FILES['picture_file']['name']);
        }

        $reg['artist'] = [
            'name'       => $name,
            'email'      => $email,
            'phone'      => $phone,
            'body_html'  => $body,
            'picture_id' => $pictureId,
        ];
        $reg['submitted_at'] = time();

        return redirect('/register/step-2');
    }

    $values = ['name' => $name, 'email' => $email, 'phone' => $phone, 'body_html' => $body];
}
?>

<div class="page-grid">
  <section>

<h1>Register: Step 1 of 4 — Artist Details</h1>

<?php if ($errors): ?>
<p role="alert"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></p>
<?php endif ?>

<form method="post" action="/register/step-1" enctype="multipart/form-data">
  <input type="text" name="website" value="" style="position:absolute;left:-9999px;top:-9999px;opacity:0" tabindex="-1" autocomplete="off" aria-hidden="true">

  <p>Please enter your details as an artist. Fields marked * are required.</p>

  <div>
    <label for="name">Name *</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($values['name'] ?? '') ?>" required>
  </div>

  <div>
    <label for="email">Email *</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($values['email'] ?? '') ?>" required>
  </div>

  <div>
    <label for="phone">Phone</label>
    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($values['phone'] ?? '') ?>">
  </div>

  <div>
    <label for="body_html">Tell us about work</label>
    <textarea id="body_html" name="body_html" rows="6"><?= htmlspecialchars($values['body_html'] ?? '') ?></textarea>
  </div>

  <div>
    <label for="picture_file">Photo of you (optional)</label>
    <?php if (!empty($reg['artist']['picture_id'])): ?>
    <img src="<?= cloudinary_url($reg['artist']['picture_id'], 'w_160,h_160,c_fill') ?>" alt="Current profile picture">
    <p><small>Upload a new file to replace the current picture.</small></p>
    <?php endif ?>
    <input type="file" id="picture_file" name="picture_file" accept="image/*">
  </div>

  <div class="form-actions">
    <button type="submit">Next &rarr;</button>
  </div>
</form>

  </section>
</div>

