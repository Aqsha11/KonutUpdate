# Project Plan — Konut.Update

> Portal Berita Profesional berbasis Laravel 12  
> Tech Stack: PHP 8.3+ | MySQL | Bootstrap 5 | Blade | CKEditor 5 | DataTables | Font Awesome

---

## 1. Audit Struktur Project

### Status Saat Ini

| Komponen | Status | Keterangan |
|---|---|---|
| Laravel 12 | ✅ Terinstall | Versi `^12.0`, PHP `^8.2` |
| Migrations (8 custom) | ✅ Siap | `roles`, `categories`, `tags`, `posts`, `post_tag`, `settings`, `page_views`, plus modifikasi `users` |
| Eloquent Models | ⚠️ Sebagian | Hanya `User` — belum ada model untuk tabel custom |
| Controllers | ❌ Kosong | Hanya `Controller.php` abstrak |
| Middleware | ❌ Kosong | Tidak ada middleware kustom |
| Routes | ⚠️ Minimal | Hanya `GET /` di `web.php`, belum ada `api.php` |
| Views | ❌ Kosong | Hanya `welcome.blade.php` default Laravel |
| Auth Scaffolding | ❌ Tidak ada | Belum pakai Breeze / Jetstream |
| Frontend Build | ⚠️ Tailwind v4 | Vite + Tailwind CSS v4, belum ada Bootstrap 5 |
| Database Config | ✅ MySQL | `.env` mengarah ke `konut_update` (MySQL), testing pakai SQLite `:memory:` |
| Testing | ⚠️ Dasar | Hanya 2 test example |
| Docs | ❌ Tidak ada | Perlu dibuat |

### Kesimpulan Audit

Project adalah **skeleton awal** portal berita. Semua migration sudah siap untuk tabel inti, namun **belum ada satu pun** model, controller, view, atau middleware yang dibangun. Frontend masih menggunakan Tailwind v4 default Laravel — perlu migrasi ke Bootstrap 5 sesuai spesifikasi.

---

## 2. Roadmap Implementasi

```
Fase 1: Foundation (Database & Models)
├── Setup Bootstrap 5 + DataTables + CKEditor 5 + Font Awesome
├── Buat semua Eloquent Models & Relationships
├── Buat Factory & Seeder untuk data dummy
├── Setup Filament / Custom Admin Panel
│
Fase 2: Admin Panel (CMS)
├── Middleware Role & Admin
├── CRUD Kategori & Tag
├── CRUD Post (CKEditor 5, thumbnail upload)
├── Manajemen User & Role
├── Manajemen Settings (website config)
├── Dashboard Statistik
│
Fase 3: Frontend Publik
├── Layout utama Bootstrap 5
├── Halaman Beranda (featured, breaking, terbaru)
├── Halaman Kategori & Tag
├── Halaman Detail Berita (dengan page view tracking)
├── Pencarian
├── Pagination & DataTables
│
Fase 4: Fitur Lanjutan
├── Halaman Statis (Tentang, Kontak, Kebijakan)
├── Newsletter / Subscriber
├── Sitemap & SEO
├── Komentar (moderasi)
├── API endpoints (untuk mobile/frontend lain)
│
Fase 5: Polish & Deploy
├── Testing coverage
├── Optimasi query
├── Caching
├── Security hardening
├── Dokumentasi
```

---

## 3. Daftar Fitur

### Frontend Publik

| # | Fitur | Prioritas |
|---|---|---|
| 1 | Beranda — menampilkan berita terbaru, featured, breaking news | P0 |
| 2 | Detail Berita — konten lengkap, share buttons, page view | P0 |
| 3 | Daftar Kategori — filter berita per kategori | P0 |
| 4 | Pencarian — full-text search judul & konten | P0 |
| 5 | Tag — filter berita per tag | P1 |
| 6 | Related Posts — berdasarkan kategori yang sama | P1 |
| 7 | Arsip — filter berdasarkan tanggal/bulan | P1 |
| 8 | Halaman Statis — tentang, kontak, kebijakan privasi, pedoman media | P1 |
| 9 | RSS Feed | P2 |
| 10 | Sitemap XML (SEO) | P2 |
| 11 | Navigasi responsif dengan dropdown kategori | P0 |

