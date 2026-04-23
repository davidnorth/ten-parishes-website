<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../lib/db.php';

$db = get_db();

$name = readline('Name: ');
$email = readline('Email: ');

echo 'Password: ';
system('stty -echo');
$password = trim(fgets(STDIN));
system('stty echo');
echo PHP_EOL;

$salt = bin2hex(random_bytes(32));

$db->insert('users', [
    'name' => $name,
    'email' => $email,
    'password' => password_hash($salt . $password, PASSWORD_DEFAULT),
    'salt' => $salt,
]);

echo "Created user: {$name} <{$email}>\n";
