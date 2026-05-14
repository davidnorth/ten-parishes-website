<?php

if (!$req->isPost()) {
    return redirect('/admin/parishes');
}

$db->delete('parishes', ['id' => $req->params['id']]);
return redirect('/admin/parishes');
