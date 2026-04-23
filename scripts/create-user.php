<?php

require_once __DIR__ . '/../lib/db.php';

$name = readline('Name: ');
$email = readline('Email: ');

echo 'Password: ';
system('stty -echo');
$password = trim(fgets(STDIN));
system('stty echo');
echo PHP_EOL;

$salt = bin2hex(random_bytes(32));

$db = get_db();
$stmt = $db->prepare("INSERT INTO users (name, email, password, salt) VALUES (?, ?, ?, ?)");
$stmt->execute([$name, $email, password_hash($salt . $password, PASSWORD_DEFAULT), $salt]);

echo "Created user: {$name} <{$email}>\n";
