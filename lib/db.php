<?php

use Medoo\Medoo;

function get_db(): Medoo {
    static $db = null;
    if ($db === null) {
        $db = new Medoo([
            'type'     => 'sqlite',
            'database' => realpath(__DIR__ . '/../storage') . '/db.sqlite',
        ]);
        init_schema($db);
    }
    return $db;
}

function init_schema(Medoo $db): void {
    $db->query("CREATE TABLE IF NOT EXISTS users (
        id       INTEGER PRIMARY KEY AUTOINCREMENT,
        name     TEXT NOT NULL,
        email    TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        salt     TEXT NOT NULL
    )");

    $db->query("CREATE TABLE IF NOT EXISTS parishes (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        name       TEXT NOT NULL,
        slug       TEXT NOT NULL UNIQUE,
        latitude   REAL,
        longitude  REAL,
        picture_id TEXT
    )");

    $db->query("CREATE TABLE IF NOT EXISTS venues (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        parish_id  INTEGER,
        name       TEXT NOT NULL,
        slug       TEXT NOT NULL UNIQUE,
        latitude   REAL,
        longitude  REAL,
        picture_id TEXT
    )");
}
