# Ten Parishes Website

A festival website for visitors to explore events, with an admin system for managing artists, venues, parishes and event dates. Artists register via a public multi-step form; submissions require admin approval before going live.

## Technologies

- PHP, file-system based routing (see `framework.md`)
- SQLite via Medoo (lightweight query builder, the only composer dependency)
- Tailwind CSS CDN — admin UI only
- Cloudinary — image storage and transformation
- Leaflet.js + OpenStreetMap/Nominatim — map picker (admin layout includes it; public pages must include it inline per-page if needed)
- Quill.js — rich text editor (admin layout only)

## Dependencies

Medoo is approved. Tailwind for admin only. Ask before adding anything else — the goal is minimal dependencies.

## HTML & CSS

- Public-facing pages: plain semantic HTML, no CSS classes, no custom CSS
- Admin pages: Tailwind utility classes throughout
- Public multi-step forms (e.g. `/register`): plain HTML — no Tailwind, no Quill; plain `<textarea>` instead of rich text editor

## Routing

URL paths map directly to files under `pages/`. Examples:
- `/register/step-1` → `pages/register/step-1.php`
- `/admin/artists/[id]/edit` → `pages/admin/artists/[id]/edit.php`

`[param_name]` in a directory or filename becomes a dynamic segment; the value lands in `$req->params['param_name']`.

`_init.php` files run root-first before the matched page. They set `$layout`, enforce auth, and initialise shared state. An early `return redirect(...)` from `_init.php` aborts the request.

## Variables available in every page and `_init.php`

- `$req` — Request object: `$req->path`, `$req->method`, `$req->params` (merged GET + POST + route params), `$req->isGET()`, `$req->isPost()`
- `$db` — Medoo instance connected to `storage/db.sqlite`
- `$layout` — set to a filename in `layouts/` to wrap output (set by `_init.php`, not by pages)

Admin pages additionally have:
- `$currentUser` — logged-in user row, set by `pages/admin/_init.php`

## Key `_init.php` files

| File | What it does |
|---|---|
| `pages/_init.php` | Sets `$layout = 'default'`, starts session |
| `pages/admin/_init.php` | Sets `$layout = 'admin'`, enforces auth, sets `$currentUser` |
| `pages/register/_init.php` | Ensures `$_SESSION['registration']` exists; sets `$reg = &$_SESSION['registration']` |

## Library functions

**`lib/helpers.php`** (loaded globally via framework):
- `redirect(string $url): never` — sends Location header and exits

**`lib/slug.php`**:
- `slugify(string $text): string`
- `unique_slug(Medoo $db, string $table, string $text, ?int $excludeId = null): string` — appends `-2`, `-3` etc. to avoid collisions; pass `$excludeId` on edit

**`lib/cloudinary.php`**:
- `cloudinary_upload(string $tmpPath, string $fileName): string` — uploads to Cloudinary, returns `public_id`; throws `RuntimeException` on failure
- `cloudinary_url(string $publicId, string $transform = 'w_800,c_limit'): string` — builds CDN URL

**`lib/db.php`**:
- `get_db(): Medoo` — singleton; calls `init_schema()` on first run
- `init_schema(Medoo $db): void` — `CREATE TABLE IF NOT EXISTS` for every table, followed by additive `ALTER TABLE ADD COLUMN` migration blocks (see venues and artists blocks as the pattern)

## Database

Schema documented in `schema.md`. Tables: `users`, `parishes`, `venues`, `artists`, `images`, `event_dates`.

Key artist fields: `venue_id`, `type` (exhibition/special/workshop), `name`, `slug`, `body_html`, `email`, `phone`, `short_description`, `picture_id` (Cloudinary), `approved` (0/1 — public pages only show `approved = 1`).

To create an admin user: `php scripts/create-user.php`

## DB migration pattern

When adding columns to an existing table, follow the pattern in `lib/db.php` after the venues block:

```php
$artistColumns = array_column(
    $db->query("PRAGMA table_info(artists)")->fetchAll(PDO::FETCH_ASSOC),
    'name'
);
foreach (['new_col' => 'TEXT'] as $col => $def) {
    if (!in_array($col, $artistColumns)) {
        $db->query("ALTER TABLE artists ADD COLUMN $col $def");
    }
}
```

