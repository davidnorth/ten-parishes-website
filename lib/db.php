<?php

use Medoo\Medoo;

function get_db(): Medoo {
    static $db = null;
    if ($db === null) {
        $db = new Medoo([
            'type' => 'sqlite',
            'database' => realpath(__DIR__ . '/../storage') . '/db.sqlite',
        ]);
        $db->query("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            salt TEXT NOT NULL
        )");
    }
    return $db;
}
