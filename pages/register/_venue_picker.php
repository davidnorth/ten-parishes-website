<form method="post" action="/register/step-2" id="venue-picker-form">
  <input type="hidden" name="mode" value="pick">

  <div>
    <label for="postcode">Postcode</label>
    <input type="text" id="postcode" name="postcode" placeholder="e.g. TA24 6QP" autocomplete="postal-code">
    <button class="small" type="button" id="postcode-search-btn">Search</button>
  </div>

  <p id="postcode-message"><small>Enter your venue's postcode to find existing venues nearby.</small></p>

  <div id="venue-results"></div>

  <input type="hidden" name="chosen_venue_id" id="chosen-venue-id" value="">

  <div class="form-actions">
    <button class="secondary" type="submit" name="action" value="back" formnovalidate>&larr; Back</button>

    <div>
      Can&rsquo;t find your venue? &nbsp;
      <button class="primary" type="button" id="create-new-venue-btn">Create a new one</button>
    </div>
  </div>
</form>

<script>
(function () {
  const postcodeInput  = document.getElementById('postcode');
  const searchBtn      = document.getElementById('postcode-search-btn');
  const message        = document.getElementById('postcode-message');
  const results        = document.getElementById('venue-results');
  const chosenIdInput  = document.getElementById('chosen-venue-id');
  const pickerForm     = document.getElementById('venue-picker-form');
  const createNewBtn   = document.getElementById('create-new-venue-btn');

  function search() {
    const q = postcodeInput.value.trim();
    if (!q) return;
    message.innerHTML = '<small>Searching…</small>';
    results.innerHTML = '';
    fetch('/venues/search?postcode=' + encodeURIComponent(q))
      .then(r => r.json())
      .then(data => {
        if (data.error) {
          message.innerHTML = '<small>' + data.error + '</small>';
          return;
        }
        if (!data.venues.length) {
          message.innerHTML = '<small>No venues found near that postcode. You can create a new one below.</small>';
          return;
        }
        message.innerHTML = '<small>Found ' + data.venues.length + ' venue' + (data.venues.length === 1 ? '' : 's') + ' nearby:</small>';
        results.innerHTML = data.venues.map(v => `
          <div class="venue-result">
            <div>
              <strong>${escapeHtml(v.name)}</strong>
              ${v.parish ? '<br><small>' + escapeHtml(v.parish) + '</small>' : ''}
              ${v.address ? '<br><small>' + escapeHtml(v.address) + '</small>' : ''}
            </div>
            <button type="button" class="small use-venue-btn" data-id="${v.id}">Use this venue</button>
          </div>
        `).join('');
        results.querySelectorAll('.use-venue-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            chosenIdInput.value = btn.dataset.id;
            pickerForm.submit();
          });
        });
      })
      .catch(() => {
        message.innerHTML = "<small>Search failed. Please try again, or create a new venue.</small>";
      });
  }

  function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => (
      { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
    ));
  }

  searchBtn.addEventListener('click', search);
  postcodeInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); search(); }
  });

  createNewBtn.addEventListener('click', () => {
    document.getElementById('venue-picker-section').style.display = 'none';
    document.getElementById('venue-create-section').style.display = '';
  });
})();
</script>
