<?php
if ($req->isPost()) {
    $db->insert('artists', [
        'type'     => 'exhibition',
        'name'     => $req->params['name'],
        'slug'     => unique_slug($db, 'artists', $req->params['name']),
        'approved' => 0,
    ]);
    return redirect('/admin/artists/' . $db->id() . '/edit');
}
?>
<h1 class="text-2xl font-semibold text-gray-900 mb-6">New Artist</h1>
<p class="text-sm text-gray-600 mb-4">Artists usually self-register through the public form. Enter a name to create a stub — you'll fill in the rest on the next screen.</p>
<form method="post" action="/admin/artists/new" class="max-w-lg space-y-4">
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
    <input type="text" name="name" required autofocus
           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
  </div>
  <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2">
    Create
  </button>
</form>
