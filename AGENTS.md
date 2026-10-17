# AGENTS.md

**Konut Update** — Laravel 12 news portal (Indonesian, Konawe Utara). Bootstrap-style, no modules/packages of its own.

**README.md is unreliable.** Verified wrong claims: headline limit is **6** (`Post::enforceHeadlineLimit`), not 9/unlimited; there is no Cropper.js client-side crop and no "headline keeps original size" exception; the `posts` columns/status list is outdated. Trust `routes/web.php`, `app/`, and migrations.

## Commands

| Command | Notes |
|---|---|
| `composer dev` | `artisan serve` + `queue:listen --tries=1` + `pail` + `npm run dev`. **The queue worker is mandatory** — `RecordViewJob` runs on the `database` queue, so `views_count` never moves without it. |
| `composer test` | `artisan config:clear` + `artisan test`. Currently 104 tests, ~25s, fully offline (`:memory:` SQLite, `QUEUE_CONNECTION=sync`). |
| `php artisan test --filter=PostExpiryTest` | Single test / class. Also `--testsuite=Unit`. |
| `vendor/bin/pint` | Style. **No `pint.json`** — Pint defaults. No lint/typecheck step otherwise; no CI (`.github/` does not exist), so run `pint` + `composer test` yourself. |
| `composer setup` | `composer install` → `.env` copy → `key:generate` → `migrate` → `npm install` → `npm run build`. |
| `php artisan storage:link` | Required for thumbnails/CKEditor uploads (`storage/app/public/{thumbnails,avatars,uploads/images,videos,stories}`). |
| `php artisan posts:expire-flags` | `routes/console.php` schedules it daily. Only prod worker runs it (see Ops). |
| `npm run build` | Vite 7 + Tailwind 4 (`@tailwindcss/vite`). Pages are unstyled until this has run once. Vite dev server is pinned to `127.0.0.1` (`vite.config.js`) — HMR breaks if you browse from another device. |

## Architecture

- **Single route file**: `routes/web.php` (includes hand-written `robots.txt`, `/feed`, `/sitemap.xml` closures). Article detail is the root-level catch-all `GET /{slug}` at the **very end** of the file; `GET /berita/{slug}` is a 301 legacy redirect to it. Comment/like POST endpoints deliberately stay under `/berita/{post}/...`.
- **A route can never shadow a file in `public/`**: `artisan serve` (and `render.yaml` in production) uses `server.php`, which serves any existing `public/` file directly without booting the framework. A route at `/manifest.json` is therefore dead code while the file exists. Corollary for tests: `$this->get('/manifest.json')` 404s in the test client (it hits the routes), so read `public_path('manifest.json')` from disk instead.
- **Controllers**: `app/Http/Controllers/{Admin,Frontend,Contributor,Auth}`. Validation lives in `app/Http/Requests/{Store,Update}*` FormRequests, not in controllers.
- `Auth\LoginController` renders the **admin** login view (`admin/auth.login`) for *everyone* — there is no separate public login page.
- `admin` routes run `['auth','admin','admin.session-timeout']`; `bootstrap/app.php` aliases `admin`, `role`, `permission`, `admin.session-timeout` and appends `SecurityHeaders` to every web request.
- `app/Repositories/PostRepository.php` is the real frontend query layer (headline/trending/category/kecamatan structures); `HeadlineService` is a thin wrapper. Go here before adding query logic to a controller.

### Data model
- `posts` holds all content via `type`: `article`, `video`, `opini`.
  - Gotcha: the column migration declares only `['article','video']`; `opini` was added later **only as a raw pgsql CHECK constraint** (`2026_08_15_000002`). So `type='opini'` works on SQLite but has no local DB enforcement.
- `status`: `draft`, `pending`, `published`, `rejected`. Contributor submissions land as `pending`; admin approve/reject sets `status` + `rejection_reason`.
- `category_id` is legacy. Use the `post_categories` pivot via `Category::allPosts()` / `$post->categories()`. `Category::posts()` (HasMany on `category_id`) still exists — don't use it.
- `is_headline` and `is_featured` are **independent** columns (renamed apart in `2025_07_26`, then `is_featured` re-added in `2026_08_15`). Don't conflate them.
- **Max 3 categories per post** — controllers hard-slice with `array_slice($category_ids, 0, 3)`.

