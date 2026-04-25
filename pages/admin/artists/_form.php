<div class="space-y-4">
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
    <select name="venue_id" id="venue-select"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      <option value="">— None —</option>
      <?php foreach ($venues as $venue): ?>
      <option value="<?= $venue['id'] ?>" <?= $record['venue_id'] == $venue['id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($venue['name']) ?>
      </option>
      <?php endforeach ?>
    </select>
    <a id="venue-edit-link"
       href="/admin/venues/<?= $record['venue_id'] ?>/edit"
       class="mt-1 text-sm text-blue-600 hover:text-blue-800 inline-block <?= $record['venue_id'] ? '' : 'hidden' ?>">
      Edit this venue
    </a>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
    <select name="type"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      <?php foreach (['exhibition', 'special', 'workshop'] as $t): ?>
      <option value="<?= $t ?>" <?= $record['type'] === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
      <?php endforeach ?>
    </select>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($record['name']) ?>" required
           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Body</label>
    <div id="body-editor" class="bg-white" style="height:200px"></div>
    <textarea name="body_html" id="body-html-input" class="hidden"><?= htmlspecialchars($record['body_html'] ?? '') ?></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($record['email'] ?? '') ?>"
           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
    <input type="tel" name="phone" value="<?= htmlspecialchars($record['phone'] ?? '') ?>"
           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Disciplines <span class="text-gray-400 font-normal">(choose up to <?= FormOptions::MAX_DISCIPLINES ?>)</span></label>
    <?php $selectedDisciplines = array_filter(array_map('trim', explode(',', $record['disciplines'] ?? ''))); ?>
    <div id="disciplines-group" class="grid grid-cols-2 gap-2">
      <?php foreach (FormOptions::DISCIPLINES as $d): ?>
      <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="disciplines[]" value="<?= htmlspecialchars($d) ?>"
               <?= in_array($d, $selectedDisciplines, true) ? 'checked' : '' ?>>
        <?= htmlspecialchars($d) ?>
      </label>
      <?php endforeach ?>
    </div>
    <script>
    (function() {
      const max = <?= FormOptions::MAX_DISCIPLINES ?>;
      const boxes = document.querySelectorAll('#disciplines-group input[type=checkbox]');
      function update() {
        const checked = Array.from(boxes).filter(b => b.checked).length;
        boxes.forEach(b => { if (!b.checked) b.disabled = checked >= max; });
      }
      boxes.forEach(b => b.addEventListener('change', update));
      update();
    })();
    </script>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Short description <span class="text-gray-400 font-normal">(for printed brochure)</span></label>
    <textarea name="short_description" rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($record['short_description'] ?? '') ?></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Profile picture <span class="text-gray-400 font-normal">(optional)</span></label>
    <?php if (!empty($record['picture_id'])): ?>
    <img src="<?= cloudinary_url($record['picture_id'], 'w_160,h_160,c_fill') ?>" alt=""
         class="w-20 h-20 object-cover rounded mb-2">
    <p class="text-xs text-gray-400 mt-1">Upload a new file to replace the current picture.</p>
    <?php endif ?>
    <input type="file" name="picture_file" accept="image/*"
           class="text-sm text-gray-500 file:mr-3 file:border-0 file:rounded-md file:bg-blue-600 file:text-white file:px-3 file:py-1.5 file:text-sm file:cursor-pointer">
  </div>
  <div class="flex items-center gap-2">
    <input type="checkbox" name="approved" value="1" id="approved" <?= $record['approved'] ? 'checked' : '' ?>>
    <label for="approved" class="text-sm font-medium text-gray-700">Approved</label>
  </div>
</div>
