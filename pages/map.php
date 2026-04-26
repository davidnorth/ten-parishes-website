<?php
$pageTitle = 'Map';
$venues = $db->select('venues', ['id', 'name', 'latitude', 'longitude'], [
    'latitude[!]'  => null,
    'longitude[!]' => null,
    'ORDER'        => 'name',
]);

$allArtists = $db->select('artists', ['venue_id', 'name', 'slug'], [
    'approved'      => 1,
    'venue_id[!]'   => null,
]);

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

<nav class="icon-tabs">
  <a href="/artists">
    <iconify-icon icon="ph:list" class="icon-medium" aria-hidden="true"> </iconify-icon>
    <span>List view</span>
  </a>
  <a href="/map" class="current">
    <iconify-icon icon="ph:map-pin" class="icon-medium" aria-hidden="true" /></iconify-icon>
    <span>Map view</span>
  </a>
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
    const count = v.artists.length;
    const icon = L.divIcon({
        html: `<div style="background:#387FBA;color:#fff;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:bold;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.4)">${count}</div>`,
        className: '',
        iconSize: [28, 28],
        iconAnchor: [14, 14],
        popupAnchor: [0, -14],
    });
    const marker = L.marker([v.lat, v.lng], { icon }).addTo(map);
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