### Roles & auth
- Roles: `super_admin`, `editor`, `reporter`, `kontributor`, `pembaca`. `User::hasPermission()` short-circuits `true` for `super_admin`.
- Admin opini routes are gated by `permission:manage_opini`; `/admin/users` + `users/{user}/verifikasi` require `role:super_admin`.
- Controller-based auth, **not** Breeze/Jetstream. `User` implements `MustVerifyEmail`; only `super_admin` may log in unverified. Registrasi umum membuat role `pembaca` (wajib verifikasi email). Kontributor tetap ada untuk penulis yang masuk lewat jalur terpisah. Contributors land on `/panel-kontributor`.
- `AdminSessionTimeout` hardcodes 5 idle minutes (`$idleMinutes = 5`, not env-driven).
- `TRUST_PROXIES` is only set in `render.yaml`, **not** in `.env.example` — set it manually if you reverse-proxy locally.

### Turnstile (read this before touching auth)
- Login/register/reset/resend/verify POSTs validate `cf-turnstile-response` via `App\Rules\Turnstile`. `TURNSTILE_ENABLED` **is now live** (config `services.turnstile.enabled`, default `true`): set `false` to disable the captcha in local dev. When disabled the rule/service short-circuit to pass, the controllers drop `required`/`string` from the rule set, and `partials/turnstile.blade.php` renders nothing (no widget, no Cloudflare script — so no outbound call). **Never set it false in production** (`.env.production`/`.env.example` keep it `true`/unset).
- When enabled, the widget partial renders only if `TURNSTILE_SITE_KEY` is set, but the rule runs regardless. Empty keys ⇒ `TurnstileService::verify()` returns `false` ⇒ every login/register fails.
- Local dev can alternatively keep it enabled with Cloudflare's always-pass dummy keys (`1x00000000000000000000AA` for both site + secret, as in `.env`) — but that makes a real outbound call to `challenges.cloudflare.com`.
- Tests: `phpunit.xml` pins `TURNSTILE_ENABLED=true` so the enabled-path tests still run; disabled-path tests toggle `config(['services.turnstile.enabled' => false])`. Enabled tests **must** `Http::fake()`; see `tests/Feature/LoginTurnstileTest.php`.

### Helpers & rendering
- `app/Helpers/helpers.php` is loaded via `require_once` in `AppServiceProvider::register()` — not composer autoload. Adding a helper file means editing that call.
- `setting($key)` caches per-key forever; `clearSettingCache()` clears `site_settings` + per-key entries. Call it after writing settings.
- `HtmlSanitizer` whitelists CKEditor `body` HTML; controllers call it before persisting, and views print with `{!! !!}`. **The sanitizer is the only XSS defense** — never skip it, and never widen `allowedTags`/`allowedAttributes` casually.
- `seoInternalLinks()` auto-links the first occurrence of each keyword in article bodies (skips `<a>`/`<pre>`/`<code>`/`<script>`/`<style>`).
- `SecurityHeaders::buildCsp()` hardcodes the CSP allowlist. Adding a new external embed/CDN (a new video host, a font, an analytics script) requires editing it or the browser blocks it silently.
- **Favicon/icon are static and must stay opaque.** Apple silently rejects transparent PNGs for `apple-touch-icon`, and Android `maskable` icons need a full-bleed background. The assets in `public/icons/` + `public/favicon.ico` were pre-rendered from `public/logo/logo KU.png` on white with ~18–40% padding (logo fills the maskable safe zone); don't regenerate them from the transparent source. The `favicon` site setting is **no longer read** by the frontend — the `<link>`s point at the bundled files unconditionally, and `public/manifest.json` is plain static. `tests/Feature/FaviconFallbackTest.php` locks this, including that every declared `sizes` matches the real pixel size of the file it points to.
- **`og:image` is emitted from a `@section('share_image')`, not the layout's meta block.** 13 frontend views define their own `@section('meta')`, so anything placed in the layout's `@else` branch silently never renders on those pages. Article pages override the section with their thumbnail (1200×675), falling back to the `logo` setting then `/og-default.jpg`.

## Domain gotchas

