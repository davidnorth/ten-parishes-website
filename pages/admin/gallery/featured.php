<?php
$layout = null;

$images = $db->query("
    SELECT images.id, images.image_id, images.name, artists.name AS artist_name
    FROM images
    JOIN artists ON artists.id = images.artist_id
    WHERE images.featured = 1
    ORDER BY images.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php if (!$images): ?>
  <p class="text-sm text-gray-500 col-span-3">No featured images yet.</p>
<?php else: ?>
  <?php foreach ($images as $img): ?>
    <button type="button" data-image-id="<?= (int) $img['id'] ?>"
            class="block aspect-square overflow-hidden rounded-md border border-gray-200 hover:ring-2 hover:ring-red-500 focus:outline-none"
            title="Remove from featured: <?= htmlspecialchars(trim(($img['name'] ?? '') . ' — ' . $img['artist_name'], ' —')) ?>">
      <img src="<?= cloudinary_url($img['image_id'], 'w_200,h_200,c_fill') ?>" alt="" class="w-full h-full object-cover">
    </button>
  <?php endforeach ?>
<?php endif ?>
