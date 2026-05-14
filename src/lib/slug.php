<?php

function slugify(string $text): string {
    $text = mb_strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function unique_slug(\Medoo\Medoo $db, string $table, string $text, ?int $excludeId = null): string {
    $base = slugify($text);
    $slug = $base;
    $i = 2;
    while (true) {
        $where = ['slug' => $slug];
        if ($excludeId !== null) {
            $where['id[!]'] = $excludeId;
        }
        if (!$db->has($table, $where)) {
            break;
        }
        $slug = $base . '-' . $i++;
    }
    return $slug;
}
