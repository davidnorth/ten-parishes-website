<?php
// Resets the test database and seeds one admin user for E2E tests.
// Called by Playwright's globalSetup — expects APP_DB env var to be set.

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../lib/db.php';

$testDbPath = getenv('APP_DB');
if (!$testDbPath) {
    fwrite(STDERR, "APP_DB env var is required.\n");
    exit(1);
}

if (file_exists($testDbPath)) {
    unlink($testDbPath);
}

$db = get_db();

$salt = bin2hex(random_bytes(16));
$db->insert('users', [
    'name'     => 'Test Admin',
    'email'    => 'admin@test.local',
    'password' => password_hash($salt . 'testpass123', PASSWORD_DEFAULT),
    'salt'     => $salt,
]);

echo "Test database ready. Admin: admin@test.local / testpass123\n";
