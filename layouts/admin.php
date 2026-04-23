<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ten Parishes — Admin</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="flex h-screen bg-gray-50 text-gray-900">

  <aside class="w-56 bg-gray-900 text-gray-100 flex flex-col shrink-0">
    <div class="px-6 py-5 font-semibold text-white border-b border-gray-700">
      Ten Parishes
    </div>
    <nav class="flex-1 py-3">
      <?php
      $navLinks = [
        '/admin'          => 'Admin Home',
        '/admin/artists'  => 'Artists',
        '/admin/venues'   => 'Venues',
        '/admin/parishes' => 'Parishes',
      ];
      foreach ($navLinks as $href => $label):
        $active = $href === '/admin'
          ? rtrim($req->path, '/') === '/admin'
          : str_starts_with($req->path, $href);
        $cls = $active
          ? 'bg-gray-700 text-white'
          : 'text-gray-400 hover:bg-gray-800 hover:text-white';
      ?>
        <a href="<?= $href ?>" class="block px-6 py-2 text-sm <?= $cls ?>"><?= $label ?></a>
      <?php endforeach ?>
    </nav>
    <?php if (isset($currentUser)): ?>
    <div class="px-6 py-4 border-t border-gray-700 text-sm">
      <div class="text-gray-300 mb-1"><?= htmlspecialchars($currentUser['name']) ?></div>
      <a href="/admin/logout" class="text-gray-500 hover:text-gray-300">Log out</a>
    </div>
    <?php endif ?>
  </aside>

  <main class="flex-1 overflow-auto p-8">
    <?= $content ?>
  </main>

</body>
</html>
