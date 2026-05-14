<h1 class="text-2xl font-semibold text-gray-900 mb-6">Gallery</h1>

<div class="grid grid-cols-2 gap-6">
  <section>
    <h2 class="text-lg font-medium text-gray-700 mb-3">Featured</h2>
    <div id="featured-pane" class="grid grid-cols-3 gap-2">
      <p class="text-sm text-gray-500">Loading…</p>
    </div>
  </section>

  <section>
    <h2 class="text-lg font-medium text-gray-700 mb-3">All images</h2>
    <input type="search" id="gallery-search" placeholder="Search by image or artist name"
           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-3">
    <div id="search-pane" class="grid grid-cols-3 gap-2">
      <p class="text-sm text-gray-500">Loading…</p>
    </div>
  </section>
</div>

<script>
const featuredPane = document.getElementById('featured-pane');
const searchPane = document.getElementById('search-pane');
const searchInput = document.getElementById('gallery-search');

async function loadFeatured() {
  const res = await fetch('/admin/gallery/featured');
  featuredPane.innerHTML = await res.text();
}

async function loadSearch() {
  const q = encodeURIComponent(searchInput.value);
  const res = await fetch('/admin/gallery/search?q=' + q);
  searchPane.innerHTML = await res.text();
}

async function toggleFeatured(imageId) {
  await fetch('/admin/gallery/set_featured?image_id=' + encodeURIComponent(imageId), { method: 'POST' });
  await Promise.all([loadFeatured(), loadSearch()]);
}

document.addEventListener('click', (e) => {
  const tile = e.target.closest('[data-image-id]');
  if (!tile) return;
  toggleFeatured(tile.dataset.imageId);
});

let searchTimer;
searchInput.addEventListener('input', () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(loadSearch, 200);
});

loadFeatured();
loadSearch();
</script>