### Admin Panel (CMS)

| # | Fitur | Prioritas |
|---|---|---|
| 1 | Dashboard — statistik total posts, views, users | P0 |
| 2 | Kelola Post — CRUD dengan CKEditor 5, upload thumbnail, slug otomatis | P0 |
| 3 | Kelola Kategori — CRUD | P0 |
| 4 | Kelola Tag — CRUD | P0 |
| 5 | Kelola User — CRUD, role assignment | P1 |
| 6 | Kelola Role & Permission | P1 |
| 7 | Pengaturan Website — nama, logo, meta, sosial media, iklan | P0 |
| 8 | Manajemen Halaman Statis | P1 |
| 9 | DataTables — server-side processing untuk semua tabel | P0 |
| 10 | Autentikasi Admin — login/logout | P0 |
| 11 | Grafik & Statistik — views per hari/bulan | P2 |

---

## 4. ERD Database

```
┌─────────────────────────────────────────────────────────────────────┐
│                           ENTITY RELATIONSHIP                        │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌──────────┐       ┌────────────┐       ┌──────────────┐          │
│  │  roles   │──1:N──│   users    │──1:N──│   posts      │          │
│  └──────────┘       └────────────┘       └──────┬───────┘          │
│                        │                         │                  │
│                        │ 1:N                     │ N:M              │
│                        │                         │ ┌──────────┐    │
│                        ▼                         ├─│post_tag  │    │
│                  ┌──────────────┐                │ └──────────┘    │
│                  │ page_views   │◄─N:1──┘        │                 │
│                  └──────────────┘                 │ N:1             │
│                                                  │                 │
│                                                  ▼                 │
│                                          ┌──────────────┐          │
│                                          │ categories   │          │
│                                          └──────────────┘          │
│                                                                     │
│  ┌──────────┐       ┌──────────────┐                                │
│  │  tags    │──1:N──│  post_tag    │                                │
│  └──────────┘       └──────────────┘                                │
│                                                                     │
│  ┌──────────┐                                                       │
│  │settings  │  (key-value store)                                    │
│  └──────────┘                                                       │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘

RELATIONSHIPS:

  roles     1 ──── N users        : Satu role dimiliki banyak user
  users     1 ──── N posts        : Satu user menulis banyak berita
  categories 1 ──── N posts       : Satu kategori memiliki banyak berita
  posts     N ──── M tags         : Banyak-to-banyak via post_tag
  posts     1 ──── N page_views   : Satu post memiliki banyak page view
```

### Detail Entity & Attributes

**roles**
| Column | Type | Constraint |
|---|---|---|
| id | bigint PK | auto-increment |
| name | string(255) | NOT NULL |
| slug | string(255) | UNIQUE, NOT NULL |
| description | text | NULLABLE |
| created_at | timestamp | |
| updated_at | timestamp | |

**users** (+ default Laravel columns)
| Column | Type | Constraint |
|---|---|---|
| id | bigint PK | auto-increment |
| name | string(255) | NOT NULL |
| email | string(255) | UNIQUE |
| password | string(255) | NOT NULL |
| role_id | bigint FK → roles.id | NULLABLE, ON DELETE SET NULL |
| avatar | string(255) | NULLABLE *(belum ada migration)* |
| timestamps | | |

**categories**
| Column | Type | Constraint |
|---|---|---|
| id | bigint PK | auto-increment |
| name | string(255) | NOT NULL |
| slug | string(255) | UNIQUE, NOT NULL |
| description | text | NULLABLE |
| created_at | timestamp | |
| updated_at | timestamp | |

**tags**
| Column | Type | Constraint |
|---|---|---|
| id | bigint PK | auto-increment |
| name | string(255) | NOT NULL |
| slug | string(255) | UNIQUE, NOT NULL |
| created_at | timestamp | |
| updated_at | timestamp | |

