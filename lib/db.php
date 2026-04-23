<?php

function get_db(): PDO {
    static $db = null;
    if ($db === null) {
        $path = realpath(__DIR__ . '/../storage') . '/db.sqlite';
        $db = new PDO('sqlite:' . $path);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        init_schema($db);
    }
    return $db;
}

function init_schema(PDO $db): void {
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        salt TEXT NOT NULL
    )");
}
