<?php

class Request {
    public string $path;
    public string $method;
    public array $params;

    public function __construct() {
        $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->params = array_merge($_GET, $_POST);
    }

    public function isGET(): bool {
        return $this->method === 'GET';
    }

    public function isPost(): bool {
        return $this->method === 'POST';
    }
}
