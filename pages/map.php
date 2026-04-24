<?php
$pageTitle = 'Map';
$venues = $db->select('venues', ['id', 'name', 'latitude', 'longitude'], [
    'latitude[!]'  => null,
    'longitude[!]' => null,
    'ORDER'        => 'name',
]);

$allArtists = $db->select('artists', ['venue_id', 'name', 'slug']);

$artistsByVenue = [];
foreach ($allArtists as $a) {
    $artistsByVenue[$a['venue_id']][] = ['name' => $a['name'], 'slug' => $a['slug']];
}

$mapData = [];
foreach ($venues as $v) {
    $mapData[] = [
        'name'    => $v['name'],
        'lat'     => (float) $v['latitude'],
        'lng'     => (float) $v['longitude'],
        'artists' => $artistsByVenue[$v['id']] ?? [],
    ];
}
?>

<div class="page-grid">
<section>

<h1>Artists</h1>

<nav>
  <a href="/artists">List view</a>
  <a href="/map">Map view</a>
</nav>


<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<div id="map" style="height:600px"></div>


</section>
</div>





<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const venues = <?= json_encode($mapData, JSON_HEX_TAG) ?>;
const map = L.map('map');
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

const bounds = [];
venues.forEach(v => {
    const marker = L.marker([v.lat, v.lng]).addTo(map);
    bounds.push([v.lat, v.lng]);
    let html = '<strong>' + v.name + '</strong>';
    if (v.artists.length) {
        html += '<ul>' + v.artists.map(a =>
            '<li><a href="/artists/' + a.slug + '">' + a.name + '</a></li>'
        ).join('') + '</ul>';
    }
    marker.bindPopup(html);
});

if (bounds.length > 0) {
    map.fitBounds(bounds, {padding: [20, 20]});
} else {
    map.setView([51.0, -3.5], 12);
}
</script>

