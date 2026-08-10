# AGENTS.md

Konut.Update — Laravel 12 news portal (Indonesian). Blade views; frontend Tailwind 4 + Alpine 3 + Bootstrap 5; Vite 7 build. UI copy and commit messages are Indonesian — match that.

## Commands

- `composer dev` — full local stack: `artisan serve` + `queue:listen` + `pail` + Vite concurrently. Use this instead of starting servers separately.
- `composer test` — `artisan config:clear` then PHPUnit (sqlite `:memory:`, no DB setup). Single test: `php artisan test --filter=PostExpiryTest`.
- `npm run build` / `npm run dev` — Vite assets. Inputs in `vite.config.js`: `resources/css/app.css` + `admin.css`, `resources/js/app.js` + `admin.js`.
- `vendor/bin/pint` — code style (default Laravel preset; no `pint.json`).
- Fresh setup: `composer run setup`. Seeded admin login: `admin@konutupdate.com` / `password`.

## Architecture

- Controllers split into `Admin/` and `Frontend/` namespaces; `routes/web.php` imports both with aliases (e.g. `AdminPostController` vs `Frontend\PostController`). No `api.php`; all routes in `routes/web.php`.
- Post query logic lives in `app/Repositories/PostRepository.php` (headline, trending, category structure); `app/Services/HeadlineService.php` wraps it. Global helpers in `app/Helpers/helpers.php` (`setting()`, `formatDate()`, `readTime()`, share helpers).
- Posts have a legacy `category_id` plus many-to-many `categories` (`post_categories`). `category()` falls back to first pivot category. Keep both in sync on create/update (see `Admin\PostController::store`).
- DB: local = SQLite (`database/database.sqlite`), production (Render) = PostgreSQL. Queue, cache, and session all use the `database` driver.

## Gotchas

- `QUEUE_CONNECTION=database`: `RecordViewJob` (page-view tracking, dispatched in `Frontend\PostController::show`) silently does nothing without a worker — `php artisan queue:listen` is part of `composer dev`.
- Thumbnails: `Admin\PostController` server-side crops `cover(1200,675)` → WebP 85 via Intervention Image 3 (GD). README claims headline posts skip cropping — **stale**, current code always crops. CKEditor uploads: `resizeDown(1200)` → WebP, max 5MB; thumbnail max 2MB.
- Headline/breaking auto-expire after 7 days via `posts:expire-featured`, scheduled daily in `routes/console.php`. Post scopes (`headline()`, `breaking()`, `excludeHeadline()`) filter on `*_expires_at`, not the bare boolean — when toggling a flag manually, also set the expiry timestamp or the post drops out immediately. Scheduler needs cron; `render.yaml` has no scheduler/worker service.
- `setting()` caches values forever — after changing settings (UI or seeder) call `clearSettingCache()` or `php artisan cache:clear`, or stale values persist.
- `setup.php` in the webroot is a one-time deploy bootstrap that forces sqlite and echoes the admin password. **Delete it after provisioning**; keep it out of production.
- `role:super_admin` guards `/admin/users`; `admin` middleware allows super_admin/editor/reporter. Comments POST is throttled (`throttle:5,1`).
- Local `.env` is already set to `APP_ENV=production`/sqlite; `.env.example` is the dev reference.
- Livewire is a composer dependency but not used in code.
