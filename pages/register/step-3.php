<?php
if (empty($reg['artist_id'])) {
    return redirect('/register/step-1');
}

$pageTitle = 'Register: Event Dates';
$eventDates = $reg['event_dates'] ?? [];

if ($req->isPost()) {
    $eventDates = array_values(array_filter(
        $req->params['event_dates'] ?? [],
        fn($ed) => !empty($ed['date'])
    ));
    $reg['event_dates'] = $eventDates;

    $db->delete('event_dates', ['artist_id' => $reg['artist_id']]);
    foreach ($eventDates as $ed) {
        $db->insert('event_dates', [
            'artist_id' => $reg['artist_id'],
            'date'      => $ed['date'],
            'from_time' => $ed['from_time'] ?: null,
            'to_time'   => $ed['to_time'] ?: null,
        ]);
    }

    if ($req->params['action'] === 'back') {
        return redirect('/register/step-2');
    }

    return redirect('/register/step-4');
}

$existingCount = count($eventDates);
?>



<div class="page-grid">
  <section class="register-wizard">

    <div>
      <?php $current = 3; require __DIR__ . '/_progress.php'; ?>
    </div>
    <div>

<h1>Event Dates</h1>

<p>Please add the dates your event will be open to the public. This step is optional &mdash; you can come back and add them later.</p>

<form method="post" action="/register/step-3">

    <legend>Dates</legend>
    <div id="event-dates">
      <?php foreach ($eventDates as $i => $ed): ?>
      <div class="event-date-row">
        <input type="date" name="event_dates[<?= $i ?>][date]" value="<?= htmlspecialchars($ed['date'] ?? '') ?>">
        <input type="time" name="event_dates[<?= $i ?>][from_time]" value="<?= htmlspecialchars($ed['from_time'] ?? '') ?>">
        <span>to</span>
        <input type="time" name="event_dates[<?= $i ?>][to_time]" value="<?= htmlspecialchars($ed['to_time'] ?? '') ?>">
        <button type="button" onclick="this.closest('.event-date-row').remove()">Remove</button>
      </div>
      <?php endforeach ?>
    </div>
    <button type="button" onclick="addEventDate()">+ Add date</button>

  <div class="form-actions">
    <button class="secondary" type="submit" name="action" value="back">&larr; Back</button>
    <button class="primary" type="submit" name="action" value="next">Next &rarr;</button>
  </div>
</form>

    </div>
  </section>
</div>



<script>
let eventDateIndex = <?= $existingCount ?>;
function addEventDate() {
    const idx = eventDateIndex++;
    const row = document.createElement('div');
    row.className = 'event-date-row';
    row.innerHTML = `
        <input type="date" name="event_dates[${idx}][date]">
        <input type="time" name="event_dates[${idx}][from_time]">
        <span>to</span>
        <input type="time" name="event_dates[${idx}][to_time]">
        <button type="button" onclick="this.closest('.event-date-row').remove()">Remove</button>
    `;
    document.getElementById('event-dates').appendChild(row);
}
</script>
