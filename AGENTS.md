# AGENTS.md — Konut.Update (Laravel 12)

News portal for Konawe Utara. Uses **both** Tailwind CSS v4 (frontend) and Bootstrap 5 (admin panel).

## Commands

| Command | What it does |
|---|---|
| `composer run test` | `php artisan config:clear && php artisan test` |
| `composer run dev` | `artisan serve`, `queue:listen`, `pail`, `npm run dev` via concurrently |
| `composer run setup` | Full bootstrap: install, `.env` copy, `key:generate`, `migrate`, `npm i && npm run build` |
| `npm run dev` | Vite dev server only |
| `npm run build` | `vite build` |
| `php artisan migrate` | Run migrations (SQLite) |
| `php artisan test --filter=ExampleTest` | Single test class |
| `vendor/bin/phpunit tests/Unit/ExampleTest.php` | Run one file |
| `./vendor/bin/pint` | Format PHP with Laravel Pint |

## Code style

- **Laravel Pint** (default config) for PHP
- `.editorconfig`: 4-space indent, LF, UTF-8
- Post model uses `SoftDeletes`, status field: `published` / `draft`

## Testing

- `phpunit.xml` forces `APP_ENV=testing`, `DB_CONNECTION=sqlite` + `:memory:`, `CACHE_STORE=array`, `QUEUE_CONNECTION=sync`, `SESSION_DRIVER=array`
- Unit tests: `PHPUnit\Framework\TestCase`; Feature tests: `Tests\TestCase`
- No `RefreshDatabase` trait by default in Feature tests — add manually
- No seeder-based test deps; factory data per test

## Database

- **SQLite** for local and test (`.env` has `DB_CONNECTION=sqlite`)
- Session and queue use `database` driver in production; cache uses `file`
- Local SQLite file: `database/database.sqlite`

## Frontend

- **Tailwind CSS v4** — no `tailwind.config.js`; `@import 'tailwindcss'` in `resources/css/app.css` with `@theme` block
- **Vite** entry points: `resources/css/{app.css,admin.css}` + `resources/js/{app.js,admin.js}`
- Frontend: **Alpine.js 3** + Tailwind + Lucide icons + Font Awesome brands
- Admin: **Bootstrap 5** + jQuery + DataTables + CKEditor 5 + Font Awesome + Bootstrap Icons
- Axios bound to `window.axios` in `resources/js/bootstrap.js`
- Infinite scroll via `IntersectionObserver` (`resources/js/app.js`)
- Dark mode: `x-data` on `<html>` + `localStorage`, class toggling
- CKEditor uploads images to `/admin/posts/upload-image` via custom upload adapter (`resources/js/admin.js`)

## Architecture

### Routes (`routes/web.php`)
- Frontend: `/`, `/berita/{slug}`, `/kategori/{slug}`, `/tag/{slug}`, `/search`, `/tentang-kami`, `/pedoman-media-siber`, `/privacy-policy`, `/p/{slug}`
- Ad click tracking: `/iklan/{ad}` — increments click count and redirects
- SEO: `/robots.txt` (literal), `/sitemap.xml` (view)
- Auth: `/login` (custom `Auth\LoginController`, no Breeze/Jetstream)
- Admin: `prefix /admin`, middleware `['auth', 'admin']`, resource controllers for posts, categories, tags, pages, roles, permissions, users, ads
- Admin settings: `/admin/settings` (GET + PUT)
- Admin profile: `/admin/profile` (GET + PUT)
- User management under `/admin/users` requires `role:super_admin` middleware

### Key files
- `docs/project-plan.md` — full project plan (554 lines)
- `app/Helpers/helpers.php` — `setting($key, $default)` cached with `Cache::rememberForever`; `clearSettingCache($key?)`
- `app/Services/HtmlSanitizer.php` — CKEditor HTML output sanitizer
- `app/Http/Controllers/` — separate `Frontend/` and `Admin/` namespaces; all controller-based
- Custom theme colors: `--color-primary: #189B39`, `--color-accent: #F58220`

### Post model scopes
`published()`, `draft()`, `featured()`, `breaking()`, `popular($minViews)`, `bySlug($slug)`
