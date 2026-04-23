# Ten Parishes Website

A festival website allowing visitors to explore events, with an admin system for managing events, locations, artists and parishes.

## Technologies

- PHP, file-system based routing (see `framework.md`)
- SQLite via Medoo (lightweight query builder, the only composer dependency)
- Tailwind CSS CDN for admin UI
- Cloudinary for image storage and transformation
- Static HTML caching of public pages (not yet implemented)

## Dependencies

Medoo is approved. Tailwind for admin only. Ask before adding anything else — the goal is minimal dependencies.

## HTML & CSS

- Public-facing pages: keep HTML minimal and semantically correct, no custom CSS
- Admin pages: use Tailwind utility classes throughout

## Admin UI conventions

Forms follow this pattern:

- Page title: `text-2xl font-semibold text-gray-900 mb-6`
- Form wrapper: `space-y-4`, each field in a `<div>`
- Label: `block text-sm font-medium text-gray-700 mb-1` — always a separate element, never wrapping the input
- Input: `w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent`
- Primary button: `bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2`
- Validation error: `text-sm text-red-600 mb-4`

## Variables available in every page and _init.php

- `$req` — Request object (`$req->path`, `$req->method`, `$req->params`, `$req->isGET()`, `$req->isPost()`)
- `$db` — Medoo instance (connected to `storage/db.sqlite`)
- `$layout` — set this to a filename in `layouts/` to wrap page output (set by `_init.php` files, not pages)

Admin pages additionally have:

- `$currentUser` — the logged-in user row, set by `pages/admin/_init.php`

## Database

Schema is documented in `schema.md`. `lib/db.php` creates tables on first run via `get_db()`.

To create an admin user: `php scripts/create-user.php`

## Running locally

```
php -S localhost:8000 -t public public/index.php
```
