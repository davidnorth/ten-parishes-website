<?php $_mapId = 'map-' . bin2hex(random_bytes(4)); ?>
<div id="<?= $_mapId ?>" style="height:280px"></div>
<script>
(function () {
  const map = L.map('<?= $_mapId ?>', { scrollWheelZoom: false }).setView([<?= (float) $lat ?>, <?= (float) $lng ?>], 15);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19
  }).addTo(map);
  L.marker([<?= (float) $lat ?>, <?= (float) $lng ?>]).addTo(map);
})();
</script>
