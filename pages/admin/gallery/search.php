<?php
$layout = null;

$q = trim($req->params['q'] ?? '');

if ($q !== '') {
    $like = '%' . $q . '%';
    $images = $db->query("
        SELECT images.id, images.image_id, images.name, images.featured, artists.name AS artist_name
        FROM images
        JOIN artists ON artists.id = images.artist_id
        WHERE images.name LIKE :q1 OR artists.name LIKE :q2
        ORDER BY artists.name, images.id
        LIMIT 60
    ", [':q1' => $like, ':q2' => $like])->fetchAll(PDO::FETCH_ASSOC);
} else {
    $images = $db->query("
        SELECT images.id, images.image_id, images.name, images.featured, artists.name AS artist_name
        FROM images
        JOIN artists ON artists.id = images.artist_id
        ORDER BY images.id DESC
        LIMIT 60
    ")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php if (!$images): ?>
  <p class="text-sm text-gray-500 col-span-3">No images match.</p>
<?php else: ?>
  <?php foreach ($images as $img): ?>
    <button type="button" data-image-id="<?= (int) $img['id'] ?>"
            class="relative block aspect-square overflow-hidden rounded-md border border-gray-200 hover:ring-2 hover:ring-blue-500 focus:outline-none"
            title="<?= htmlspecialchars(trim(($img['name'] ?? '') . ' — ' . $img['artist_name'], ' —')) ?>">
      <img src="<?= cloudinary_url($img['image_id'], 'w_200,h_200,c_fill') ?>" alt="" class="w-full h-full object-cover">
      <?php if ($img['featured']): ?>
        <span class="absolute top-1 right-1 bg-green-600 text-white text-xs font-medium px-1.5 py-0.5 rounded">★</span>
      <?php endif ?>
    </button>
  <?php endforeach ?>
<?php endif ?>
