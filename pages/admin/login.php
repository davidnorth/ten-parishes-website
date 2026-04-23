<?php

require_once __DIR__ . '/../../lib/db.php';

if ($req->isPost()) {
    $stmt = get_db()->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$req->params['email'] ?? '']);
    $user = $stmt->fetch();

    if ($user && password_verify($user['salt'] . ($req->params['password'] ?? ''), $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        return redirect('/admin');
    }

    $error = 'Invalid email or password.';
}
?>
<h1>Admin Login</h1>
<?php if (isset($error)): ?>
<p><?= htmlspecialchars($error) ?></p>
<?php endif ?>
<form method="post" action="/admin/login">
  <label>Email <input type="email" name="email" autocomplete="username"></label>
  <label>Password <input type="password" name="password" autocomplete="current-password"></label>
  <button type="submit">Log in</button>
</form>
