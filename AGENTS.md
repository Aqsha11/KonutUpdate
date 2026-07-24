# AGENTS.md — Konut.Update

## Project

Laravel 12 news portal (PHP 8.3+, MySQL/PostgreSQL). Deploys to Render (`render.yaml`).

- **Stack**: Laravel 12 + Livewire 4 + Tailwind CSS v4 + Bootstrap 5 (admin) + CKEditor 5 + DataTables + Alpine.js
- **Database**: MySQL (local dev), PostgreSQL (Render production). SQLite in-memory for tests.
- **Entry point**: `resources/views/frontend/` (public) and `resources/views/admin/` (CMS)

## Key Commands

```bash
composer setup          # full setup: install, env, migrate, npm, build
composer dev            # runs artisan serve + queue + pail + vite concurrently
composer test           # clear config cache then php artisan test
php artisan migrate --force
php artisan db:seed     # runs all seeders
```

## Structure

- `app/Http/Controllers/Frontend/` — public routes (Home, Post, Category, Tag, Search, Page)
- `app/Http/Controllers/Admin/` — CMS routes (Dashboard, Post, Category, Tag, User, Role, Permission, Setting, Ad, Page, Profile)
- `app/Http/Controllers/Auth/LoginController` — single auth controller
- `app/Http/Middleware/AdminMiddleware.php` — checks role slug ∈ {super_admin, editor, reporter}
- `app/Http/Middleware/RoleMiddleware.php` — parameterized `role:super_admin` etc.
- `app/Models/` — User, Post, Category, Tag, Role, Permission, Setting, PageView, Ad, Page
- `app/Services/HtmlSanitizer.php` — whitelist-based HTML sanitizer for CKEditor output
- `app/Helpers/helpers.php` — `setting()`, `clearSettingCache()`, `formatDate()`, `readTime()`, share link generators
- `app/Jobs/RecordViewJob.php` — queued page view tracking (IP anonymized)
- `database/migrations/` — 16 migrations (users, roles, categories, tags, posts, post_tag, settings, page_views, permissions, pages, ads, type+video on posts)

## Frontend Details

- **Vite entry points**: `resources/css/app.css`, `resources/css/admin.css`, `resources/js/app.js`, `resources/js/admin.js`
- `resources/css/app.css` — Tailwind v4 + custom theme (green primary, orange accent, Material Design tokens)
- `resources/js/app.js` — Alpine.js + Lucide icons + toast system + scroll reveal + infinite scroll + reading progress
- Brand: green (#189B39) primary, orange (#F58220) accent
- Indonesian locale: dates via `Carbon::locale('id')`, UI text in Bahasa Indonesia

## Caching

`AppServiceProvider` caches and injects into all views: `site_settings`, `frontend_categories`, `trending_posts`, `breaking_news`, sidebar/in-article ads. Cache keys: `site_settings`, `frontend_categories`, `trending_posts`, `breaking_news`, `sidebar_ads_top`, `sidebar_ads_bottom`, `in_article_ads`, `frontend_pages`, `setting_{key}`.

Run `clearSettingCache()` after changing settings to invalidate.

## Auth & Roles

- Middleware aliases registered in `bootstrap/app.php`: `admin` → `AdminMiddleware`, `role` → `RoleMiddleware`
- Admin routes require `auth` + `admin` middleware
- User management (`/admin/users`) additionally requires `role:super_admin`
- Role slugs: `super_admin`, `editor`, `reporter`

## Deploy

Render blueprint: `render.yaml`. Production uses PostgreSQL. Build: `composer install --no-dev && npm ci && npm run build && migrate && seed (CategorySeeder, PageSeeder) && storage:link`.

## Tests

PHPUnit 11 with SQLite `:memory:`. Run: `composer test`.

## Git Remote

`origin` → `https://github.com/Aqsha11/KonutUpdate.git`
