# Audit & Perbaikan SEO — Konut Update

Dokumen ini merangkum hasil audit SEO pada portal berita **Konut Update**
(`https://konutupdate.com`) beserta perbaikan yang sudah diimplementasikan di repo ini.

> **Tidak ada klaim "pasti lolos" Google News / Google Search.**
> Yang diukur di sini hanya kondisi teknis situs. Penilaian akhir tetap milik
> Google (dan redaksi), dan butuh akses Google Search Console yang tidak
> dimiliki tim developers.

- Audit & implementasi: repo `konutupdate` (Laravel 12, Blade + Alpine + Tailwind 4)
- Deploy: Render (`konut-update` web + `konut-worker` + Postgres `konut-db`)
- Status dokumen: **implementasi selesai di lokal, belum ter-deploy** (lihat [§7](#7-blocker-deploy--langkah-deploy))

---

## 1. Ringkasan eksekutif

| Area | Sebelum | Sesudah |
|---|---|---|
| Sumber metadata | 14 view masing-masing menulis `@section('meta')` sendiri; blok `@else` di layout tidak pernah render | Satu partial `frontend/partials/seo-meta.blade.php` sebagai sumber tunggal |
| Canonical | Beberapa halaman punya 2 tag canonical, sebagian relatif | 1 tag canonical per halaman, selalu absolut |
| `og:image` | berasal dari `@section('share_image')` yang tidak pernah dibaca layout | `@section('seo_image')` + fallback logo/`og-default.jpg` |
| JSON-LD | `@id` relatif (`#organization`) → tidak valid | `@id` absolut (`https://konutupdate.com/#organization`) |
| `dateModified` | `updated_at` (lompat jauh hanya karena admin toggle flag) | kolom baru `posts.content_updated_at` |
| Byline | "Mimin1" (nama akun), tanpa tautan, tanpa schema Person | Field CMS `author_name`/`author_id` + profil publik `/penulis/{slug}` + `Person` JSON-LD |
| Duplikat | tidak pernah ditandai | `posts.duplicate_of_id` → otomatis `noindex, follow` + keluar dari sitemap + deteksi kandidat judul mirip (≥ 85%) |
| Sitemap | closure lama: kategori via `category_id` legacy, tanpa arsip/kecamatan/tag | Ditulis ulang: artikel, arsip, kategori (pivot), kecamatan, tag ≥ 2 artikel, halaman statis friendly + `/p/*` |
| News sitemap | tidak ada | `/sitemap-news.xml` (+ alias `/news-sitemap.xml`), artikel ≤ 2 hari |
| `robots.txt` | tidak mendaftarkan sitemap | Mendaftarkan 2 sitemap |
| Halaman tipis | tag/kategori/kecamatan kosong ikut ter-index | `noindex, follow` |
| Query per halaman (homepage) | **170** | **47** |

---

## 2. Metode audit

1. **Audit source code** — `routes/web.php`, controller, model, view Blade, middleware.
2. **Audit HTTP lokal** — render halaman di dev server + uji respons (`curl`, `php artisan test`).
3. **Audit HTTP produksi** — `curl`/`webfetch` ke `https://konutupdate.com` (data produksi dibaca, tidak diubah).
4. **Audit performa** — penghitungan query via `DB::getQueryLog()` pada 5 halaman utama.
5. **Validasi otomatis** — 150 feature test baru/lama (`php artisan test`), offline.

**Batasan yang harus diingat:**
- Tidak ada akses Google Search Console → status indexasi, canonical pilihan Google, Core Web Vitals field, dan Tab Berita **tidak** bisa diverifikasi.
- Tidak ada salinan database produksi → angka konten produksi (jumlah kategori/tag/post) hanya disimpulkan dari HTML & sitemap yang publik.
- Perbandingan dengan kompetitor tidak dilakukan (di luar scope).

---

## 3. Temuan & status

### 3.1 Metadata & Open Graph

| # | Temuan | Dampak | Status |
|---|---|---|---|
| M1 | 14 view mendefinisikan `@section('meta')` sendiri; `@hasSection('meta')` di layout mengambil cabang view, sehingga blok default layout (description/OG fallback) **tidak pernah** render di halaman-halaman itu | `description`, `og:*`, `twitter:*` hilang/tidak konsisten per halaman | ✅ Diperbaiki — partial tunggal `seo-meta.blade.php`, semua view memakai `@section('seo_*')` |
| M2 | `og:image` diambil dari `@section('share_image')`, sedangkan layout tidak pernah membaca section itu | Tidak ada `og:image` di sebagian besar halaman (share ke WhatsApp/Facebook tanpa thumbnail) | ✅ Diperbaiki — `@section('seo_image')`, fallback ke setting `logo` lalu `/og-default.jpg` |
| M3 | Beberapa view tetap menulis `<link rel="canonical">` sendiri *dan* layout juga menulis canonical | 2 tag canonical per halaman; Google mengabaikan keduanya | ✅ Diperbaiki — canonical hanya di layout; view override via `@section('canonical')` |
| M4 | `og:url` & canonical dibangun dari `url()->current()` tanpa normalisasi trailing slash/parameter | Duplikat URL (`.`/`/`/parameter tracking) tersebar | ✅ Diperbaiki — canonical absolut dari layout, section `canonical` untuk override |
| M5 | `meta keywords` masih ada di tabel settings | Tidak ada dampak (mesin pencari modern mengabaikannya), tapi menambah ruang kesalahan | ✅ Dibiarkan di setting, **tidak** emitted ke HTML |
| M6 | Halaman pencarian & halaman akun ikut ter-index | Halaman tanpa nilai SEO merebut ruang crawl dari arsip | ✅ `noindex, nofollow` pada `/cari` & `/profil` |

### 3.2 Structured data

| # | Temuan | Dampak | Status |
|---|---|---|---|
| S1 | `@id` JSON-LD relatif (`#organization`, `#website`) | `NewsArticle`/publisher tidak tertaut ke node yang valid | ✅ Diperbaiki — URL absolut |
| S2 | `dateModified` memakai `updated_at` | Tanggal "diperbarui" melompat jauh setelah publikasi hanya karena admin men-toggle flag headline/featured | ✅ Diperbaiki — `posts.content_updated_at` (isi berubah ⇒ tanggal berubah) |
| S3 | Tidak ada byline yang dapat diverifikasi | Google News Journalists / author page tidak ada; E-E-A-T lemah | ✅ Ditambahkan — `author`/`publisher` tertaut, `Person` JSON-LD di halaman profil |
| S4 | Schema artikel tidak mendeklarasikan `articleBody`/`keywords` | Penilaian rich result Google News turun | ✅ `keywords` ditambahkan; `articleBody` sengaja tidak (isi penuh memang ada di halaman) |
| S5 | Video (`type=video`) memakai schema yang sama dengan artikel | Salah klasifikasi di hasil kaya | ✅ `VideoObject` terpisah (sudah ada sebelumnya, diverifikasi) |

### 3.3 Sitemap & robots

| # | Temuan | Dampak | Status |
|---|---|---|---|
| X1 | Sitemap lama closure, kategori diambil dari `category_id` legacy (kategori bisa kosong semua) | Kategori tidak pernah muncul di sitemap | ✅ Ditulis ulang — pivot `post_categories` (`Category::allPosts()`) |
| X2 | Tidak ada arsip (`/terkini`, `/trending`, `/semua-berita`, `/opini`, `/video`) di sitemap | Halaman indeks penting tidak discovering | ✅ Ditambahkan |
| X3 | Tidak ada news sitemap | Tidak ada jalur penemuan untuk Google News | ✅ `/sitemap-news.xml` + alias `/news-sitemap.xml` |
| X4 | `robots.txt` tidak mendaftarkan sitemap | Penemuan sitemap bergantung tebakan | ✅ Dua baris `Sitemap:` |
| X5 | Sitemap memuat artikel yang ditandai duplikat | Indexing URL kembar | ✅ Artikel ber-`duplicate_of_id` tidak masuk sitemap |
| X6 | Sitemap rebuilt tiap request (tanpa cache) | Beban DB tidak perlu | ✅ Cache 1 jam (`sitemap_xml`) / 15 menit (`sitemap_news_xml`), invalidated di semua titik tulis admin |

### 3.4 Duplikat & konten tipis

| # | Temuan | Dampak | Status |
|---|---|---|---|
| D1 | Tidak ada mekanisme menandai artikel kembar | Banyak URL untuk berita yang sama خبر | ✅ `duplicate_of_id` + `noindex, follow` otomatis + badge "Duplikat" di daftar admin |
| D2 | Editor tidak diberi peringatan judul mirip | Duplikat baru muncul setelah terbit | ✅ Deteksi Similaritas (PHP `similar_text`, ambang 85%) menampilkan kandidat di form admin (peringatan, keputusan tetap di editor) |
| D3 | Tag dengan 1 artikel, kategori & kecamatan kosong tetap ter-index | Thin content & crawl budget terbuang | ✅ `noindex, follow` (tetap bisa di-follow, tidak memblokir crawl) |
| D4 | Halaman statis memakai URL `/p/{slug}` | URL tidak ramah, kanal riwayat | ✅ Alias friendly (`/tentang-kami`, `/pedoman-media-siber`, `/privacy-policy`, `/kontak`) + **301** dari URL lama |

### 3.5 Performa & Core Web Vitals (proxy)

| # | Temuan | Angka | Status |
|---|---|---|---|
| P1 | `Schema::hasTable()` dipanggil di `View::composer('*')` → 2 query metadata **per view yang dirender** (homepage merender ~40 view) | ±80 query/halaman | ✅ Memo per-request (`AppServiceProvider::tableExists()`) |
| P2 | Data navigasi (kategori/kecamatan/iklan) di-query ulang 4–6× per halaman (sidebar desktop + mobile, footer, breaking news) | ±13 query identik | ✅ Dihitung 1× per request + cache store (`frontend_categories`, `frontend_kecamatans`, `frontend_ads`) |
| P3 | `Post::isLikedBy()` menembak `EXISTS` per kartu di 6 loop homepage | ±35 query | ✅ Satu query untuk seluruh kartu halaman (`HomeController::attachViewerLikes()`) |
| P4 | `frontend_categories` di-`Cache::forget()` di 3 controller tapi **tidak pernah ditulis** — invalidasi sia-sia | — | ✅ Sekarang benar-benar ditulis + dihapus saat write |
| P5 | `Post::duplicateCandidates()` menarik kolom `body` seluruh tabel published | O(N) penuh | ✅ Ambil `id`+`title` saja |
| P6 | `popularTags` memuat seluruh tabel tag lalu disaring di PHP | Subquery count per tag | ✅ Filter + limit di SQL (`whereHas` + `orderByDesc(posts_count)`) |
| P7 | Gambar listing & hero: apakah sudah memakai `loading`/`fetchpriority` yang tepat? | — | ✅ Dicek & dipertahankan — listing `loading="lazy"`, hero + gambar artikel `loading="eager" fetchpriority="high"` (LCP) |
| P8 | `alt` gambar selalu berisi judul artikel | Alt text generik | ✅ Field CMS `image_alt` (fallback ke judul) dipakai di 17 lokasi gambar |

**Hasil pengukuran (dev, `DB::getQueryLog()`):**

| Halaman | Sebelum | Sesudah | Selisih |
|---|---|---|---|
| `/` | 170 | **47** | −72% |
| `/terkini` | 81 | **7** | −91% |
| `/trending` | 92 | **7** | −92% |
| `/semua-berita` | 40 | **10** | −75% |
| `/kategori/{slug}` | 76 | **7** | −91% |

Angka diukur dengan cache **dingin** (cache store dikosongkan sebelum request) —
jadi ini skenario terburuk, bukan kondisi ideal.

> Angka ini adalah proxy TTFB/Kecepatan server. **Core Web Vitals di field** (LCP/INP/CLS) hanya bisa dinilai lewat PageSpeed Insights / CrUX setelah deploy — belum diukur.

### 3.6 Temuan tambahan saat implementasi

| # | Temuan | Dampak | Status |
|---|---|---|---|
| B1 | Homepage: bila tidak ada headline yang masih berlaku (`headline_expires_at` habis), 4 artikel terbaru dipakai untuk mengisi kolom hero — tetapi blok hero hanya dirender jika ada `heroMain`, sehingga **4 artikel terbaru itu hilang dari homepage** (tidak muncul di Kabar Terkini, Rekomendasi, maupun Berita Kecamatan) | Konten terbaru tidak pernah tayang di halaman utama → sinyal "terbaru" ke Google & pembaca hilang | ✅ Diperbaiki — bila tidak ada headline valid, artikel pembisi hero **tidak** dikuras dari `usedIds` sehingga tetap tampil di Kabar Terkini |
| B2 | `Post::isLikedBy()` memakai relasi `likes` bila sudah di-eager-load | Status "sudahlikes" bisa salah bila relasi di-load tanpa filter IP | ✅ Regression test `LikeStateTest` mengunci perilaku per-IP |

### 3.7 Temuan yang perlu GSC (tidak bisa diverifikasi dari sini)

- Status coverage & jumlah URL ter-index.
- Canonical yang benar-benar dipilih Google (bukan yang kita tulis).
- Core Web Vitals field data (INP, LCP, CLS) per kelompok halaman.
- Backlink & digital PR.
- Manual action / penalty.
- Tab Berita: status verifikasi publisher & performa artikel.

### 3.8 Temuan yang belum pasti / butuh keputusan redaksi

| # | Temuan | Catatan |
|---|---|---|
| R1 | Produksi punya ±210 tag, sebagian besar berisi sangat sedikit artikel | Halaman tag ≥ 2 artikel masih di-index. Rekomendasi: naikkan ambang ke 3–5 artikel atau `noindex` semua tag — putuskan redaksi. |
| R2 | Brand di DB produksi masih "**Konutupdate**" (site_name), sedangkan brand resmi "**KonutUpdate**" | Mempengaruhi `og:site_name`, `schema.org` publisher, dan footer. **Harus diubah manual di panel admin produksi** (seeder tidak menimpa nilai yang sudah ada). |
| R3 | Jumlah kategori produksi (8) berbeda dari seeder repo (10) | `Kecelakaan`, `Olahraga`, `Event`, `Nasional` ada di lokal. Perlu sinkronisasi keputusan redaksi. |
| R4 | `author_id` belum diisi di produksi → byline artikel masih "Redaksi"/nama akun lama | Schema `Person` per penulis baru aktif setelah profil diisi. Perlu diisi secara berkala oleh redaksi. |
| R5 | 103 artikel lokal hampir tanpa gambar (data seed) | Hanya artefak data lokal, bukan masalah kode. |

---

## 4. Yang perlu dikerjakan redaksi (bukan kode)

1. Isi **Alt Text Gambar** saat unggah gambar berita (sudah ada fieldnya di form admin).
2. Pilih **Akun Penulis** per artikel agar byline menaut ke profil `/penulis/{slug}`.
3. Tandai artikel kembar lewat dropdown **Artikel Duplikat** (form admin menampilkan kandidat judul mirip otomatis).
4. Isi **Bio** dan **slug profil** di menu Admin → User.
5. Perbarui nilai setting `site_name` produksi menjadi `KonutUpdate`.
6. Pastikan minimal satu artikel berita (bukan opini) terbit **setiap hari** — konsistensi terbit adalah sinyal utama, bukan faktor teknis.

---

## 5. Validasi otomatis

Test baru (semua offline, SQLite `:memory:`):

| File | Cakupan |
|---|---|
| `tests/Feature/SeoMetaTest.php` | Meta description/robots/OG/Twitter, canonical tunggal, ISO date, JSON-LD absolut, noindex duplikat/pencarian/tag/kategori kosong, 301 halaman statis |
| `tests/Feature/SitemapTest.php` | Artikel published, arsip, pivot kategori, skip duplikat, skip draft, kecamatan & halaman lain |
| `tests/Feature/NewsSitemapTest.php` | Rentang 2 hari, ISO date, `news:keywords`, skip duplikat, alias `/news-sitemap.xml` |
| `tests/Feature/RobotsTest.php` | Pendaftaran sitemap + aturan `Disallow` |
| `tests/Feature/AuthorProfileTest.php` | Profil `/penulis/{slug}`, `Person` JSON-LD, 404 slug asing |
| `tests/Feature/DuplicateContentTest.php` | Auto `noindex`, badge admin, kandidat judul mirip, guard "duplikat diri sendiri" |
| `tests/Feature/LikeStateTest.php` | Status "sudahlikes" di homepage mengikuti IP pengunjung (regresi untuk eager-load `likes`) |

Status terakhir: **150 test passed** (`php artisan test`).

---

## 6. Rincian teknis perubahan

**Metadata**
- `resources/views/frontend/partials/seo-meta.blade.php` — satu-satunya sumber meta/OG/Twitter.
- `resources/views/frontend/layouts/app.blade.php` — canonical tunggal, include partial, `@yield('json_ld')`.
- `app/Helpers/helpers.php` — helper `seoSectionValue()`. **Catatan:** `@yield` di dalam blok `@php` tidak dikompilasi, dan `Blade::startSection($section, $content)` sudah meng-escape isi (`&` → `&amp;`), jadi nilainya di-decode sekali agar tidak dobel escape.

**Canonical & URL**
- `app/Models/Page.php` — `FRIENDLY_ROUTES` + `canonicalUrl()`.
- `app/Http/Controllers/Frontend/PageController.php` — 301 `/p/{slug}` → alias friendly.

**Sitemap & robots**
- `routes/web.php` — `robots`, `sitemap`, `sitemap.news` (+ `sitemap.news.alias`), `/feed`.
- `resources/views/frontend/partials/sitemap.blade.php`, `.../sitemap-news.blade.php`.
- Invalidasi cache: `Admin\PostController`, `Admin\OpiniController`, `Admin\VideoController`, `Admin\PageController`, `Admin\CategoryController`, `Admin\KecamatanController`, `Admin\AdController`.

**Duplikat & byline**
- `database/migrations/2026_10_22_000001_add_seo_fields_to_posts_table.php` — `author_id`, `image_alt`, `content_updated_at`, `duplicate_of_id`.
- `database/migrations/2026_10_22_000002_add_profile_fields_to_users_table.php` — `users.slug`, `users.bio`.
- `app/Models/Post.php` — scope `notDuplicate()`, `duplicateCandidates()` (ambang 85%), accessor `author_name`/`image_alt`.
- `app/Models/User.php` — slug unik, `writtenPosts()`, `publicProfileUrl()`.
- `app/Http/Controllers/Frontend/AuthorController.php` + `resources/views/frontend/authors/show.blade.php`.

**Performa**
- `app/Providers/AppServiceProvider.php` — memo `Schema::hasTable()`, data navigasi dihitung sekali per request + cache store.
- `app/Http/Controllers/Frontend/HomeController.php` — `attachViewerLikes()`, `popularTags` di SQL.

---

## 7. Blocker deploy & langkah deploy

### 7.1 Blocker yang ditemukan

`render.yaml` menjalankan (berurutan, dirantai `&&`):

```
composer install … && npm ci && npm run build && php artisan migrate --force \
  && php artisan db:seed --class=CategorySeeder --force \
  && php artisan db:seed --class=PageSeeder --force \
  && php artisan storage:link --force && php artisan optimize
```

`CategorySeeder` (versi lama) memakai `Category::create()` → **unique violation** pada database yang sudah berisi kategori → `db:seed` gagal → rantai build berhenti → **deploy gagal**. Akibatnya situs produksi masih menjalankan versi lama:

| Endpoint produksi | Status (18 Okt 2026) | Artinya |
|---|---|---|
| `https://konutupdate.com/` | 200 | Versi lama (`bottom-nav`, "Mimin1" byline) |
| `https://konutupdate.com/sitemap.xml` | 200 | **Versi lama** (belum pivot kategori/arsip) |
| `https://konutupdate.com/sitemap-news.xml` | 404 | Belum ada |
| `https://konutupdate.com/kirim-opini` | 404 | Belum ada (versi `c61b5f7` sudah punya fitur ini) |

**Perbaikan:** `CategorySeeder` & `SettingSeeder` sekarang idempotent (`firstOrCreate`), `PageSeeder` sudah `updateOrCreate`, sehingga build tidak lagi gagal karena data sudah ada.

> `php artisan route:cache` tetap aman: Laravel 12 bisa men-cache route closure.

### 7.2 Urutan deploy

1. `git push` ke branch deploy Render (repo ini belum di-commit — **perlu commit & push lebih dulu**).
2. Render menjalankan `buildCommand`; pastikan log build menunjukkan `migrate --force` sukses **tanpa** error seed.
3. Setelah deploy aktif, verifikasi dengan `curl`:

```bash
for u in / /robots.txt /sitemap.xml /sitemap-news.xml /news-sitemap.xml; do
  curl -s -o /dev/null -w "%{http_code}  $u\n" "https://konutupdate.com$u"
done
```

Target: semua **200**, dan `/sitemap-news.xml` berisi `<news:url>`.

4. Update setting produksi `site_name` → `KonutUpdate` di panel admin, lalu `php artisan cache:clear`-equivalent (panel ini sudah memanggil `clearSettingCache()`).

### 7.3 Yang tidak boleh lupa setelah deploy

- **Worker wajib hidup** (`konut-worker`): tanpa itu `RecordViewJob` (views_count) dan `posts:expire-flags` tidak pernah jalan → `trending` kaku dan flag tidak pernah kedaluwarsa.
- `storage:link --force` sudah ada di build command ✓.
- Jangan mengubah `TRUST_PROXIES` atau `GEMINI_*` di `.env.production`.
- **Captcha Cloudflare (Turnstile) aktif di semua environment.** Yang wajib diisi manual di
  **dashboard Render**: `TURNSTILE_SECRET_KEY` (secret asli widget `0x4AAAAAAEQnYy9UzRg7lK1T`).
  Tanpa secret itu, `TurnstileService::verify()` bernilai `false` (fail-closed) ⇒ **login &
  pendaftaran gagal** dan log produksi berisi `Turnstile tidak dapat diverifikasi`.
  `render.yaml` mendaftarkan `TURNSTILE_SECRET_KEY` dengan `sync: false` supaya tidak tertimpa
  blueprint. Verifikasi setelah deploy: coba daftar akun baru di situs, captcha harus muncul dan
  submitting-nya diterima.

---

## 8. Rekomendasi lanjutan (prioritas)

| Prioritas | Item | Estimasi |
|---|---|---|
| Tinggi | Naikkan ambang tag (≥ 3–5 artikel) atau `noindex` semua tag (item R1) | 1 jam |
| Tinggi | Isi byline & profil penulis di produksi (item R4) | 1–2 jam |
| Tinggi | Isi `site_name` produksi (item R2) | 5 menit |
| Sedang | Direktorium halaman tag: 210 tag → konsolidasi/archivasi | 2–4 jam |
| Sedang | `searchForChat()` (Tanya Berita) memindai `LOWER(body) LIKE` + menarik `body` penuh untuk 60 kandidat — perlu index full-text/trigram atau ambil body hanya untuk 8 finalist | 2 jam |
| Sedang | 6 method di `app/Repositories/PostRepository.php` tidak terpakai (dead code) — pindahkan query listing ke repository agar ikut ter-cover `SmokeSeedTest` | 3 jam |
| Sedang | Sinkronkan `posts.category_id` (legacy) dengan pivot `post_categories` di form admin, lalu hapus cabang `@elseif($post->category)` di view (mencegah N+1 laten) | 2 jam |
| Rendah | Tambahkan `width`/`height` eksplisit pada gambar kartu (anti-CLS) | 1 jam |
| Rendah | `Post::commentsCount()` fallback memakai scope `approved()`, sedangkan `withCount('comments')` menghitung semua komentar — selaraskan | 30 menit |

---

## 9. Daftar file yang berubah (ringkas)

```
app/Helpers/helpers.php
app/Http/Controllers/Admin/{Ad,Category,Kecamatan,Opini,Page,Post,User,Video}Controller.php
app/Http/Controllers/Frontend/{AuthorController,HomeController,PageController}.php
app/Http/Requests/{Store,Update}PostRequest.php, {Store,Update}UserRequest.php
app/Models/{Page,Post,User}.php
app/Providers/AppServiceProvider.php
database/migrations/2026_10_22_000001_add_seo_fields_to_posts_table.php
database/migrations/2026_10_22_000002_add_profile_fields_to_users_table.php
database/seeders/{Category,Page,Setting}Seeder.php
resources/views/frontend/** (layout, partials, semua halaman indeks)
resources/views/admin/{posts,users}/**
routes/web.php
tests/Feature/{AuthorProfileTest,DuplicateContentTest,NewsSitemapTest,PublicProfileTest,RobotsTest,SeoMetaTest,SitemapTest}.php
```

---

*Dokumen ini dibuat sebagai bagian dari perbaikan SEO. Semua angka berasal dari pengukuran lokal/dev kecuali yang ditandai "produksi"; hasil akhir indeksasi搜索引擎 tetap bergantung pada mesin pencari.*
