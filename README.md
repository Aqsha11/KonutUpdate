# Konut.Update — Portal Berita Konawe Utara

Portal berita berbasis **Laravel 12** untuk Konawe Utara (Konut.Update). Sistem berita lengkap dengan konten artikel/video, sistem headline carousel, manajemen kategori/tag/kecamatan, iklan, komentar, like, serta role-based admin panel.

---

## Fitur

### Frontend
- **Hero Carousel** — Menampilkan headline berita (max 9) dengan auto-slide 5 detik
- **Kategori** — Kriminal, Pemerintahan, Tambang, Ekonomi, Olahraga (dengan struktur hero + trending + terbaru)
- **Filter Kecamatan** — Berita bisa difilter per kecamatan
- **Pencarian** — Full-text search di judul & body
- **Trending** — Berita terpopuler berdasarkan views
- **Detail Berita** — Menampilkan judul, thumbnail, konten (CKEditor), kategori, tag, author
- **Komentar** — Pengunjung bisa komentar (perlu approval admin)
- **Like** — Like/unlike per IP address
- **Social Share** — Facebook, WhatsApp, Telegram, Twitter
- **Iklan** — Sidebar & inline artikel (dengan manajemen jadwal tayang)
- **RSS Feed** — `/feed`
- **Sitemap XML** — `/sitemap.xml`
- **Breaking News** — Ticker berita terbaru di header
- **Responsive** — Tailwind CSS dengan tampilan mobile & desktop

### Admin Panel (`/admin`)
- **Dashboard** — Statistik berita, views, komentar (chart.js interaktif)
- **Berita** — CRUD dengan CKEditor 5, Cropper.js (16:9 / bebas crop)
- **Kategori** — CRUD
- **Tag** — CRUD
- **Kecamatan** — CRUD dengan sort order
- **Halaman Statis** — About, Pedoman, Privasi, Kontak
- **Iklan** — CRUD dengan posisi, jadwal tayang, tracking klik
- **Komentar** — Approve/reject/hapus
- **Users** — CRUD (super_admin only)
- **Roles & Permissions** — Manajemen role & permission
- **Settings** — Site name, deskripsi, logo, meta, sosial media, Google Analytics
- **Profile** — Edit profile admin

### Sistem Headline
- Toggle `is_headline` per post
- Tampil di hero carousel (max 9 post, 3 per slide)
- **Thumbnail bebas crop** — Saat headline ON, crop & resize tidak dipaksakan (ukuran asli)
- Non-headline: crop paksa 16:9 (1200×675), resize max 1200px
- Headline di-exclude dari daftar "Berita Terbaru" & "Trending"

---

## Tech Stack

| Bagian | Teknologi |
|--------|-----------|
| **Backend** | PHP 8.2+, Laravel 12 |
| **Frontend** | TailwindCSS 4, Alpine.js 3, Bootstrap 5 |
| **Editor** | CKEditor 5 (Classic Build + Alignment) |
| **Image Crop** | Cropper.js |
| **Carousel** | CSS murni (flex + transform) |
| **Ikon** | Lucide Icons, Bootstrap Icons |
| **Database** | MySQL / SQLite |
| **Image Processing** | Intervention Image 3.x (GD Driver) |
| **Build** | Vite + Laravel Vite Plugin |
| **Auth** | Laravel session-based (login manual) |
| **Pagination** | Tailwind-style |

---

## Struktur Database

### Tabel Utama

| Tabel | Keterangan |
|-------|------------|
| `posts` | Berita (artikel/video), soft deletes |
| `categories` | Kategori berita |
| `tags` | Tag berita |
| `post_categories` | Pivot many-to-many post ↔ kategori |
| `post_tag` | Pivot many-to-many post ↔ tag |
| `kecamatans` | Kecamatan (dengan sort_order) |
| `comments` | Komentar (perlu approval) |
| `likes` | Like per IP address |
| `page_views` | Page view tracking |
| `ads` | Iklan (dengan posisi & jadwal) |
| `pages` | Halaman statis |
| `users` | User admin |
| `roles` | Role (super_admin, editor, reporter) |
| `permissions` | Permission individual |
| `permission_role` | Pivot permission ↔ role |
| `settings` | Key-value settings |
| `sessions` | Session database driver |

### Kolom Penting `posts`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint (PK) | |
| `user_id` | bigint (FK) | Author |
| `title` | string | Judul berita |
| `slug` | string (unique) | URL slug |
| `excerpt` | text | Ringkasan |
| `body` | longText | Konten HTML |
| `thumbnail` | string (nullable) | Path thumbnail |
| `type` | enum('article','video') | Jenis berita |
| `video_path` | string (nullable) | File path / URL |
| `status` | enum('draft','published') | Status publikasi |
| `is_breaking` | boolean | Breaking news |
| `is_headline` | boolean | Headline carousel |
| `kecamatan_id` | bigint (FK, nullable) | Kecamatan |
| `published_at` | timestamp | Waktu publikasi |
| `views_count` | integer | Counter views |
| `deleted_at` | timestamp (soft delete) | |

---

## Routes

### Frontend

