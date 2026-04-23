# Framework

File-system based routing similar to Razor Pages. No framework classes in page files — just `$req`, `$db`, and HTML.

## Request lifecycle

1. Web server passes all non-static requests to `public/index.php`
2. `public/index.php` requires `framework/bootstrap.php`, which:
   - Builds a `Request` object as `$req`
   - Instantiates a Medoo db connection as `$db`
   - Matches the request path to a file in `pages/`
   - Executes any `_init.php` files found along the path (root → page directory)
   - Buffers the page output, then wraps it in a layout if one was set

## Routing

URL paths map directly to files in `pages/`:

```
GET /                    → pages/index.php
GET /about/team          → pages/about/team.php
GET /admin               → pages/admin/index.php
```

Dynamic segments use `[param_name]` in directory or file names:

```
GET /parishes/wiveliscombe/artists/mary-smith
  → pages/parishes/[parish_slug]/artists/[artist_slug].php
  → $req->params['parish_slug'] = 'wiveliscombe'
  → $req->params['artist_slug'] = 'mary-smith'
```

`$req->params` merges POST body, query string, and route params into one array.

No match → `public/404.html`.

## Pages

A page file contains logic followed by HTML output. POST handling uses an early return:

```php
<?php
if ($req->isPost()) {
    $db->update('products', ['name' => $req->params['name']], ['id' => $req->params['id']]);
    return redirect("/products/{$req->params['id']}");
}
?>
<h1>Edit product</h1>
```

`redirect()` sends a `Location` header and exits. The `return` is stylistic — it documents intent.

## _init.php files

Placing a `_init.php` in any `pages/` subdirectory causes it to run before the page, for every request under that directory. All `_init.php` files along the path are executed root-first.

Common uses: setting `$layout`, enforcing auth, loading shared data.

```php
// pages/_init.php — sets layout and starts session for all pages
$layout = 'default';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

```php
// pages/admin/_init.php — auth guard + loads current user
$layout = 'admin';

if (!isset($_SESSION['user_id']) && $req->path !== '/admin/login') {
    return redirect('/admin/login');
}

if (isset($_SESSION['user_id'])) {
    $currentUser = $db->get('users', '*', ['id' => $_SESSION['user_id']]);
}
```

`return redirect(...)` stops execution of the current `_init.php` and the page — subsequent `_init.php` files and the page are not executed.

## Layouts

A layout wraps the page output. Set `$layout` to a filename (without `.php`) from `layouts/`:

```php
$layout = 'admin'; // uses layouts/admin.php
```

Inside the layout file, `$content` contains the buffered page output:

```php
<main><?= $content ?></main>
```

`$req`, `$db`, and any variables set by `_init.php` files (e.g. `$currentUser`) are also available in layout files.

## Directory structure

```
pages/          Page logic and templates
layouts/        Layout wrappers (default.php, admin.php)
framework/      Router, Request, helpers — not app-specific
lib/            App-specific shared code (db.php etc.)
storage/        SQLite database
public/         Web root — static assets, cached HTML, index.php
scripts/        CLI utilities (create-user.php etc.)
```
