<?php
$venue = $db->get('venues', '*', ['id' => $req->params['id']]);

if (!$venue) {
    http_response_code(404);
    return;
}

$parishes = $db->select('parishes', ['id', 'name'], ['ORDER' => 'name']);

if ($req->isPost()) {
    $db->update('venues', [
        'name'                => $req->params['name'],
        'slug'                => unique_slug($db, 'venues', $req->params['name'], (int) $venue['id']),
        'parish_id'           => $req->params['parish_id'] ?: null,
        'latitude'            => $req->params['latitude'] ?: null,
        'longitude'           => $req->params['longitude'] ?: null,
        'address'             => $req->params['address'] ?: null,
        'what_3_words'        => $req->params['what_3_words'] ?: null,
        'directions'          => $req->params['directions'] ?: null,
        'parking'             => $req->params['parking'] ?: null,
        'refreshments'        => $req->params['refreshments'] ?: null,
        'accessibility'       => $req->params['accessibility'] ?: null,
        'dog_policy'          => $req->params['dog_policy'] ?: null,
        'venue_contact_name'  => $req->params['venue_contact_name'] ?: null,
        'venue_contact_phone' => $req->params['venue_contact_phone'] ?: null,
    ], ['id' => $venue['id']]);
    return redirect('/admin/venues');
}

$record = $venue;
?>
<h1 class="text-2xl font-semibold text-gray-900 mb-6">Edit Venue</h1>
<form method="post" action="/admin/venues/<?= $venue['id'] ?>/edit" class="max-w-lg space-y-4">
  <?php require __DIR__ . '/../_form.php' ?>
  <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2">
    Save changes
  </button>
</form>
<div class="max-w-lg mt-8 pt-6 border-t border-gray-200">
  <form method="post" action="/admin/venues/<?= $venue['id'] ?>/delete"
        onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($venue['name'])) ?>? This cannot be undone.')">
    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete venue</button>
  </form>
</div>
