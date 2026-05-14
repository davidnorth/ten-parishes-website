<?php

/**
 * Geocode a free-text query (postcode, address, place name) to lat/lng using
 * Nominatim. UK-biased. Returns ['lat' => float, 'lng' => float] or null.
 *
 * Server-side caller; Nominatim usage policy requires a User-Agent.
 */
function geocode(string $query): ?array {
    $query = trim($query);
    if ($query === '') return null;

    $url = 'https://nominatim.openstreetmap.org/search?'
        . http_build_query([
            'q'            => $query,
            'format'       => 'json',
            'limit'        => 1,
            'countrycodes' => 'gb',
        ]);

    $ctx = stream_context_create(['http' => [
        'method'  => 'GET',
        'header'  => "User-Agent: TenParishesFestival/1.0 (https://tenparishes.org)\r\n",
        'timeout' => 5,
    ]]);

    $body = @file_get_contents($url, false, $ctx);
    if ($body === false) return null;

    $results = json_decode($body, true);
    if (!is_array($results) || empty($results[0]['lat']) || empty($results[0]['lon'])) {
        return null;
    }

    return [
        'lat' => (float) $results[0]['lat'],
        'lng' => (float) $results[0]['lon'],
    ];
}
