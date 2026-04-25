<?php

use Medoo\Medoo;

function get_db(): Medoo {
    static $db = null;
    if ($db === null) {
        $dbPath = getenv('APP_DB') ?: (realpath(__DIR__ . '/../storage') . '/db.sqlite');
        $db = new Medoo([
            'type'     => 'sqlite',
            'database' => $dbPath,
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
        id                   INTEGER PRIMARY KEY AUTOINCREMENT,
        parish_id            INTEGER,
        name                 TEXT NOT NULL,
        slug                 TEXT NOT NULL UNIQUE,
        latitude             REAL,
        longitude            REAL,
        what_3_words         TEXT,
        parking              TEXT,
        refreshments         TEXT,
        dog_policy           TEXT,
        accessibility        TEXT,
        directions           TEXT,
        address              TEXT,
        venue_contact_name   TEXT,
        venue_contact_phone  TEXT
    )");

    // Migrate existing venues table
    $venueColumns = array_column(
        $db->query("PRAGMA table_info(venues)")->fetchAll(PDO::FETCH_ASSOC),
        'name'
    );
    foreach ([
        'what_3_words'        => 'TEXT',
        'parking'             => 'TEXT',
        'refreshments'        => 'TEXT',
        'accessibility'       => 'TEXT',
        'directions'          => 'TEXT',
        'address'             => 'TEXT',
        'venue_contact_name'  => 'TEXT',
        'venue_contact_phone' => 'TEXT',
        'dog_policy'          => 'TEXT',
    ] as $col => $def) {
        if (!in_array($col, $venueColumns)) {
            $db->query("ALTER TABLE venues ADD COLUMN $col $def");
        }
    }

    // Migrate dogs_allowed → dog_policy for existing rows
    if (!in_array('dog_policy', $venueColumns) && in_array('dogs_allowed', $venueColumns)) {
        $db->query("UPDATE venues SET dog_policy = CASE WHEN dogs_allowed = 1 THEN 'Dogs welcome' ELSE 'Dogs not allowed' END WHERE dog_policy IS NULL");
    }

    $db->query("CREATE TABLE IF NOT EXISTS artists (
        id                INTEGER PRIMARY KEY AUTOINCREMENT,
        venue_id          INTEGER,
        type              TEXT NOT NULL DEFAULT 'exhibition',
        name              TEXT NOT NULL,
        slug              TEXT NOT NULL UNIQUE,
        body_html         TEXT,
        email             TEXT,
        phone             TEXT,
        short_description TEXT,
        picture_id        TEXT,
        approved          INTEGER NOT NULL DEFAULT 0
    )");

    $artistColumns = array_column(
        $db->query("PRAGMA table_info(artists)")->fetchAll(PDO::FETCH_ASSOC),
        'name'
    );
    foreach ([
        'email'             => 'TEXT',
        'phone'             => 'TEXT',
        'short_description' => 'TEXT',
        'picture_id'        => 'TEXT',
        'approved'          => 'INTEGER NOT NULL DEFAULT 0',
        'disciplines'       => 'TEXT',
    ] as $col => $def) {
        if (!in_array($col, $artistColumns)) {
            $db->query("ALTER TABLE artists ADD COLUMN $col $def");
        }
    }

    $db->query("CREATE TABLE IF NOT EXISTS images (
        id        INTEGER PRIMARY KEY AUTOINCREMENT,
        artist_id INTEGER NOT NULL,
        main      INTEGER NOT NULL DEFAULT 0,
        name      TEXT,
        image_id  TEXT NOT NULL,
        featured  INTEGER NOT NULL DEFAULT 0
    )");

    $imageColumns = array_column(
        $db->query("PRAGMA table_info(images)")->fetchAll(PDO::FETCH_ASSOC),
        'name'
    );
    foreach ([
        'featured' => 'INTEGER NOT NULL DEFAULT 0',
    ] as $col => $def) {
        if (!in_array($col, $imageColumns)) {
            $db->query("ALTER TABLE images ADD COLUMN $col $def");
        }
    }

    $db->query("CREATE TABLE IF NOT EXISTS event_dates (
        id        INTEGER PRIMARY KEY AUTOINCREMENT,
        artist_id INTEGER NOT NULL,
        date      TEXT NOT NULL,
        from_time TEXT,
        to_time   TEXT
    )");
}
