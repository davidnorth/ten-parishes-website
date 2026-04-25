<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
  <input type="text" name="name" value="<?= htmlspecialchars($record['name'] ?? '') ?>" required
         class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
</div>
<?php $locationLat = $record['latitude'] ?? null; $locationLng = $record['longitude'] ?? null; require __DIR__ . '/../_location_picker.php'; ?>
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
  <?php if (!empty($record['picture_id'])): ?>
  <img src="<?= cloudinary_url($record['picture_id'], 'w_400,h_200,c_fill') ?>"
       alt="" class="w-full rounded-md mb-2 object-cover">
  <?php endif ?>
  <input type="file" name="image" accept="image/*"
         class="text-sm text-gray-500 file:mr-3 file:border-0 file:rounded-md file:bg-blue-600 file:text-white file:px-3 file:py-1.5 file:text-sm file:cursor-pointer">
</div>
