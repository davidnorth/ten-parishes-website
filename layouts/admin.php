<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Ten Parishes — Admin</title></head>
<body>
<header><strong>Admin</strong> <?= htmlspecialchars($currentUser['name'] ?? '') ?> <a href="/admin/logout">Log out</a></header>
<?= $content ?>
</body>
</html>
