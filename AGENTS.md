# Konut.Update — Portal Berita (Laravel 12)

## Stack
- PHP 8.2+, Laravel 12, Livewire 4, Vite 7
- Public: Tailwind CSS v4 + Alpine.js + Swiper + Lucide icons
- Admin: Bootstrap 5 + Bootstrap Icons + DataTables + CKEditor 5 + jQuery
- DB: SQLite (local) / PostgreSQL (Render production)

## Commands
| Command | What it does |
|---|---|
| `composer dev` | Starts server, queue, logs, Vite concurrently |
| `composer test` | `php artisan config:clear && php artisan test` |
| `composer setup` | Full project setup in one shot |
| `npm run build` / `npm run dev` | Vite build/dev |

Run `composer test` before committing (runs PHPUnit with SQLite :memory:).

## Architecture
- **Routes**: `routes/web.php` only (`admin.*` prefix + auth/admin middleware).
- **Auth**: Custom `LoginController` (no Breeze/Jetstream). Login uses rate limiting (5 attempts/min).
- **Admin middleware** (`bootstrap/app.php`): `admin` (checks role: super_admin/editor/reporter) and `role` (parameterized).
- **Roles**: `super_admin`, `editor`, `reporter`. User management requires `role:super_admin`.
- **Default admin**: `admin@konutupdate.com` / `password`
- **Views**: `resources/views/frontend/` (public) and `resources/views/admin/` (CMS).
- **Global view data** (AppServiceProvider): `$site_settings` (cached forever), `$categories`, `$trendingPosts`, `$breakingNews`, `$kecamatans`, sidebar ads, `$footerPages`.

## Key Models & Relationships
- `Post` (soft-deletes): categories (BelongsToMany via `post_categories`, up to 3), category (BelongsTo fallback), tags (BelongsToMany), kecamatan (BelongsTo), comments, likes, pageViews.
- `Post` scopes: `published()`, `draft()`, `headline()`/`featured()` (both check `is_headline`), `breaking()`, `popular()`.
- `Category`: posts (HasMany via `category_id`), allPosts (BelongsToMany via `post_categories`).
- `is_featured` was renamed to `is_headline` — use `headline()` scope, not `featured()` in new code.

## Important Conventions
- **Slug**: auto-generated from title with dedup counter in Admin\PostController.
- **Tags**: comma-separated input, auto-created via `firstOrCreate`.
- **Thumbnails**: stored in `storage/app/public/thumbnails`; uploads in `uploads/images/`; videos in `videos/`.
- **Comments**: require moderation (`is_approved`), throttled at 5/post/minute.
- **Likes**: tracked by IP address.
- **Page views**: queued via `RecordViewJob` (anonymizes IPv4 last octet → `x.x.x.0`).
- **Settings**: cached forever via `setting('key')` helper; call `clearSettingCache('key')` after updates.
- **Locale**: `id` — dates via `formatDate()` helper (Carbon `locale('id')`).
- **Pagination**: Tailwind style (`Paginator::useTailwind()`).
- **CKEditor**: initialized via `window.initCKEditor(elementId)`, image uploads POST to `/admin/posts/upload-image`.

## Deploy (Render)
PostgreSQL, Node 20. Build: composer install --no-dev → npm ci → npm build → migrate → seed (categories, pages) → storage:link.
