<?php
$parishes = $db->select('parishes', ['id', 'name'], ['ORDER' => 'name']);

if ($req->isPost()) {
    $db->insert('venues', [
        'name'                => $req->params['name'],
        'slug'                => unique_slug($db, 'venues', $req->params['name']),
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
    ]);
    return redirect('/admin/venues');
}

$record = [];
?>
<h1 class="text-2xl font-semibold text-gray-900 mb-6">New Venue</h1>
<form method="post" action="/admin/venues/new" class="max-w-lg space-y-4">
  <?php require __DIR__ . '/_form.php' ?>
  <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2">
    Create venue
  </button>
</form>