**posts**
| Column | Type | Constraint |
|---|---|---|
| id | bigint PK | auto-increment |
| user_id | bigint FK → users.id | ON DELETE CASCADE |
| category_id | bigint FK → categories.id | NULLABLE, ON DELETE SET NULL |
| title | string(255) | NOT NULL |
| slug | string(255) | UNIQUE, NOT NULL |
| excerpt | text | NULLABLE |
| body | longtext | NOT NULL |
| thumbnail | string(255) | NULLABLE |
| status | enum('draft','published') | DEFAULT 'draft' |
| is_breaking | boolean | DEFAULT false |
| is_featured | boolean | DEFAULT false |
| published_at | timestamp | NULLABLE |
| views_count | unsignedBigInteger | DEFAULT 0 |
| timestamps | | |

**post_tag** (pivot)
| Column | Type | Constraint |
|---|---|---|
| id | bigint PK | auto-increment |
| post_id | bigint FK → posts.id | ON DELETE CASCADE |
| tag_id | bigint FK → tags.id | ON DELETE CASCADE |
| timestamps | | |
| UNIQUE(post_id, tag_id) | | *(perlu ditambahkan)* |

**page_views**
| Column | Type | Constraint |
|---|---|---|
| id | bigint PK | auto-increment |
| post_id | bigint FK → posts.id | NULLABLE, ON DELETE CASCADE |
| ip_address | string(45) | NULLABLE |
| user_agent | text | NULLABLE |
| created_at | timestamp | |

**settings**
| Column | Type | Constraint |
|---|---|---|
| id | bigint PK | auto-increment |
| key | string(255) | UNIQUE, NOT NULL |
| value | text | NULLABLE |
| timestamps | | |

---

## 5. Daftar Migration

### Existing (8 migration — sudah dibuat, tinggal di-run)

| # | File | Tabel |
|---|---|---|
| 1 | `0001_01_01_000000_create_users_table` | `users`, `password_reset_tokens`, `sessions` |
| 2 | `0001_01_01_000001_create_cache_table` | `cache`, `cache_locks` |
| 3 | `0001_01_01_000002_create_jobs_table` | `jobs`, `job_batches`, `failed_jobs` |
| 4 | `2024_01_01_000001_create_roles_table` | `roles` |
| 5 | `2024_01_01_000002_add_role_id_to_users_table` | — (alter `users`) |
| 6 | `2024_01_01_000003_create_categories_table` | `categories` |
| 7 | `2024_01_01_000004_create_tags_table` | `tags` |
| 8 | `2024_01_01_000005_create_posts_table` | `posts` |
| 9 | `2024_01_01_000006_create_post_tag_table` | `post_tag` |
| 10 | `2024_01_01_000007_create_settings_table` | `settings` |
| 11 | `2024_01_01_000008_create_page_views_table` | `page_views` |

### Additional Migration yang Dibutuhkan

| # | Tujuan | Perubahan |
|---|---|---|
| 1 | **Add avatar to users** | Tambah `avatar` (string, nullable) ke `users` |
| 2 | **Add unique constraint pivot** | Tambah unique key `(post_id, tag_id)` di `post_tag` |
| 3 | **Add created_by to settings** | Tambah `created_by` (FK → users) di `settings` |
| 4 | **Create media table** | Opsional — untuk library gambar terpusat (jika diperlukan) |

---

## 6. Daftar Model

### Existing (1 model)

| Model | Tabel | Hubungan Perlu Ditambahkan |
|---|---|---|
| `User` | `users` | Belum ada `role()`, `posts()`, dll |

### Model yang Harus Dibuat

| # | Model | Tabel | Relations |
|---|---|---|---|
| 1 | `Role` | `roles` | `hasMany(User)` |
| 2 | `Category` | `categories` | `hasMany(Post)` |
| 3 | `Tag` | `tags` | `belongsToMany(Post)` |
| 4 | `Post` | `posts` | `belongsTo(User)`, `belongsTo(Category)`, `belongsToMany(Tag)`, `hasMany(PageView)` |
| 5 | `PageView` | `page_views` | `belongsTo(Post)` |
| 6 | `Setting` | `settings` | — |

