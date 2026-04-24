<?php
$parishes = $db->select('parishes', '*', ['ORDER' => 'name']);
?>



<div class="page-grid">
<section>

  <h1>Parishes</h1>
  <?php foreach ($parishes as $parish): ?>
  <article class="card">
    <?php if ($parish['picture_id']): ?>
    <a href="/parishes/<?= htmlspecialchars($parish['slug']) ?>">
      <img src="<?= cloudinary_url($parish['picture_id'], 'w_400,h_300,c_fill') ?>" alt="<?= htmlspecialchars($parish['name']) ?>">
    </a>
    <?php endif ?>
    <a href="/parishes/<?= htmlspecialchars($parish['slug']) ?>"><?= htmlspecialchars($parish['name']) ?></a>
  </article>
  <?php endforeach ?>

</section>
</div>