- **Headline** (`is_headline`, 7-day `headline_expires_at`) is excluded from "Berita Terbaru" and trending via `scopeExcludeHeadline`. Cap is 6, enforced by `Post::enforceHeadlineLimit($keepId)` — it silently revokes the oldest headline flag. Only Admin `PostController`/`OpiniController` call it (store + update = 4 call sites); Contributor and Video flows never accept `is_headline` (they use `StoreContributorPostRequest` / plain `Request`), so add enforcement there if you ever expose the field.
- **`is_featured`** ("Konten Pilihan") expires 14 days after `published_at` (`Post::FEATURED_EXPIRE_DAYS`). `scopeFeatured()` hides expired rows immediately; `posts:expire-flags` clears the flag. Re-saving an old post in admin re-arms it from scratch.
- **Frontend caching** lives in `AppServiceProvider` view composers: `site_settings` (forever), `frontend_pages` / `trending_posts` (1h), `breaking_news` (5m). Cache invalidation is split by writer: admin Post/Opini/Video controllers call their private `forgetFrontendCaches()` (clears `trending_posts`, `breaking_news`, `frontend_categories`), `Admin\PageController` clears `frontend_pages` directly, `Admin\SettingController` calls `clearSettingCache()`. If you add a cached query, wire up its forget at every write site or the UI goes stale. (`frontend_categories` is forgotten by controllers but never actually written — dead key.)
- **Images**: Intervention Image 3 instantiated inline as `new ImageManager(new Driver)` (GD) — there is no `config/image.php`. All output is WebP 85%. Post/Opini/Video/contributor thumbnails are unconditionally `cover(1200, 675)` (no headline exception). Inline CKEditor uploads use `resizeDown(1200)`. Avatars are `cover(400,400)`.
- **Video**: `video_path` may be a YouTube/Vimeo/TikTok URL or an uploaded file. `video_url` / `video_embed_url` / `video_poster` / `is_tiktok` accessors handle the branching. `fetchVideoThumbnail()` (helpers) makes **real outbound HTTP** to YouTube/TikTok oEmbed — guard it in tests.
- **Story**: own `stories` table (not a post type) — `title` **derived from the selected `category_id`** (admin picks a category; `StoryController` stores `title = category->name`), required `image`, optional `url`, `is_active`, `sort_order`, and `expires_at` set to `now()->addDays(Story::EXPIRE_DAYS)` (=7) on create. Display title comes from `Story::display_title` (`category?->name ?? title`), so category renames reflect immediately and `nullOnDelete` keeps the last-known name. **No count cap** (unlike headline). `Story::scopeActive()` + `scopeSorted()` render it as a ring strip **above the headline** on the homepage only (`HomeController::index` eager-loads `category` and queries it directly — *not* cached, so no `forgetFrontendCaches` needed in `Admin\StoryController`). Image is stored via `resizeDown(1080, 1920)` (aspect-preserving, no crop) in `stories/`; admin `extend` re-arms expiry + reactivates, `toggle` flips `is_active`. Frontend uses the `storyViewer` Alpine component (`resources/js/components/storyViewer.js`) — fullscreen viewer with 6s auto-advance, tap/arrow nav, hold-to-pause; story data is passed through `data-*` attributes, never inline JSON.
- **Views/comments/likes are IP-keyed, not auth-keyed.** `RecordViewJob` dedupes per IP for 10 min and stores an anonymized IP (last octet zeroed).
- **AI chat ("Tanya Berita")**: public RAG over published posts, no embeddings/vector store. Tamu mendapat jatah **pertanyaan gratis terbatas** (`services.gemini.guest_limit`, default 2) yang dihitung per IP (TTL 1 hari). Setelah jatah habis, endpoint mengembalikan `403` dengan `requires_auth: true` dan UI menampilkan ajakan Masuk/Daftar (interaksi). User terautentikasi tidak mengkonsumsi jatah tamu dan tetap tunduk pada rate limit `ai-chat` (10/min + 100/day) serta `services.gemini.daily_limit` (cache counter, default 1000, 0 = off). `POST /tanya-berita` (`Frontend\AiChatController@ask`, middleware `throttle:ai-chat`) → `AiChatService` → `PostRepository::searchForChat()` (keyword + PHP scoring, published-only; portable `LOWER(...) LIKE` so it works on both sqlite and pgsql) → Google Gemini `generateContent` via `Http`. `sources` are built from the DB (not from model text); the answer is rendered **escaped** (`x-text`), never `{!! !!}`. Gemini runs **server-side**, so **no CSP change is needed** — do NOT add googleapis to the allowlist (`SecurityHeaders::buildCsp()` `connect-src` already allows only `'self'` + turnstile + open-meteo). Gated by `config('services.gemini.enabled')` + a non-empty key: widget hidden and endpoint 503 when the key is missing. The model is `GEMINI_MODEL` (default `gemini-2.5-flash`) with a comma-separated `GEMINI_FALLBACK_MODELS` chain tried on any non-2xx (Google retires models — `gemini-2.0-flash` 404s as of 2026-10). **No captcha on this endpoint** (Turnstile is only used for auth): abuse is bounded by the per-IP limiter `ai-chat` (10/min + 100/day), `question` max 300 chars, and a global daily budget cap `services.gemini.daily_limit` (cache counter, default 1000, 0 = off). Frontend: `resources/js/components/floatingHub.js` (Alpine) + `partials/floating-hub.blade.php`, included in the frontend layout. It's a **single FAB (bottom-right) that expands into a menu** — "Tanya Berita AI" + "Masuk/Daftar" (or "Akun Saya"/"Keluar" when authenticated). Untuk `pembaca`, menu tambahan **"Kirim Opini"** ditampilkan. So login and chat share one bubble; a one-time teaser (`localStorage` key `ku_hub_teaser_seen`) advertises the AI chat to visitors. Chat panel has two close actions: **minimize** (keeps the conversation, shows a badge on the FAB while it has messages) and **end** (clears the history, with an inline confirm) — `minimizeChat()`/`requestEnd()`/`endChat()` in `floatingHub.js`.
- Local DB is SQLite (`.env`, `.env.example`); prod is **pgsql** (`render.yaml`). Migrations that use raw `ALTER TABLE ... ADD CONSTRAINT` are pgsql-guarded — keep that guard if you add one.
- Rate limits: login 10/min/IP, register 5/min/IP, password reset 5/min, comments 5/min, likes 30/min, verification 6/min, ai-chat 10/min + 100/day.

