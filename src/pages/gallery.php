<?php
$pageTitle = 'Gallery';

$images = $db->query("
    SELECT images.id, images.image_id, images.name
    FROM images
    JOIN artists ON artists.id = images.artist_id
    WHERE images.featured = 1 AND artists.approved = 1
    ORDER BY images.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-grid">
<section>
  <h1>Gallery</h1>
  <?php render_partial(__DIR__ . '/_gallery.php', ['images' => $images]) ?>
</section>
</div>
