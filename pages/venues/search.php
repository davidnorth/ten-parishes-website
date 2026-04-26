<?php
use Medoo\Medoo;

$layout = null;
header('Content-Type: application/json');

$postcode = trim($req->params['postcode'] ?? '');
if ($postcode === '') {
    echo json_encode(['error' => 'Please enter a postcode.', 'venues' => []]);
    return;
}

$origin = geocode($postcode);
if ($origin === null) {
    echo json_encode([
        'error'  => "Couldn't find that postcode. Try again or create a new venue.",
        'venues' => [],
    ]);
    return;
}

// ~5km bounding box. 1 deg latitude ≈ 111km; longitude scales with cos(lat).
// Rural postcode centroids can sit a few km from the actual addresses.
$radiusKm = 5;
$latDelta = $radiusKm / 111;
$lngDelta = $radiusKm / (111 * max(0.1, cos(deg2rad($origin['lat']))));

// Medoo binds numeric BETWEEN values as PDO::PARAM_INT (truncating our floats),
// so we hand the bounds in as Medoo::raw() literals instead.
$latMin = (float) ($origin['lat'] - $latDelta);
$latMax = (float) ($origin['lat'] + $latDelta);
$lngMin = (float) ($origin['lng'] - $lngDelta);
$lngMax = (float) ($origin['lng'] + $lngDelta);

$rows = $db->select('venues', [
    '[>]parishes' => ['parish_id' => 'id'],
], [
    'venues.id',
    'venues.name',
    'venues.address',
    'venues.latitude',
    'venues.longitude',
    'parishes.name(parish_name)',
], [
    'venues.latitude[<>]'  => [Medoo::raw($latMin), Medoo::raw($latMax)],
    'venues.longitude[<>]' => [Medoo::raw($lngMin), Medoo::raw($lngMax)],
]);

// Sort by approximate distance (squared, no sqrt needed for ordering)
usort($rows, function ($a, $b) use ($origin) {
    $da = ($a['latitude'] - $origin['lat']) ** 2 + ($a['longitude'] - $origin['lng']) ** 2;
    $db = ($b['latitude'] - $origin['lat']) ** 2 + ($b['longitude'] - $origin['lng']) ** 2;
    return $da <=> $db;
});

echo json_encode([
    'venues' => array_map(fn($r) => [
        'id'      => (int) $r['id'],
        'name'    => $r['name'],
        'address' => $r['address'],
        'parish'  => $r['parish_name'],
    ], $rows),
]);