### Attributes per Model (yang perlu di-cast / diatur)

| Model | Casts | Fillable |
|---|---|---|
| `User` | `password` → `hashed`, `email_verified_at` → `datetime` | `name`, `email`, `password`, `role_id`, `avatar` |
| `Post` | `is_breaking` → `boolean`, `is_featured` → `boolean`, `published_at` → `datetime` | Semua kecuali `id`, `views_count`, `timestamps` |
| `Role` | — | `name`, `slug`, `description` |
| `Category` | — | `name`, `slug`, `description` |
| `Tag` | — | `name`, `slug` |
| `Setting` | — | `key`, `value` |
| `PageView` | — | `post_id`, `ip_address`, `user_agent` |

---

## 7. Daftar Controller

### Frontend (Public)

| # | Controller | Method | Routes | Fungsi |
|---|---|---|---|---|
| 1 | `Front\HomeController` | `index()` | `GET /` | Beranda — menampilkan featured, breaking, terbaru |
| 2 | `Front\PostController` | `show($slug)` | `GET /berita/{slug}` | Detail berita + tracking page view |
| 3 | `Front\CategoryController` | `index()`, `show($slug)` | `GET /kategori`, `/kategori/{slug}` | Daftar & filter kategori |
| 4 | `Front\TagController` | `show($slug)` | `GET /tag/{slug}` | Daftar berita per tag |
| 5 | `Front\SearchController` | `index()` | `GET /cari` | Pencarian berita |
| 6 | `Front\PageController` | `show($slug)` | `GET /halaman/{slug}` | Halaman statis |

### Admin (CMS)

| # | Controller | Method | Routes | Fungsi |
|---|---|---|---|---|
| 1 | `Admin\DashboardController` | `index()` | `GET /admin` | Dashboard statistik |
| 2 | `Admin\AuthController` | `login()`, `logout()` | `GET/POST /admin/login` | Autentikasi admin |
| 3 | `Admin\PostController` | `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()` | `/admin/posts` | CRUD berita dengan CKEditor 5 |
| 4 | `Admin\CategoryController` | `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()` | `/admin/categories` | CRUD kategori |
| 5 | `Admin\TagController` | `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()` | `/admin/tags` | CRUD tag |
| 6 | `Admin\UserController` | `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()` | `/admin/users` | Manajemen user |
| 7 | `Admin\RoleController` | `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()` | `/admin/roles` | Manajemen role |
| 8 | `Admin\SettingController` | `index()`, `update()` | `/admin/settings` | Pengaturan website |
| 9 | `Admin\PageViewController` | `index()` | `/admin/statistics` | Statistik views |
| 10 | `Admin\MediaController` | `index()`, `upload()`, `delete()` | `/admin/media` | Manajemen media/gambar |

---

## 8. Daftar Middleware

| # | Middleware | Fungsi | Route Group |
|---|---|---|---|
| 1 | `AdminMiddleware` | Mengecek role user apakah admin/author/editor | `admin.*` |
| 2 | `CheckRole` | Middleware parameterized — `role:admin|editor` | `admin.*` |
| 3 | `TrackPageView` | Opsional — middleware global tracking page views | `web` (opsional) |

### Registrasi Middleware di `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

---

## 9. Daftar Halaman Frontend

| # | Route | View | Deskripsi |
|---|---|---|---|
| 1 | `GET /` | `front.home` | Beranda — slider featured, breaking news, grid/list berita terbaru, sidebar trending |
| 2 | `GET /berita/{slug}` | `front.post.show` | Detail berita — thumbnail, body (CKEditor), tags, share buttons, related posts, komentar |
| 3 | `GET /kategori` | `front.categories.index` | Daftar semua kategori |
| 4 | `GET /kategori/{slug}` | `front.categories.show` | Berita per kategori — list dengan pagination |
| 5 | `GET /tag/{slug}` | `front.tags.show` | Berita per tag |
| 6 | `GET /cari` | `front.search.index` | Halaman pencarian — query string `?q=keyword` |
| 7 | `GET /halaman/{slug}` | `front.pages.show` | Halaman statis (tentang, kontak, kebijakan privasi, pedoman media) |
| 8 | `GET /arsip` | `front.archive.index` | Arsip berita filter bulan/tahun |

