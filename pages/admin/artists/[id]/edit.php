<?php
$artist = $db->get('artists', '*', ['id' => $req->params['id']]);

if (!$artist) {
    http_response_code(404);
    return;
}

$venues     = $db->select('venues', ['id', 'name'], ['ORDER' => 'name']);
$eventDates = $db->select('event_dates', '*', ['artist_id' => $artist['id'], 'ORDER' => ['date', 'from_time']]);
$images     = $db->select('images', '*', ['artist_id' => $artist['id']]);

if ($req->isPost()) {
    $pictureId = $artist['picture_id'];
    if (!empty($_FILES['picture_file']['tmp_name']) && is_uploaded_file($_FILES['picture_file']['tmp_name'])) {
        $pictureId = cloudinary_upload($_FILES['picture_file']['tmp_name'], $_FILES['picture_file']['name']);
    }

    $db->update('artists', [
        'venue_id'          => $req->params['venue_id'] ?: null,
        'type'              => $req->params['type'],
        'name'              => $req->params['name'],
        'slug'              => unique_slug($db, 'artists', $req->params['name'], (int) $artist['id']),
        'body_html'         => $req->params['body_html'] ?: null,
        'email'             => $req->params['email'] ?: null,
        'phone'             => $req->params['phone'] ?: null,
        'short_description' => $req->params['short_description'] ?: null,
        'picture_id'        => $pictureId,
        'approved'          => isset($req->params['approved']) ? 1 : 0,
    ], ['id' => $artist['id']]);

    // Replace event dates
    $db->delete('event_dates', ['artist_id' => $artist['id']]);
    foreach (($req->params['event_dates'] ?? []) as $ed) {
        if (!empty($ed['date'])) {
            $db->insert('event_dates', [
                'artist_id' => $artist['id'],
                'date'      => $ed['date'],
                'from_time' => $ed['from_time'] ?: null,
                'to_time'   => $ed['to_time'] ?: null,
            ]);
        }
    }

    // Delete images not in keep list
    $keepIds      = array_map('intval', $req->params['keep_image_ids'] ?? []);
    $allImageIds  = $db->select('images', ['id'], ['artist_id' => $artist['id']]);
    foreach ($allImageIds as $row) {
        if (!in_array((int) $row['id'], $keepIds)) {
            $db->delete('images', ['id' => $row['id']]);
        }
    }

    // Update main flag and name for kept images
    $mainImageId = isset($req->params['main_image']) ? (int) $req->params['main_image'] : null;
    foreach ($keepIds as $imgId) {
        $db->update('images', [
            'main' => $imgId === $mainImageId ? 1 : 0,
            'name' => $req->params['image_name'][$imgId] ?? null,
        ], ['id' => $imgId]);
    }

    // Upload new images
    if (!empty($_FILES['new_image_file']['tmp_name'])) {
        foreach ($_FILES['new_image_file']['tmp_name'] as $idx => $tmpPath) {
            if (!empty($tmpPath) && is_uploaded_file($tmpPath)) {
                $publicId = cloudinary_upload($tmpPath, $_FILES['new_image_file']['name'][$idx]);
                $db->insert('images', [
                    'artist_id' => $artist['id'],
                    'main'      => 0,
                    'name'      => $req->params['new_image_name'][$idx] ?? null,
                    'image_id'  => $publicId,
                ]);
            }
        }
    }

    return redirect('/admin/artists');
}

