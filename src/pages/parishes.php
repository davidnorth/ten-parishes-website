<?php
$pageTitle = 'Parishes';
$parishes = $db->select('parishes', '*', ['ORDER' => 'name']);
?>



<div class="page-grid">
<section>

  <h1>Parishes</h1>
  <?php foreach ($parishes as $parish): ?>
  <article class="card">
    <?php if ($parish['picture_id']): ?>
    <a href="/parishes/<?= htmlspecialchars($parish['slug']) ?>">
      <?= cloudinary_image($parish['picture_id'], 400, 300, $parish['name']) ?>
    </a>
    <?php endif ?>
    <a href="/parishes/<?= htmlspecialchars($parish['slug']) ?>"><?= htmlspecialchars($parish['name']) ?></a>
  </article>
  <?php endforeach ?>

</section>
</div>