### Layout Frontend

| Layout | Deskripsi |
|---|---|
| `layouts/front.blade.php` | Layout utama — navbar responsif dengan dropdown kategori, header logo, sidebar, footer |
| `layouts/partials/navbar.blade.php` | Navigasi atas — logo, menu utama, pencarian |
| `layouts/partials/header.blade.php` | Header — breaking news ticker |
| `layouts/partials/sidebar.blade.php` | Sidebar — trending, kategori, tag cloud |
| `layouts/partials/footer.blade.php` | Footer — menu, sosial media, copyright |

---

## 10. Daftar Halaman Admin

| # | Route | View | Deskripsi |
|---|---|---|---|
| 1 | `GET /admin` | `admin.dashboard` | Dashboard — kartu statistik (total posts, views, users), grafik views mingguan, daftar post terbaru |
| 2 | `GET /admin/login` | `admin.auth.login` | Form login admin |
| 3 | `POST /admin/login` | — | Proses autentikasi |
| 4 | `POST /admin/logout` | — | Logout |
| 5 | `GET /admin/posts` | `admin.posts.index` | DataTable server-side — semua post, filter status, bulk actions |
| 6 | `GET /admin/posts/create` | `admin.posts.form` | Form create — CKEditor 5, upload thumbnail, pilih kategori, tags |
| 7 | `POST /admin/posts` | — | Store post |
| 8 | `GET /admin/posts/{id}/edit` | `admin.posts.form` | Form edit |
| 9 | `PUT /admin/posts/{id}` | — | Update post |
| 10 | `DELETE /admin/posts/{id}` | — | Hapus post |
| 11 | `GET /admin/categories` | `admin.categories.index` | DataTable kategori |
| 12 | `GET /admin/categories/create` | `admin.categories.form` | Form create kategori |
| 13 | `POST /admin/categories` | — | Store kategori |
| 14 | `GET /admin/categories/{id}/edit` | `admin.categories.form` | Form edit |
| 15 | `PUT /admin/categories/{id}` | — | Update kategori |
| 16 | `DELETE /admin/categories/{id}` | — | Hapus kategori |
| 17 | `GET /admin/tags` | `admin.tags.index` | DataTable tag |
| 18 | `GET /admin/tags/create` | `admin.tags.form` | Form create tag |
| 19 | `POST /admin/tags` | — | Store tag |
| 20 | `GET /admin/tags/{id}/edit` | `admin.tags.form` | Form edit |
| 21 | `PUT /admin/tags/{id}` | — | Update tag |
| 22 | `DELETE /admin/tags/{id}` | — | Hapus tag |
| 23 | `GET /admin/users` | `admin.users.index` | DataTable user |
| 24 | `GET /admin/users/create` | `admin.users.form` | Form create user |
| 25 | `POST /admin/users` | — | Store user |
| 26 | `GET /admin/users/{id}/edit` | `admin.users.form` | Form edit — termasuk role & avatar |
| 27 | `PUT /admin/users/{id}` | — | Update user |
| 28 | `DELETE /admin/users/{id}` | — | Hapus user (kecuali diri sendiri) |
| 29 | `GET /admin/roles` | `admin.roles.index` | DataTable role |
| 30 | `GET /admin/roles/create` | `admin.roles.form` | Form create role |
| 31 | `POST /admin/roles` | — | Store role |
| 32 | `GET /admin/roles/{id}/edit` | `admin.roles.form` | Form edit |
| 33 | `PUT /admin/roles/{id}` | — | Update role |
| 34 | `DELETE /admin/roles/{id}` | — | Hapus role |
| 35 | `GET /admin/settings` | `admin.settings.index` | Form pengaturan website — nama, deskripsi, logo, favicon, sosial media, meta SEO, kode Google Analytics, kode iklan |
| 36 | `PUT /admin/settings` | — | Update settings |
| 37 | `GET /admin/statistics` | `admin.statistics.index` | Grafik views, popular posts, views by category |
| 38 | `GET /admin/media` | `admin.media.index` | Library gambar — grid upload, delete |
| 39 | `POST /admin/media/upload` | — | Upload via CKEditor 5 atau drag-drop |