// Reload after potential redirect target
$eventDates = $db->select('event_dates', '*', ['artist_id' => $artist['id'], 'ORDER' => ['date', 'from_time']]);
$images     = $db->select('images', '*', ['artist_id' => $artist['id']]);
?>
<h1 class="text-2xl font-semibold text-gray-900 mb-6">Edit Artist</h1>
<form id="artist-form" method="post" action="/admin/artists/<?= $artist['id'] ?>/edit" enctype="multipart/form-data" class="max-w-2xl space-y-6">

  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
      <select name="venue_id" id="venue-select"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <option value="">— None —</option>
        <?php foreach ($venues as $venue): ?>
        <option value="<?= $venue['id'] ?>" <?= $artist['venue_id'] == $venue['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($venue['name']) ?>
        </option>
        <?php endforeach ?>
      </select>
      <a id="venue-edit-link"
         href="/admin/venues/<?= $artist['venue_id'] ?>/edit"
         class="mt-1 text-sm text-blue-600 hover:text-blue-800 inline-block <?= $artist['venue_id'] ? '' : 'hidden' ?>">
        Edit this venue
      </a>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
      <select name="type"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <?php foreach (['exhibition', 'special', 'workshop'] as $t): ?>
        <option value="<?= $t ?>" <?= $artist['type'] === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
        <?php endforeach ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
      <input type="text" name="name" value="<?= htmlspecialchars($artist['name']) ?>" required
             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Body</label>
      <div id="body-editor" class="bg-white" style="height:200px"></div>
      <textarea name="body_html" id="body-html-input" class="hidden"><?= htmlspecialchars($artist['body_html'] ?? '') ?></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($artist['email'] ?? '') ?>"
             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
      <input type="tel" name="phone" value="<?= htmlspecialchars($artist['phone'] ?? '') ?>"
             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Short description (for use in printed brochure)</label>
      <textarea name="short_description" rows="3"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($artist['short_description'] ?? '') ?></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Profile picture <span class="text-gray-400 font-normal">(optional)</span></label>
      <?php if ($artist['picture_id']): ?>
      <img src="<?= cloudinary_url($artist['picture_id'], 'w_160,h_160,c_fill') ?>" alt=""
           class="w-20 h-20 object-cover rounded mb-2">
      <?php endif ?>
      <input type="file" name="picture_file" accept="image/*"
             class="text-sm text-gray-500 file:mr-3 file:border-0 file:rounded-md file:bg-blue-600 file:text-white file:px-3 file:py-1.5 file:text-sm file:cursor-pointer">
      <?php if ($artist['picture_id']): ?>
      <p class="text-xs text-gray-400 mt-1">Upload a new file to replace the current picture.</p>
      <?php endif ?>
    </div>
    <div class="flex items-center gap-2">
      <input type="checkbox" name="approved" value="1" id="approved" <?= $artist['approved'] ? 'checked' : '' ?>>
      <label for="approved" class="text-sm font-medium text-gray-700">Approved</label>
    </div>
  </div>

  <!-- Event Dates -->
  <div class="pt-4 border-t border-gray-200">
    <h2 class="text-base font-semibold text-gray-900 mb-3">Event Dates</h2>
    <div id="event-dates" class="space-y-2">
      <?php foreach ($eventDates as $i => $ed): ?>
      <div class="event-date-row flex gap-2 items-center">
        <input type="date" name="event_dates[<?= $i ?>][date]" value="<?= htmlspecialchars($ed['date']) ?>"
               class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <input type="time" name="event_dates[<?= $i ?>][from_time]" value="<?= htmlspecialchars($ed['from_time'] ?? '') ?>"
               class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <span class="text-gray-400 text-sm">to</span>
        <input type="time" name="event_dates[<?= $i ?>][to_time]" value="<?= htmlspecialchars($ed['to_time'] ?? '') ?>"
               class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <button type="button" onclick="this.closest('.event-date-row').remove()"
                class="text-sm text-red-600 hover:text-red-800 ml-1">Remove</button>
      </div>
      <?php endforeach ?>
    </div>
    <button type="button" onclick="addEventDate()"
            class="mt-2 text-sm text-blue-600 hover:text-blue-800">+ Add date</button>
  </div>

  <!-- Images -->
  <div class="pt-4 border-t border-gray-200">
    <h2 class="text-base font-semibold text-gray-900 mb-3">Images</h2>
    <div id="images" class="space-y-3">
      <?php foreach ($images as $img): ?>
      <div class="image-row flex gap-3 items-start border border-gray-200 rounded-md p-3">
        <img src="<?= cloudinary_url($img['image_id'], 'w_160,h_100,c_fill') ?>"
             alt="" class="w-20 h-14 object-cover rounded flex-shrink-0">
        <div class="flex-1 space-y-1">
          <input type="hidden" name="keep_image_ids[]" value="<?= $img['id'] ?>">
          <input type="text" name="image_name[<?= $img['id'] ?>]" value="<?= htmlspecialchars($img['name'] ?? '') ?>"
                 placeholder="Caption"
                 class="w-full border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          <label class="flex items-center gap-1.5 text-sm text-gray-600 cursor-pointer">
            <input type="radio" name="main_image" value="<?= $img['id'] ?>" <?= $img['main'] ? 'checked' : '' ?>>
            Main image
          </label>
        </div>
        <button type="button" onclick="this.closest('.image-row').remove()"
                class="text-sm text-red-600 hover:text-red-800 flex-shrink-0">Delete</button>
      </div>
      <?php endforeach ?>
    </div>
    <div id="new-images" class="space-y-2 mt-3"></div>
    <button type="button" onclick="addImage()"
            class="mt-2 text-sm text-blue-600 hover:text-blue-800">+ Add image</button>
  </div>

  <div class="pt-2">
    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2">
      Save changes
    </button>
  </div>
</form>

<div class="max-w-2xl mt-8 pt-6 border-t border-gray-200">
  <form method="post" action="/admin/artists/<?= $artist['id'] ?>/delete"
        onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($artist['name'])) ?>? This cannot be undone.')">
    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete artist</button>
  </form>
