<?php

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function render_partial(string $path, array $vars = []): void {
    extract($vars);
    require $path;
}

/* given a full URL with scheme etc,
  remove the scheme and www. prefix, and trailing slash */
function formatURL(string $url): string {
    $url = preg_replace('/^https?:\/\/(www\.)?/', '', $url);
    return rtrim($url, '/');
}
