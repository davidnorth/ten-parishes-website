<?php

$layout = 'admin';

if (!isset($_SESSION['user_id'])) {
    if ($req->path !== '/admin/login') {
        return redirect('/admin/login');
    }
} else {
    require_once __DIR__ . '/../../lib/db.php';
    $stmt = get_db()->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch() ?: null;
}
