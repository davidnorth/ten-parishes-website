<?php
$venues = $db->select('venues', ['id', 'name'], ['ORDER' => 'name']);

if ($req->isPost()) {
    $pictureId = null;
    if (!empty($_FILES['picture_file']['tmp_name']) && is_uploaded_file($_FILES['picture_file']['tmp_name'])) {
        $pictureId = cloudinary_upload($_FILES['picture_file']['tmp_name'], $_FILES['picture_file']['name']);
    }

    $db->insert('artists', [
        'venue_id'          => $req->params['venue_id'] ?: null,
        'type'              => $req->params['type'],
        'name'              => $req->params['name'],
        'slug'              => unique_slug($db, 'artists', $req->params['name']),
        'body_html'         => $req->params['body_html'] ?: null,
        'email'             => $req->params['email'] ?: null,
        'phone'             => $req->params['phone'] ?: null,
        'short_description' => $req->params['short_description'] ?: null,
        'picture_id'        => $pictureId,
        'approved'          => isset($req->params['approved']) ? 1 : 0,
    ]);
    $id = $db->id();

    foreach (($req->params['event_dates'] ?? []) as $ed) {
        if (!empty($ed['date'])) {
            $db->insert('event_dates', [
                'artist_id' => $id,
                'date'      => $ed['date'],
                'from_time' => $ed['from_time'] ?: null,
                'to_time'   => $ed['to_time'] ?: null,
            ]);
        }
    }

    if (!empty($_FILES['new_image_file']['tmp_name'])) {
        foreach ($_FILES['new_image_file']['tmp_name'] as $idx => $tmpPath) {
            if (!empty($tmpPath) && is_uploaded_file($tmpPath)) {
                $publicId = cloudinary_upload($tmpPath, $_FILES['new_image_file']['name'][$idx]);
                $db->insert('images', [
                    'artist_id' => $id,
                    'main'      => 0,
                    'name'      => $req->params['new_image_name'][$idx] ?? null,
                    'image_id'  => $publicId,
                ]);
            }
        }
    }

    return redirect('/admin/artists');
}
?>
<h1 class="text-2xl font-semibold text-gray-900 mb-6">New Artist</h1>
<form id="artist-form" method="post" action="/admin/artists/new" enctype="multipart/form-data" class="max-w-2xl space-y-6">

  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
      <select name="venue_id"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <option value="">— None —</option>
        <?php foreach ($venues as $venue): ?>
        <option value="<?= $venue['id'] ?>"><?= htmlspecialchars($venue['name']) ?></option>
        <?php endforeach ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
      <select name="type"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <option value="exhibition">Exhibition</option>
        <option value="special">Special</option>
        <option value="workshop">Workshop</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
      <input type="text" name="name" required
             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Body</label>
      <div id="body-editor" class="bg-white" style="height:200px"></div>
      <textarea name="body_html" id="body-html-input" class="hidden"></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
      <input type="email" name="email"
             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
      <input type="tel" name="phone"
             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Short description</label>
      <textarea name="short_description" rows="3"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Profile picture <span class="text-gray-400 font-normal">(optional)</span></label>
      <input type="file" name="picture_file" accept="image/*"
             class="text-sm text-gray-500 file:mr-3 file:border-0 file:rounded-md file:bg-blue-600 file:text-white file:px-3 file:py-1.5 file:text-sm file:cursor-pointer">
    </div>
    <div class="flex items-center gap-2">
      <input type="checkbox" name="approved" value="1" id="approved">
      <label for="approved" class="text-sm font-medium text-gray-700">Approved</label>
    </div>
  </div>

  <!-- Event Dates -->
  <div class="pt-4 border-t border-gray-200">
    <h2 class="text-base font-semibold text-gray-900 mb-3">Event Dates</h2>
    <div id="event-dates" class="space-y-2"></div>
    <button type="button" onclick="addEventDate()"
            class="mt-2 text-sm text-blue-600 hover:text-blue-800">+ Add date</button>
  </div>

  <!-- Images -->
  <div class="pt-4 border-t border-gray-200">
    <h2 class="text-base font-semibold text-gray-900 mb-3">Images</h2>
    <div id="new-images" class="space-y-2"></div>
    <button type="button" onclick="addImage()"
            class="mt-2 text-sm text-blue-600 hover:text-blue-800">+ Add image</button>
  </div>

  <div class="pt-2">
    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2">
      Create artist
    </button>
  </div>
</form>

<script>
let eventDateIndex = 0;
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
document.getElementById('artist-form').addEventListener('submit', () => {
    document.getElementById('body-html-input').value = quill.root.innerHTML;
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