</div>

<script>
const quill = new Quill('#body-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            ['bold', 'italic'],
            [{ header: [2, 3, false] }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['link'],
            ['clean'],
        ]
    }
});
const existingBody = document.getElementById('body-html-input').value;
if (existingBody) quill.clipboard.dangerouslyPasteHTML(existingBody);
document.getElementById('artist-form').addEventListener('submit', () => {
    document.getElementById('body-html-input').value = quill.root.innerHTML;
});

let eventDateIndex = <?= count($eventDates) ?>;
function addEventDate() {
    const idx = eventDateIndex++;
    const row = document.createElement('div');
    row.className = 'event-date-row flex gap-2 items-center';
    row.innerHTML = `
        <input type="date" name="event_dates[${idx}][date]"
               class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <input type="time" name="event_dates[${idx}][from_time]"
               class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <span class="text-gray-400 text-sm">to</span>
        <input type="time" name="event_dates[${idx}][to_time]"
               class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <button type="button" onclick="this.closest('.event-date-row').remove()"
                class="text-sm text-red-600 hover:text-red-800 ml-1">Remove</button>
    `;
    document.getElementById('event-dates').appendChild(row);
}

const venueSelect = document.getElementById('venue-select');
const venueLink = document.getElementById('venue-edit-link');
venueSelect.addEventListener('change', () => {
    const id = venueSelect.value;
    if (id) {
        venueLink.href = `/admin/venues/${id}/edit`;
        venueLink.classList.remove('hidden');
    } else {
        venueLink.classList.add('hidden');
    }
});

let imageIndex = 0;
function addImage() {
    const idx = imageIndex++;
    const row = document.createElement('div');
    row.className = 'new-image-row flex gap-2 items-center border border-gray-200 rounded-md p-3';
    row.innerHTML = `
        <input type="file" name="new_image_file[${idx}]" accept="image/*"
               class="text-sm text-gray-500 file:mr-3 file:border-0 file:rounded-md file:bg-blue-600 file:text-white file:px-3 file:py-1.5 file:text-sm file:cursor-pointer flex-1">
        <input type="text" name="new_image_name[${idx}]" placeholder="Caption"
               class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-40">
        <button type="button" onclick="this.closest('.new-image-row').remove()"
                class="text-sm text-red-600 hover:text-red-800 flex-shrink-0">Remove</button>
    `;
    document.getElementById('new-images').appendChild(row);
}
</script>
