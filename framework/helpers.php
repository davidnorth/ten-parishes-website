<?php

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function render_partial(string $path, array $vars = []): void {
    extract($vars);
    require $path;
}
