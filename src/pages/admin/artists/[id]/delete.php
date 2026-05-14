<?php
if (!$req->isPost()) {
    return redirect('/admin/artists');
}

$id = $req->params['id'];
$db->delete('event_dates', ['artist_id' => $id]);
$db->delete('images', ['artist_id' => $id]);
$db->delete('artists', ['id' => $id]);
return redirect('/admin/artists');