`NOT NULL DEFAULT x` is safe with SQLite's `ALTER TABLE ADD COLUMN` as long as a default is provided.

## Admin UI conventions

- Page title: `text-2xl font-semibold text-gray-900 mb-6`
- Form wrapper: `space-y-4`, each field in a `<div>`
- Label: `block text-sm font-medium text-gray-700 mb-1` — always a separate element, never wrapping the input
- Input: `w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent`
- Primary button: `bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-4 py-2`
- Validation error: `text-sm text-red-600 mb-4`

## Admin CRUD pattern

See `pages/admin/artists/new.php` and `pages/admin/artists/[id]/edit.php` as the canonical examples.

```php
if ($req->isPost()) {
    $db->insert('table', [...]);
    $id = $db->id(); // last insert ID
    return redirect('/admin/table');
}
// GET: render form
```

Edit pages load the record first; 404 if not found. Redirect after POST.

## Admin dynamic forms (event dates, images)

Repeating sections use JS to add/remove rows with indexed array names (`event_dates[0][date]`, `new_image_file[0]`). PHP iterates `$req->params['event_dates'] ?? []` and `$_FILES['new_image_file']['tmp_name']`. See `pages/admin/artists/new.php` for the full pattern including Quill setup.

## Cloudinary image upload pattern

```php
$pictureId = null;
if (!empty($_FILES['picture_file']['tmp_name']) && is_uploaded_file($_FILES['picture_file']['tmp_name'])) {
    $pictureId = cloudinary_upload($_FILES['picture_file']['tmp_name'], $_FILES['picture_file']['name']);
}
```

Store the returned `public_id` string in the DB. Render with `cloudinary_url($publicId, 'w_400,h_400,c_fill')`. Forms need `enctype="multipart/form-data"`. File data never passes through session — upload on POST and store only the public_id string.

## Map / location picker

Admin pages include a reusable partial `pages/admin/_location_picker.php` (uses Tailwind; Leaflet loaded by admin layout). Set `$locationLat` and `$locationLng` before requiring it. Outputs hidden inputs `latitude` and `longitude`.

Public pages use `pages/register/_location_picker.php` — identical logic, Tailwind classes stripped. The page must load Leaflet inline before including it:

```html
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?php require __DIR__ . '/_location_picker.php' ?>
```

## Multi-step public forms (registration flow)

See `pages/register/` as the canonical example.

State lives in `$_SESSION['registration']` (initialised by `_init.php` as `$reg`). Each step:
- GET: render form pre-filled from session
- POST: save to session, redirect to next step

Back buttons use `formnovalidate` to bypass browser validation and a named submit value to signal direction:

```html
<button type="submit" name="action" value="back" formnovalidate>&larr; Back</button>
<button type="submit" name="action" value="next">Next &rarr;</button>
```

Server-side: check `$req->params['action'] === 'back'` before any validation. The final step (step-4) commits all records to DB in one go: venue → artist → event_dates → images, then `unset($_SESSION['registration'])` and redirect to `/register/complete`.

Guard against users jumping to mid-flow steps directly:
```php
if (empty($reg['artist'])) return redirect('/register/step-1');
```

## Abuse prevention (registration)

Three layers used in the registration flow:
1. **Honeypot**: hidden field `name="website"` positioned offscreen (`style="position:absolute;left:-9999px"`). Non-empty → silent redirect to complete page.
2. **Rate limit**: `$reg['submitted_at']` timestamp; reject if < 60s since last forward submission.
3. **Approval gate**: all registrations write `approved = 0`; public listing and detail pages filter to `approved = 1` only.

## Public pages and approved artists

Any page that lists or loads artists must filter by `approved = 1`. Currently enforced in:
- `pages/artists.php` — `WHERE artists.approved = 1`
- `pages/artists/[artist_slug].php` — 404 if `!$artist['approved']`
- `pages/parishes/[parish_slug]/index.php` — `AND artists.approved = 1`

## SQLite MCP

The `sqlite` MCP server is configured in `.mcp.json` and connected to `storage/db.sqlite`. Use it to inspect data directly — `mcp__sqlite__query` for SELECTs, `mcp__sqlite__list-tables` to browse the schema — rather than writing throwaway PHP scripts or shelling out to `sqlite3`.

## Running locally

```
php -S localhost:8000 -t public public/index.php
```
