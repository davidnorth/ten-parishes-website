<?php
$artists = $db->query("
    SELECT artists.id, artists.name, artists.type, artists.slug,
           parishes.name AS parish_name
    FROM artists
    LEFT JOIN venues ON artists.venue_id = venues.id
    LEFT JOIN parishes ON venues.parish_id = parishes.id
    ORDER BY artists.name
")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-semibold text-gray-900">Artists</h1>
  <a href="/admin/artists/new" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2">Add artist</a>
</div>
<table class="w-full text-sm">
  <thead>
    <tr class="border-b border-gray-200 text-left text-gray-500">
      <th class="pb-2 font-medium">Name</th>
      <th class="pb-2 font-medium">Parish</th>
      <th class="pb-2 font-medium">Type</th>
      <th class="pb-2 font-medium">Slug</th>
      <th class="pb-2"></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($artists as $artist): ?>
    <tr class="border-b border-gray-100">
      <td class="py-2"><?= htmlspecialchars($artist['name']) ?></td>
      <td class="py-2 text-gray-500"><?= htmlspecialchars($artist['parish_name'] ?? '—') ?></td>
      <td class="py-2 text-gray-500"><?= htmlspecialchars($artist['type']) ?></td>
      <td class="py-2 text-gray-500"><?= htmlspecialchars($artist['slug']) ?></td>
      <td class="py-2 text-right">
        <a href="/admin/artists/<?= $artist['id'] ?>/edit" class="text-blue-600 hover:underline">Edit</a>
      </td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>
