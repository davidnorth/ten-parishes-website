<div class="gallery-grid">
  <?php foreach ($images as $img): ?>
  <figure>
    <img src="<?= cloudinary_url($img['image_id'], 'w_400,h_400,c_fill') ?>" alt="<?= htmlspecialchars($img['name'] ?? '') ?>" loading="lazy">
    <?php if (!empty($img['name'])): ?><figcaption><?= htmlspecialchars($img['name']) ?></figcaption><?php endif ?>
  </figure>
  <?php endforeach ?>
</div>
