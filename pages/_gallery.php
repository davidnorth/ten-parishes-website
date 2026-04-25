<div class="gallery-grid spotlight-group">
  <?php foreach ($images as $img): ?>
  <figure>
    <a class="spotlight" href="<?= cloudinary_url($img['image_id'], 'w_1600,c_limit') ?>"<?= !empty($img['name']) ? ' data-title="' . htmlspecialchars($img['name']) . '"' : '' ?>>
      <img src="<?= cloudinary_url($img['image_id'], 'w_400,h_400,c_fill') ?>" alt="<?= htmlspecialchars($img['name'] ?? '') ?>" loading="lazy">
    </a>
    <?php if (!empty($img['name'])): ?><figcaption><?= htmlspecialchars($img['name']) ?></figcaption><?php endif ?>
  </figure>
  <?php endforeach ?>
</div>
