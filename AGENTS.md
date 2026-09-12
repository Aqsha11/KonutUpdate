# AGENTS.md

**Konut.Update** — Laravel 12 news portal (Indonesian). README.md is partially stale; trust `routes/web.php` and the code over it.

## Commands

| Command | What it does |
|---|---|
| `composer dev` | Runs `artisan serve` + `queue:listen` + `pail` (logs) + `npm run dev` concurrently. **Queue worker is required**: `RecordViewJob` runs on the `database` queue, so views never persist without it. |
| `npm run build` / `npm run dev` | Vite (Tailwind 4 via `@tailwindcss/vite`). Frontend is unstyled without running this at least once. |
| `composer test` | `artisan config:clear` + `artisan test`. Tests use `:memory:` SQLite (see `phpunit.xml`), no external services needed. |
| `vendor/bin/pint` | Code style (Laravel Pint, no config file). |
| `composer setup` | Full local install (env, key, migrate, npm build). |
| `php artisan posts:expire-flags` | Custom command, scheduled daily in `routes/console.php`. Resets expired `is_headline` (7d), `is_breaking` (3d), and `is_featured` (14d) flags. |
| **Seeding** | Full `DatabaseSeeder` is local-only (creates demo admin `admin@konutupdate.com` / `password`). Production (`render.yaml`) seeds `CategorySeeder` + `PageSeeder`. |

## Architecture

### Routes & Controllers
- Routes in one file: `routes/web.php`.
- Controllers grouped: `app/Http/Controllers/{Admin,Frontend,Contributor,Auth}`.
- Admin routes use `auth`, `admin`, and `admin.session-timeout` middleware.

### Data Model
- **`posts` table** serves all content via `type` enum: `article`, `video`, `opini`.
- `status` enum: `draft`, `pending`, `published`, `rejected`. Contributor submissions need admin approve/reject; `rejection_reason` is set on reject.
- `category_id` is legacy — use the `post_categories` pivot (`categories()` relation).
- `is_headline` was renamed from the original `is_featured` column; a separate `is_featured` ("Konten Pilihan") column was re-added later — they are independent.

### Roles
- `super_admin`, `editor`, `reporter`, `kontributor`.
- Custom middleware aliases in `bootstrap/app.php`: `admin` (super_admin/editor/reporter), `role`, `permission`, `admin.session-timeout`.
- `User::hasPermission()` short-circuits `true` for `super_admin`.
- Admin opini routes gated by `permission:manage_opini`; `/admin/users` additionally requires `role:super_admin`.

### Auth
- Controller-based (own `Auth\*` controllers), **not** Laravel Breeze/Jetstream.
- Users must verify email (`MustVerifyEmail`).
- Login/register use Cloudflare Turnstile (`app/Services/TurnstileService.php`); `TURNSTILE_ENABLED=false` (default in `.env.example`) fully disables the widget + validation for local dev. `TurnstileService` also short-circuits `true` for the Cloudflare test secret `1x00000000000000000000AA` and in `local` env.
- Admin sessions timeout after 5 minutes idle (`AdminSessionTimeout` middleware).

### Contributors
- `/panel-kontributor` is the public contributor panel (`role:kontributor`). Contributor posts are stored but not auto-published.

### Helpers
- `app/Helpers/helpers.php` loaded via `require_once` in `AppServiceProvider::register()` — **not** via composer autoload files.
- `setting($key)` reads from DB with a forever cache; after updating Settings use `clearSettingCache()` or `php artisan cache:clear`.
- Also includes `seoInternalLinks()` for auto-linking keywords in article body HTML.

### HTML Sanitization
- `app/Services/HtmlSanitizer.php` whitelist-sanitizes CKEditor `body` input; apply it before persisting HTML. Render output uses `{!! ... !!}` (raw) — sanitizer is the safety net.

## Domain Gotchas

- **Headline posts** (`is_headline`) are excluded from "Berita Terbaru" + "Trending" via `scopeExcludeHeadline`. Limit is 6 active headlines, enforced by `Post::enforceHeadlineLimit($keepId)` on save.
- **`is_featured`** ("Konten Pilihan") auto-expires 14 days after `published_at` — the `featured()` scope hides old posts immediately, and `posts:expire-flags` clears the flag. Admin edits after expiry re-set it from scratch.
- **Frontend queries are cached** in `AppServiceProvider` view composers (`site_settings` forever, `trending_posts`/`frontend_pages` 1h, `breaking_news` 5m) — clear cache after data changes or views won't reflect them.
- **Video posts**: `video_path` can be a YouTube/Vimeo/TikTok URL or a stored file; `video_embed_url`/`video_poster` accessors handle embeds.
- **DB default** is SQLite locally (`.env.example`); production is pgsql (`render.yaml`, `SESSION_ENCRYPT=true`, DB-backed cache/queue/session).

## Testing

- Feature tests use `RefreshDatabase` + factories, no seeding. Create roles inline: `Role::factory()->create(['slug' => 'editor'])` then attach permissions via `$role->permissions()->attach(...)`.
- `UserFactory` emails are verified by default; use `unverified()` state when testing the verification flow.
- Frontend/comments/likes are IP-based, not auth-based. `RecordViewJob` uses the `sync` queue in tests.
- Turnstile is enabled in phpunit.xml but uses dummy keys (`TURNSTILE_SECRET_KEY=test-secret-key`). The `TurnstileService` short-circuits `true` for the Cloudflare test secret `1x00000000000000000000AA`, so registration/login tests pass without hitting Cloudflare.

## Ops / Build

- **Deploy target**: Render (`render.yaml`) — PHP runtime. Two services: `konut-update` (web, `artisan serve`, storage mounted to a disk) and `konut-worker` (worker, runs `queue:work` + `schedule:work`). Without the worker, `RecordViewJob` and `posts:expire-flags` never run in prod. Env vars in `render.yaml` are the source of truth for prod DB/queue/cache/session.
- `php artisan storage:link` required for thumbnails/CKEditor uploads at `storage/app/public/{thumbnails,uploads/images}`.
- Images are converted to WebP 85% (Intervention Image 3, GD driver); headline thumbnails keep original size, others are downscaled to max 1200px.
- Livewire `^4.3` is installed but **unused** — no components, no views, no `wire:` directives anywhere. Safe to ignore.
- No CI workflows (`.github/workflows/`) exist — tests must be run locally.
- **Rate limiting**: login (10/min/IP), register (5/min/IP), comments (5/min/IP), likes (30/min/IP).
