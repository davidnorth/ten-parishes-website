<?php

class Router {
    private string $pagesDir;

    public function __construct(string $pagesDir) {
        $this->pagesDir = rtrim($pagesDir, '/');
    }

    public function match(string $requestPath): ?array {
        $requestPath = rtrim($requestPath, '/') ?: '/';

        foreach ($this->scanPages($this->pagesDir, '') as [$pattern, $paramNames, $filePath]) {
            if (preg_match($pattern, $requestPath, $matches)) {
                array_shift($matches);
                $params = $paramNames ? array_combine($paramNames, $matches) : [];
                return [$filePath, $params];
            }
        }

        return null;
    }

    public function getInitFiles(string $pageFile): array {
        $initFiles = [];
        $pageDir = dirname($pageFile);
        $relative = substr($pageDir, strlen($this->pagesDir));

        $dirs = [$this->pagesDir];
        if ($relative) {
            $current = $this->pagesDir;
            foreach (explode('/', trim($relative, '/')) as $part) {
                $current .= '/' . $part;
                $dirs[] = $current;
            }
        }

        foreach ($dirs as $dir) {
            $candidate = $dir . '/_init.php';
            if (file_exists($candidate)) {
                $initFiles[] = $candidate;
            }
        }

        return $initFiles;
    }

    private function scanPages(string $dir, string $relPath): array {
        $routes = [];

        foreach (scandir($dir) as $entry) {
            if ($entry === '.' || $entry === '..' || $entry[0] === '_') {
                continue;
            }

            $fullPath = $dir . '/' . $entry;

            if (is_dir($fullPath)) {
                $routes = array_merge($routes, $this->scanPages($fullPath, $relPath . '/' . $entry));
            } elseif (str_ends_with($entry, '.php')) {
                $name = substr($entry, 0, -4);
                $urlPath = $name === 'index' ? ($relPath ?: '/') : $relPath . '/' . $name;

                [$pattern, $paramNames] = $this->buildPattern($urlPath);
                $routes[] = [$pattern, $paramNames, $fullPath];
            }
        }

        return $routes;
    }

    private function buildPattern(string $urlPath): array {
        if ($urlPath === '/') {
            return ['#^/$#', []];
        }

        $paramNames = [];
        $regexParts = [];

        foreach (explode('/', ltrim($urlPath, '/')) as $part) {
            if (preg_match('/^\[(.+)\]$/', $part, $m)) {
                $paramNames[] = $m[1];
                $regexParts[] = '([^/]+)';
            } else {
                $regexParts[] = preg_quote($part, '#');
            }
        }

        return ['#^/' . implode('/', $regexParts) . '$#', $paramNames];
    }
}