## Testing

- `RefreshDatabase` + factories; `phpunit.xml` forces `:memory:` SQLite, `array` cache/session, `sync` queue, and dummy `TURNSTILE_*` keys.
- Roles are created inline — `Role::factory()->create(['slug' => 'editor'])` — then permissions attached via `$role->permissions()->attach(...)`. `UserFactory` emails are verified unless you use the `unverified()` state.
- `tests/Feature/SmokeSeedTest.php` is the exception: it calls `$this->seed()` (full `DatabaseSeeder`, Faker-driven) and smoke-renders `/`, `/berita/{slug}`, `/kecamatan/{slug}`, `/semua-berita`, `/terkini`, `/trending`. Run it after touching frontend queries or views — it's the cheapest regression net here.
- `UserSeeder` hardcodes `role_id` 1/2/3 and depends on `RoleSeeder` inserting in that exact order. Don't reorder or renumber roles.

## Ops

- Deploy: Render (`render.yaml`), two services. `konut-update` (web) runs `artisan serve` and its buildCommand runs `migrate --force`, `CategorySeeder`, `PageSeeder`, `storage:link --force`, `optimize`. `konut-worker` runs `queue:work` + `schedule:work` but **does not migrate** — deploy order matters. Without the worker, `RecordViewJob` and `posts:expire-flags` never run in prod.
- `render.yaml` sets `PHP_CLI_SERVER_WORKERS=4`: `artisan serve` is single-threaded otherwise, and the synchronous Gemini call (up to 25s) would block the whole site. Also set `GEMINI_API_KEY` (and optionally `GEMINI_MODEL`/`GEMINI_DAILY_LIMIT`) in the Render dashboard — empty key hides the Tanya Berita widget.
- Full `DatabaseSeeder` (demo admin `admin@konutupdate.com` / `password`, plus editor/reporter) is **local-only**; production seeds just categories and pages.
- `livewire/livewire` `^4.3` is in `composer.json` but unused — no components, no views, no `wire:` directives (the only trace is a `livewire:navigated` listener in `resources/js/app.js`). Ignore it.
- Frontend is Alpine.js + jQuery/Bootstrap 5/DataTables/Swiper/Lucide; Tailwind 4 is compiled from `resources/css/*.css` with no `tailwind.config.js`.
