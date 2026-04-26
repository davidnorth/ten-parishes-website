<?php
if (empty($reg['artist_id'])) {
    return redirect('/register/step-1');
}

$pageTitle = 'Register: Venue';
$parishes  = $db->select('parishes', ['id', 'name'], ['ORDER' => 'name']);

if ($req->isPost()) {
    if ($req->params['action'] === 'back') {
        return redirect('/register/step-1');
    }

    $mode     = $req->params['mode'] ?? 'pick';
    $venueId  = null;

    if ($mode === 'pick') {
        $chosenId = (int) ($req->params['chosen_venue_id'] ?? 0);
        if ($chosenId > 0 && $db->has('venues', ['id' => $chosenId])) {
            $venueId      = $chosenId;
            $reg['venue'] = ['id' => $chosenId];
        }
        // No selection — fall through to re-render the picker.
    } else {
        $venueData = $req->params['venue'] ?? [];
        $reg['venue'] = $venueData;

        $lat = $venueData['latitude'] ?: null;
        $lng = $venueData['longitude'] ?: null;
        if (!$lat || !$lng) {
            $geo = geocode($venueData['address'] ?? '');
            if ($geo) {
                $lat = $geo['lat'];
                $lng = $geo['lng'];
            }
        }

        $db->insert('venues', [
            'name'                => $venueData['name'],
            'slug'                => unique_slug($db, 'venues', $venueData['name']),
            'parish_id'           => $venueData['parish_id'] ?: null,
            'latitude'            => $lat,
            'longitude'           => $lng,
            'address'             => $venueData['address'] ?: null,
            'what_3_words'        => $venueData['what_3_words'] ?: null,
            'directions'          => $venueData['directions'] ?: null,
            'parking'             => $venueData['parking'] ?: null,
            'refreshments'        => $venueData['refreshments'] ?: null,
            'accessibility'       => $venueData['accessibility'] ?: null,
            'dog_policy'          => $venueData['dog_policy'] ?: null,
            'venue_contact_name'  => $venueData['contact_name'] ?: null,
            'venue_contact_phone' => $venueData['contact_phone'] ?: null,
        ]);
        $venueId = $db->id();
        $reg['venue']['id'] = $venueId;
    }

    if ($venueId !== null) {
        $db->update('artists', ['venue_id' => $venueId], ['id' => $reg['artist_id']]);
        return redirect('/register/step-3');
    }
}

// If the user previously picked an existing venue, default to picker mode.
// If they previously filled the new-venue form, default to create mode.
$startInCreateMode = isset($reg['venue']) && !isset($reg['venue']['id']) && !empty($reg['venue']);
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="page-grid">
  <section>

    <h1>Register: Step 2 of 4 — Venue</h1>

    <div id="venue-picker-section" style="<?= $startInCreateMode ? 'display:none' : '' ?>">
      <?php require __DIR__ . '/_venue_picker.php' ?>
    </div>

    <div id="venue-create-section" style="<?= $startInCreateMode ? '' : 'display:none' ?>">
      <h2>Create a new venue</h2>
      <?php require __DIR__ . '/_venue_form.php' ?>
    </div>

  </section>
</div>
