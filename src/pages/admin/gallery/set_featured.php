<?php
$layout = null;

if (!$req->isPost()) {
    http_response_code(405);
    return;
}

$imageId = (int) ($req->params['image_id'] ?? 0);
if (!$imageId) {
    http_response_code(400);
    return;
}

$image = $db->get('images', ['id', 'featured'], ['id' => $imageId]);
if (!$image) {
    http_response_code(404);
    return;
}

$db->update('images', ['featured' => $image['featured'] ? 0 : 1], ['id' => $imageId]);
http_response_code(204);