| Method | URI | Controller |
|--------|-----|------------|
| GET | `/` | `HomeController@index` |
| GET | `/berita/{slug}` | `PostController@show` |
| GET | `/kategori/{slug}` | `CategoryController@show` |
| GET | `/tag/{slug}` | `TagController@show` |
| GET | `/kecamatan/{slug}` | `KecamatanController@show` |
| GET | `/trending` | `TrendingController@index` |
| GET | `/search` | `SearchController@index` |
| POST | `/berita/{post}/komentar` | `CommentController@store` |
| POST | `/berita/{post}/like` | `LikeController@toggle` |
| GET | `/tentang-kami` | `PageController@about` |
| GET | `/pedoman-media-siber` | `PageController@pedoman` |
| GET | `/privacy-policy` | `PageController@privacy` |
| GET | `/kontak` | `PageController@kontak` |
| GET | `/p/{slug}` | `PageController@show` |
| GET | `/feed` | RSS feed |
| GET | `/sitemap.xml` | XML sitemap |

### Admin (`/admin`, middleware: `auth` + `admin`)

| Method | URI | Controller |
|--------|-----|------------|
| GET | `/admin` | `DashboardController@index` |
| Resource | `/admin/posts` | `PostController` |
| POST | `/admin/posts/{post}/publish` | `PostController@publish` |
| POST | `/admin/posts/{post}/draft` | `PostController@draft` |
| POST | `/admin/posts/upload-image` | `PostController@uploadImage` |
| Resource | `/admin/categories` | `CategoryController` |
| Resource | `/admin/kecamatans` | `KecamatanController` |
| Resource | `/admin/tags` | `TagController` |
| Resource | `/admin/pages` | `PageController` |
| Resource | `/admin/ads` | `AdController` |
| Resource | `/admin/comments` | `CommentController` |
| Resource | `/admin/users` | `UserController` (super_admin) |
| Resource | `/admin/roles` | `RoleController` |
| Resource | `/admin/permissions` | `PermissionController` |
| GET/PUT | `/admin/profile` | `ProfileController` |
| GET/PUT | `/admin/settings` | `SettingController` |

---

## Cara Install

```bash
# Clone
git clone <repo-url>
cd berita

# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=berita
# DB_USERNAME=root
# DB_PASSWORD=

# Migrate & seed
php artisan migrate --seed

# Storage link
php artisan storage:link

# Build assets
npm run build

# Jalankan
php artisan serve
```

### User Default (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@konutupdate.com | password |

---

## Image Handling

### Upload Path
- **Thumbnail:** `storage/app/public/thumbnails/`
- **CKEditor Images:** `storage/app/public/uploads/images/`

### Proses Upload
1. **Client-side:** Cropper.js crop modal (16:9 untuk non-headline, bebas untuk headline)
2. **Server-side:** Intervention Image resize + konversi ke WebP 85% quality
   - Headline: **tanpa resize** (ukuran asli dipertahankan untuk kualitas tampilan hero)
   - Non-headline: `resizeDown(1200px)` → maksimal 1200px lebar

### Format
- Input: JPEG, PNG, JPG, GIF, WebP
- Output: WebP (kualitas 85)
- Max file: 2MB (thumbnail), 5MB (CKEditor upload)

---

## Alur Headline Carousel

1. Admin centang "Headline" di form create/edit berita
2. `PostRepository::getHeadlinePosts(9)` → ambil max 9 post headline terbaru yang published
3. Di-homepage, 9 post di-chunk jadi 3 slide (@3 post/slide):
   - **1 big post** — gambar besar, judul, kategori, author, actions
   - **2 small posts** — gambar kecil, judul, kategori, author
4. Carousel auto-slide tiap 5 detik (Alpine.js `setInterval`)
5. Navigasi: prev/next button + dot indicators
6. Headline post di-exclude dari "Berita Terbaru" & "Trending"

### CSS Layout Desktop
- `grid-template-columns: 1.6fr 1fr` — big (1.6x) + smalls (1x)
- Big image: `position: absolute; inset: 0` dengan `height: 100%; object-fit: cover`
- Small images: `height: 100%; object-fit: cover`

### Mobile (<768px)
- Single column (big post di atas, small posts di bawah 2 kolom)

---

## Middleware

| Alias | Middleware | Fungsi |
|-------|-----------|--------|
| `auth` | Laravel default | Wajib login |
| `guest` | Laravel default | Hanya tamu |
| `admin` | `AdminMiddleware` | Role super_admin / editor / reporter |
| `role` | `RoleMiddleware` | Role spesifik (e.g. `role:super_admin`) |

---

## Event / Jobs

| Job | Deskripsi |
|-----|-----------|
| `RecordViewJob` | Mencatat page view secara async (queue) |

Queue connection default: `database` (table `jobs`).

---

## Helpers

Semua helper ada di `app/Helpers/helpers.php`:

| Fungsi | Deskripsi |
|--------|-----------|
| `setting($key, $default)` | Ambil setting dari DB (cached forever) |
| `clearSettingCache($key)` | Hapus cache setting |
| `formatDate($date)` | Format tanggal Indonesia (d F Y H:i) |
| `readTime($text)` | Estimasi waktu baca (menit) |
| `limitText($text, $limit)` | Potong teks dengan ellipsis |
| `shareFacebook($url)` | URL share Facebook |
| `shareWhatsApp($url, $text)` | URL share WhatsApp |
| `shareTelegram($url, $text)` | URL share Telegram |
| `shareTwitter($url, $text)` | URL share Twitter |

---

## Services

| Service | Path | Fungsi |
|---------|------|--------|
| `HeadlineService` | `app/Services/HeadlineService.php` | Wrapper PostRepository untuk headline |
| `HtmlSanitizer` | `app/Services/HtmlSanitizer.php` | Sanitasi HTML (whitelist tag & attribute) |

## Repository

| Repository | Path | Fungsi |
|------------|------|--------|
| `PostRepository` | `app/Repositories/PostRepository.php` | Query headline, trending, category structure |

---

## License

Proprietary — Konut.Update
