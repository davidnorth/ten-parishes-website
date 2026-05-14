<?php

if ($req->isPost()) {
    $user = $db->get('users', '*', ['email' => $req->params['email'] ?? '']);

    if ($user && password_verify($user['salt'] . ($req->params['password'] ?? ''), $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        return redirect('/admin');
    }

    $error = 'Invalid email or password.';
}
?>
<div class="max-w-sm">
  <h1 class="text-2xl font-semibold text-gray-900 mb-6">Log in</h1>
  <?php if (isset($error)): ?>
  <p class="text-sm text-red-600 mb-4"><?= htmlspecialchars($error) ?></p>
  <?php endif ?>
  <form method="post" action="/admin/login" class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
      <input type="email" name="email" autocomplete="username"
             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
      <input type="password" name="password" autocomplete="current-password"
             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2">
      Log in
    </button>
  </form>
</div>
