<?php

if (!$req->isPost()) {
    return redirect('/admin/venues');
}

$db->delete('venues', ['id' => $req->params['id']]);
return redirect('/admin/venues');
