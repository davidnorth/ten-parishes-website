<?php

$layout = 'admin';

if (!isset($_SESSION['user_id']) && $req->path !== '/admin/login') {
    return redirect('/admin/login');
}

if (isset($_SESSION['user_id'])) {
    $currentUser = $db->get('users', '*', ['id' => $_SESSION['user_id']]);
}
