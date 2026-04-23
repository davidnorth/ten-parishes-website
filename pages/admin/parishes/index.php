<?php
$parishes = $db->select('parishes', '*');
?>
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-semibold text-gray-900">Parishes</h1>
  <a href="/admin/parishes/new" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2">Add parish</a>
</div>
<table class="w-full text-sm">
  <thead>
    <tr class="border-b border-gray-200 text-left text-gray-500">
      <th class="pb-2 font-medium">Name</th>
      <th class="pb-2 font-medium">Slug</th>
      <th class="pb-2"></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($parishes as $parish): ?>
    <tr class="border-b border-gray-100">
      <td class="py-2"><?= htmlspecialchars($parish['name']) ?></td>
      <td class="py-2 text-gray-500"><?= htmlspecialchars($parish['slug']) ?></td>
      <td class="py-2 text-right">
        <a href="/admin/parishes/<?= $parish['id'] ?>/edit" class="text-blue-600 hover:underline">Edit</a>
      </td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>
