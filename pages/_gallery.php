<div class="gallery-grid spotlight-group">
  <?php foreach ($images as $img): ?>
  <figure>
    <a class="spotlight" href="<?= cloudinary_url($img['image_id'], 'w_1600,c_limit') ?>"<?= !empty($img['name']) ? ' data-title="' . htmlspecialchars($img['name']) . '"' : '' ?>>
      <?= cloudinary_image($img['image_id'], 400, 400, $img['name'] ?? '') ?>
    </a>
    <?php if (!empty($img['name'])): ?><figcaption><?= htmlspecialchars($img['name']) ?></figcaption><?php endif ?>
  </figure>
  <?php endforeach ?>
</div>
