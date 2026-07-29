# Konut.Update — Portal Berita (Laravel 12)

## Stack
- PHP 8.2+, Laravel 12, Livewire 4, Vite 7
- Public: Tailwind CSS v4 + Alpine.js + Swiper + Lucide icons
- Admin: Bootstrap 5 + Bootstrap Icons + DataTables + CKEditor 5 + jQuery
- DB: SQLite (local) / PostgreSQL (Render production)

## Commands
| Command | What it does |
|---|---|
| `composer dev` | Starts server, queue, logs, Vite via `npx concurrently` |
| `composer test` | `php artisan config:clear && php artisan test` |
| `composer setup` | `composer install`, creates `.env`, generates key, migrates, `npm install && npm run build` |
| `npm run build` / `npm run dev` | Vite build/dev |
| `./vendor/bin/pint` | Laravel Pint (PSR-12 style fixer) |

Run `composer test` before committing (uses SQLite `:memory:`, `QUEUE_CONNECTION=sync` — jobs run inline).

## Architecture
- **Routes**: `routes/web.php` only (`admin.*` prefix + auth/admin middleware).
- **Auth**: Custom `LoginController` (no Breeze/Jetstream). Rate limited: 5 attempts/min with 60s decay.
- **Admin middleware** (`bootstrap/app.php`): `admin` (checks role: super_admin/editor/reporter) and `role` (parameterized). User management requires `role:super_admin`.
- **Default accounts** (seeded): `admin@konutupdate.com` / `password` (super_admin), `editor@konutupdate.com` / `password` (editor), `reporter@konutupdate.com` / `password` (reporter).
- **Views**: `resources/views/frontend/` (public) and `resources/views/admin/` (CMS).
- **Seeding order**: RoleSeeder → RolePermissionSeeder → CategorySeeder → UserSeeder → PostSeeder → SettingSeeder → PageSeeder.
- **Global view composers** (AppServiceProvider): `$site_settings` (cached forever as `site_settings`), `$categories`, `$trendingPosts`, `$breakingNews`, `$kecamatans`, sidebar/in-article ads, `$footerPages`.

## Key Models & Relationships
- **`Post`** (soft-deletes): categories (BelongsToMany via `post_categories`, max 3), category (BelongsTo fallback), tags (BelongsToMany), kecamatan (BelongsTo), comments, likes, pageViews.
- **`Post` scopes**: `published()` / `draft()` / `headline()`/`featured()` (both map to `is_headline`) / `breaking()` / `popular()` / `excludeHeadline()`.
- **`Category`**: `posts()` (HasMany via `category_id`), `allPosts()` (BelongsToMany via `post_categories`).
- **`PostRepository`** handles structured frontend queries (headline, category-with-structure, trending, etc.).

## Important Conventions
- **Slug**: auto-generated from title with dedup counter in `Admin\PostController@uniqueSlug`.
- **Tags**: comma-separated input, auto-created via `firstOrCreate` matching on `slug`.
- **Thumbnails**: stored in `storage/app/public/thumbnails`; CKEditor uploads in `uploads/images/`; video files in `videos/`.
- **Post body**: sanitized via `HtmlSanitizer` service (strips disallowed tags/attributes/event handlers). CKEditor uploads max 5MB (jpeg,png,jpg,gif,webp) to `/admin/posts/upload-image`.
- **Thumbnail**: client-side crop via Cropper.js (16:9, 1200×675) before upload. Server converts to WebP via Intervention Image. CKEditor body images are resized down to 1200px width max and also converted to WebP.
- **Comments**: require moderation (`is_approved`), throttled at 5/post/minute via route middleware.
- **Likes**: tracked by IP address (`Like` model, `ip_address`).
- **Page views**: queued via `RecordViewJob` (anonymizes IPv4 last octet → `x.x.x.0`). Requires `queue:listen` in dev.
- **Settings**: individual keys cached forever via `setting('key')` helper. `clearSettingCache('key')` clears both per-key and bulk `site_settings` cache.
- **Locale**: `id` — dates formatted via `formatDate()` helper (`Carbon::locale('id')`), `readTime()` helper for reading time.
- **Pagination**: Tailwind style (`Paginator::useTailwind()`).
- **CKEditor**: initialized via `window.initCKEditor(elementId)` in admin JS.
- **Public helpers**: `shareFacebook()`, `shareWhatsApp()`, `shareTelegram()`, `shareTwitter()` in `app/Helpers/helpers.php`.
- **`composer dev`** runs 4 concurrent processes: `php artisan serve`, `queue:listen --tries=1 --timeout=0`, `pail --timeout=0`, `npm run dev`.

## Deploy (Render)
PostgreSQL, Node 20. Build: `composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan migrate --force && php artisan db:seed --class=CategorySeeder --force && php artisan db:seed --class=PageSeeder --force && php artisan storage:link --force`. Cache/queue/session use `database` driver in production.
