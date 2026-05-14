<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
  <div class="flex gap-2 mb-2">
    <input type="text" id="location-search" placeholder="Search for a place…"
           class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    <button class="small" type="button" id="location-search-btn"
            class="border border-gray-300 rounded-md px-3 py-2 text-sm hover:bg-gray-100">Search</button>
  </div>
  <div id="location-map" class="w-full rounded-md border border-gray-300" style="height:280px"></div>
  <input type="hidden" name="latitude" id="location-lat" value="<?= htmlspecialchars($locationLat ?? '') ?>">
  <input type="hidden" name="longitude" id="location-lng" value="<?= htmlspecialchars($locationLng ?? '') ?>">
</div>
<script>
(function () {
  const initLat = <?= json_encode($locationLat ? (float)$locationLat : null) ?>;
  const initLng = <?= json_encode($locationLng ? (float)$locationLng : null) ?>;

  const map = L.map('location-map').setView(
    (initLat && initLng) ? [initLat, initLng] : [51.12, -3.53],
    (initLat && initLng) ? 14 : 11
  );

  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19
  }).addTo(map);

  const latInput = document.getElementById('location-lat');
  const lngInput = document.getElementById('location-lng');
  let marker = null;

  function placeMarker(lat, lng) {
    if (marker) {
      marker.setLatLng([lat, lng]);
    } else {
      marker = L.marker([lat, lng]).addTo(map);
    }
    latInput.value = lat.toFixed(6);
    lngInput.value = lng.toFixed(6);
  }

  if (initLat && initLng) placeMarker(initLat, initLng);

  map.on('click', e => placeMarker(e.latlng.lat, e.latlng.lng));

  function search() {
    const q = document.getElementById('location-search').value.trim();
    if (!q) return;
    fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(q) + '&format=json&limit=1')
      .then(r => r.json())
      .then(results => {
        if (!results.length) return;
        const lat = parseFloat(results[0].lat);
        const lng = parseFloat(results[0].lon);
        placeMarker(lat, lng);
        map.setView([lat, lng], 15);
      });
  }

  document.getElementById('location-search-btn').addEventListener('click', search);
  document.getElementById('location-search').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); search(); }
  });
})();
</script>
