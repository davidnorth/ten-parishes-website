<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
}

require_once __DIR__ . '/../lib/constants.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/slug.php';
require_once __DIR__ . '/../lib/cloudinary.php';
require_once __DIR__ . '/../lib/form_helpers.php';
require_once __DIR__ . '/../lib/geocode.php';
require_once __DIR__ . '/Request.php';
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/helpers.php';

$db = get_db();
$req = new Request();
$router = new Router(realpath(__DIR__ . '/../pages'));

$match = $router->match($req->path);

if (!$match) {
    http_response_code(404);
    include __DIR__ . '/../public/404.html';
    exit;
}

[$pageFile, $routeParams] = $match;
$req->params = array_merge($req->params, $routeParams);

$layout = null;

foreach ($router->getInitFiles($pageFile) as $initFile) {
    require $initFile;
}

ob_start();
require $pageFile;
$content = ob_get_clean();

if ($layout) {
    require __DIR__ . '/../layouts/' . $layout . '.php';
} else {
    echo $content;
}