### Layout Admin

| Layout | Deskripsi |
|---|---|
| `layouts/admin.blade.php` | Layout master admin — sidebar navigasi, top bar, content area |
| `layouts/partials/admin-sidebar.blade.php` | Sidebar — menu navigasi berbasis role |
| `layouts/partials/admin-navbar.blade.php` | Top bar — user info, notifikasi, logout |

---

## Catatan Tambahan

### Perubahan Frontend Stack

Spesifikasi meminta Bootstrap 5, namun Laravel 12 scaffold menggunakan Tailwind CSS v4. Perlu:
1. Install Bootstrap 5 via npm (`bootstrap`, `@popperjs/core`)
2. Install DataTables via npm (`datatables.net-bs5`)
3. Install Font Awesome via npm (`@fortawesome/fontawesome-free`)
4. Install CKEditor 5 via npm (`@ckeditor/ckeditor5-build-classic`)
5. Update `vite.config.js` untuk entry points baru
6. Update `resources/css/app.css` — import Bootstrap, DataTables, Font Awesome
7. Update `resources/js/app.js` — import Bootstrap JS, DataTables, CKEditor

### Route Structure

```php
// Web Routes
Route::prefix('/')->group(function () {
    // Frontend routes
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['guest'])->group(function () {
        // Auth routes
    });
    Route::middleware(['auth', 'role:admin|editor|author'])->group(function () {
        // Admin routes
    });
});
```

### Struktur Direktori yang Diharapkan

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── PostController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── TagController.php
│   │   │   ├── UserController.php
│   │   │   ├── RoleController.php
│   │   │   ├── SettingController.php
│   │   │   ├── PageViewController.php
│   │   │   └── MediaController.php
│   │   ├── Front/
│   │   │   ├── HomeController.php
│   │   │   ├── PostController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── TagController.php
│   │   │   ├── SearchController.php
│   │   │   └── PageController.php
│   │   └── Controller.php
│   └── Middleware/
│       ├── AdminMiddleware.php
│       └── CheckRole.php
├── Models/
│   ├── User.php        (update)
│   ├── Role.php        (new)
│   ├── Category.php    (new)
│   ├── Tag.php         (new)
│   ├── Post.php        (new)
│   ├── PageView.php    (new)
│   └── Setting.php     (new)
├── Services/
│   └── PageViewService.php   (opsional — logic tracking view)
└── ...

resources/views/
├── layouts/
│   ├── front.blade.php
│   └── admin.blade.php
├── front/
│   ├── home.blade.php
│   ├── post/
│   │   └── show.blade.php
│   ├── categories/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── tags/
│   │   └── show.blade.php
│   ├── search/
│   │   └── index.blade.php
│   ├── pages/
│   │   └── show.blade.php
│   └── partials/
│       ├── navbar.blade.php
│       ├── header.blade.php
│       ├── sidebar.blade.php
│       └── footer.blade.php
└── admin/
    ├── auth/
    │   └── login.blade.php
    ├── dashboard.blade.php
    ├── posts/
    │   ├── index.blade.php
    │   └── form.blade.php
    ├── categories/
    │   ├── index.blade.php
    │   └── form.blade.php
    ├── tags/
    │   ├── index.blade.php
    │   └── form.blade.php
    ├── users/
    │   ├── index.blade.php
    │   └── form.blade.php
    ├── roles/
    │   ├── index.blade.php
    │   └── form.blade.php
    ├── settings/
    │   └── index.blade.php
    ├── statistics/
    │   └── index.blade.php
    ├── media/
    │   └── index.blade.php
    └── partials/
        ├── sidebar.blade.php
        └── navbar.blade.php
```
