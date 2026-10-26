# Kontrak API Aplikasi Mobile — KonutUpdate v1

Status: **aktif di produksi**
Base URL: `https://konutupdate.com/api/v1`
Sumber kebenaran: `routes/api.php`, `app/Http/Controllers/Api/V1/*`, `app/Http/Resources/*`, `app/Models/Post.php`
Dokumen rancangan produk: `docs/mobile-app-blueprint.md`

Dokumen ini menjelaskan **apa yang benar-benar dikirim server**, termasuk aturan bisnis yang harus ditiru aplikasi. Jika ada perbedaan antara dokumen ini dan aplikasi, dokumen ini yang benar — dan kalau kode berubah, dokumen ini wajib diperbarui.

---

## 1. Ringkasan endpoint

| Method | Path | Fungsi | Paginasi |
|---|---|---|---|
| GET | `/home` | semua section beranda sekaligus | tidak (lihat §3.1) |
| GET | `/latest` | tab **Terbaru** (tanpa opini) | ya |
| GET | `/trending` | tab **Trending** (urut `views_count`) | ya |
| GET | `/opini` | tab **Opini** | ya |
| GET | `/featured` | tab **Pilihan** (`is_featured` aktif) | ya |
| GET | `/posts` | daftar berita + filter | ya |
| GET | `/posts/{slug}` | detail artikel/video/opini | tidak |
| GET | `/posts/{slug}/comments` | komentar yang sudah disetujui | ya |
| GET | `/categories` | daftar kategori berisi konten | tidak |
| GET | `/categories/{slug}` | detail kategori + daftar berita | ya |
| GET | `/videos` | daftar video | ya |
| GET | `/search?q=` | pencarian judul/ringkasan/isi | ya |

Tidak ada endpoint tulis, tidak ada auth, tidak ada API key. Semua **GET publik**.

---

## 2. Aturan umum

### 2.1 Header

```http
Accept: application/json
```

Wajib. Tanpa header ini, error (404/500) dibalas **HTML**, bukan JSON.
Tidak ada header Authorization untuk saat ini.

### 2.2 Bentuk response

Semua endpoint daftar memakai amplop yang sama:

```json
{
  "success": true,
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 180,
    "per_page": 15,
    "total": 2693,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "https://konutupdate.com/api/v1/latest?page=1",
    "last": "https://konutupdate.com/api/v1/latest?page=180",
    "prev": null,
    "next": "https://konutupdate.com/api/v1/latest?page=2"
  }
}
```

Catatan:
- `data` berupa **array** untuk semua endpoint daftar.
- `/home` memakai `data` berupa **objek** (bukan array). Lihat §3.1.
- `/search` dengan `q` kosong **tidak** mengirim `links` (hanya `success`, `data: []`, `meta`).
- `links.prev` bernilai `null` di halaman pertama, `links.next` `null` di halaman terakhir.
- Jangan menyusun URL paginasi sendiri dari `links.last`; cukup kirim `?page=n`.

### 2.3 Paginasi

Parameter: `page` (mulai 1) dan `per_page`.

- `per_page` default **15**, maksimum **50**, minimum 1. Nilai di luar itu dipotong otomatis (tidak error).
- Hentikan infinite scroll saat `meta.current_page >= meta.last_page`.
- Pull-to-refresh: reset `page = 1`, kosongkan list, lalu muat ulang.

### 2.4 Format tanggal

Semua tanggal ISO-8601 dengan offset waktu Indonesia Tengah, contoh:

```
2026-10-26T22:20:00+08:00
```

Field: `published_at`, `content_updated_at`, `expires_at` (story), `created_at` (komentar).
`content_updated_at` bisa `null` pada konten lama.

### 2.5 URL absolut

`thumbnail`, `avatar`, `video_url`, `video_poster`, `url`, `profile_url` **sudah URL lengkap**. Jangan menambahkan base URL lagi.

Semua thumbnail berformat WebP hasil crop `1200x675`; avatar `400x400`. Nilainya bisa `null` — sediakan placeholder.

### 2.6 Error

| Status | Arti | Yang harus dilakukan aplikasi |
|---|---|---|
| 404 | slug/kategori tidak ada, atau konten belum `published` | tampilkan "tidak ditemukan", jangan retry |
| 500 | error server | tampilkan error + tombol retry, laporkan |
| 429 | throttled (jika limiter diaktifkan nanti) | backoff, jangan spam |

Bentuk body 404:

```json
{ "message": "No query results for model [App\\Models\\Post]." }
```

Aplikasi cukup membaca `message`; jangan mengandalkan field lain.

> ⚠️ **Temuan produksi (27 Okt 2026):** server masih `APP_DEBUG=true`, sehingga body 404/500 ikut mengirim `exception`, `file`, `line`, dan `trace` (path server terbuka). Set `APP_DEBUG=false` di `.env` server lalu `php artisan config:clear` (atau `config:cache`). Setelah itu body error hanya berisi `message`.

### 2.7 CORS & rate limit

- Aplikasi Android/iOS native **tidak terkena CORS**.
- Flutter Web (`flutter run -d chrome`) akan kena CORS karena API tidak dikonfigurasi untuk browser. Uji di emulator/perangkat.
- Saat ini route API hanya memakai grup middleware `api`, **tanpa throttle khusus**. Tetap gunakan pola sopan: satu request per tab per halaman, cache lokal 3–5 menit, jangan polling lebih cepat dari 30 detik.

---

## 3. Detail endpoint

### 3.1 `GET /home`

Untuk render awal beranda. Satu request mengembalikan semua section, **sudah saling eksklusif** (tidak ada post yang muncul dua kali di dalam `/home`).

```json
{
  "success": true,
  "data": {
    "stories": [
      {
        "id": 12,
        "title": "Pemerintahan",
        "image": "https://konutupdate.com/storage/stories/xxx.webp",
        "url": "https://contoh.com/berita",
        "category": { "id": 5, "name": "Pemerintahan", "slug": "pemerintahan" },
        "expires_at": "2026-11-02T10:00:00+08:00"
      }
    ],
    "headline": { "main": { ...PostSummary }, "side": [ { ...PostSummary } ] },
    "latest": { "data": [ { ...PostSummary } ], "meta": { ... } },
    "trending": [ { ...PostSummary } ],
    "featured": [ { ...PostSummary } ],
    "videos": [ { ...VideoItem } ],
    "opini": [ { ...PostSummary } ],
    "categories": [ { ...Category } ],
    "tags": [ { "id": 3, "name": "Pilkada", "slug": "pilkada", "posts_count": 12 } ],
    "kecamatan": [ { "id": 2, "name": "Wanggudu", "slug": "wanggudu", "posts_count": 34 } ]
  }
}
```

Aturan penting:

- `stories`: maksimal tidak dibatasi jumlahnya di kode (produksi bisa >10), hanya story `is_active` dan belum `expires_at`. Urut `sort_order` lalu `created_at` terbaru.
- `headline.main`: **`null`** jika tidak ada headline aktif. Jika `null`, blok hero **jangan dirender**.
- `headline.side`: maksimal 4 post. Jika headline kosong, array ini juga kosong (server sengaja tidak "menguras" artikel terbaru).
- `latest.data`: maksimal 18 post terbaru **non-opini**, sudah menghindari post yang dipakai headline/video/featured/trending.
- `trending`: maksimal 6, urut `views_count` desc, menghindari post yang sudah dipakai section lain.
- `featured`: maksimal 8, hanya **Pilihan aktif**.
- `videos`: maksimal 6.
- `opini`: maksimal 8.
- `latest.meta` di `/home` adalah **meta statis** (`current_page=1`, `last_page=1`, `per_page=total`). **Jangan** dipakai untuk infinite scroll — untuk itu panggil `/latest`.
- Section yang mungkin kosong: `featured`, `opini`, `headline.main` — semua harus punya empty state.

### 3.2 `GET /latest` — tab Terbaru

- `status = published`, punya isi (`withContent`), **`type != opini`**.
- `type = video` **ikut tampil** di sini.
- Urut `published_at` desc.
- Contoh: `/latest?page=1&per_page=15`.

### 3.3 `GET /trending` — tab Trending

- `status = published`, punya isi, **semua tipe** (article, video, opini).
- Urut `views_count` desc, lalu `published_at` desc.
- `views_count` berasal dari kunjungan **website**, bukan aplikasi (lihat §4.8).

### 3.4 `GET /opini` — tab Opini

- Hanya `type = opini`, `status = published`, punya isi.
- Urut `published_at` desc.
- Opini masuk status `pending` saat dikirim dan **tidak muncul** sampai disetujui admin.
- Bisa kosong (produksi per 27 Okt 2026: total 0).

### 3.5 `GET /featured` — tab Pilihan

- `is_featured = true` **dan** `published_at >= now - 14 hari` (sama dengan scope `featured()` di web).
- Urut `published_at` desc.
- Bisa kosong. Konten yang lewat 14 hari otomatis hilang dari tab ini (flag di database masih menyala sampai dibersihkan scheduler).

### 3.6 `GET /posts` — daftar + filter

Parameter opsional (boleh digabung):

| Param | Contoh | Arti |
|---|---|---|
| `category` | `pemerintahan` | slug kategori, lewat tabel pivot `post_categories` |
| `tag` | `pilkada` | slug tag |
| `kecamatan` | `wanggudu` | slug kecamatan |
| `type` | `article` / `video` / `opini` | tipe konten |
| `page`, `per_page` | `2`, `20` | paginasi |

- Urut `published_at` desc.
- Tanpa filter `type`, opini dan video ikut tampil.
- Filter yang tidak cocok menghasilkan `data: []` dengan `meta.total = 0` (bukan 404).

### 3.7 `GET /posts/{slug}` — detail

Mengembalikan **PostDetail**: semua field `PostSummary` + tambahan:

| Field | Tipe | Catatan |
|---|---|---|
| `body` | string HTML | sudah disanitasi server; render dengan HTML renderer yang aman |
| `content_updated_at` | string/null | `dateModified` yang jujur; hanya berubah bila isi/judul/ringkasan benar-benar diubah |
| `tags` | array `{id,name,slug}` | bisa kosong |
| `related` | array `PostSummary` | selalu array, maksimal 6 |
| `video_path` | string/null | nilai mentah (URL atau path storage) |
| `video_url` | string/null | URL siap pakai: YouTube/Vimeo/TikTok atau file upload |
| `video_embed_url` | string/null | URL embed; untuk file upload nilainya sama dengan `video_url` |
| `video_poster` | string/null | thumbnail, atau poster YouTube, atau `null` |

- Slug hanya `[A-Za-z0-9-]+`.
- `related` = artikel published lain yang berbagi **kategori** (pivot) **atau** `kecamatan_id` yang sama, maksimal 6, urut terbaru.
- Post `draft/pending/rejected` → **404**.
- Post published tapi `body` kosong dan bukan video → **404** (disembunyikan dari publik).

### 3.8 `GET /posts/{slug}/comments`

```json
{
  "success": true,
  "data": [
    {
      "id": 9,
      "name": "Budi",
      "content": "Terima kasih infonya.",
      "created_at": "2026-10-26T21:00:00+08:00",
      "created_at_human": "1 jam yang lalu"
    }
  ],
  "meta": { "current_page": 1, "last_page": 1, "per_page": 20, "total": 1, "from": 1, "to": 1 },
  "links": { "first": "...", "last": "...", "prev": null, "next": null }
}
```

- Hanya komentar `is_approved = true`.
- Urut **terbaru dulu** (`created_at` desc).
- `per_page` default 20, maks 50.
- Tidak ada avatar/balasan/like komentar.
- Post draft/pending → 404.

### 3.9 `GET /categories`

- Hanya kategori yang punya minimal 1 post **published dan berisi**.
- Urut nama A–Z.
- `posts_count` = jumlah post published berisi di kategori itu (dihitung lewat pivot).
- Response: `{success, data: [Category]}`, **tanpa** `meta`/`links`.

`GET /categories/{slug}`:

```json
{
  "success": true,
  "data": {
    "category": { "id": 5, "name": "Pemerintahan", "slug": "pemerintahan", "description": "...", "posts_count": 42 },
    "posts": [ { ...PostSummary } ]
  },
  "meta": { ... },
  "links": { ... }
}
```

- Kategori tidak ada → 404.
- Kategori ada tapi belum punya post published → 200 dengan `posts: []` dan `posts_count: 0`.
- Post diambil lewat pivot `post_categories`, bukan kolom legacy `category_id`.

### 3.10 `GET /videos`

- `type = video`, published, punya isi, urut `published_at` desc.
- Bentuk item = **VideoItem** (bukan PostSummary):

```json
{
  "id": 400,
  "title": "Video: ...",
  "slug": "...",
  "excerpt": "...",
  "thumbnail": "https://konutupdate.com/storage/thumbnails/xxx.webp",
  "video_url": "https://www.youtube.com/watch?v=xxxx",
  "video_embed_url": "https://www.youtube.com/embed/xxxx",
  "published_at": "2026-10-26T20:00:00+08:00",
  "url": "https://konutupdate.com/video-...",
  "views_count": 10,
  "likes_count": 0,
  "comments_count": 0
}
```

- `thumbnail` bisa `null` (video upload tanpa thumbnail dan bukan YouTube).
- Untuk detail lengkap video (body, related, tags), tetap panggil `/posts/{slug}`.

### 3.11 `GET /search?q=`

- `q` di-`trim`. Kosong/absen → `data: []`, `meta.total = 0`, tanpa `links`.
- Mencari di `title`, `excerpt`, `body` (bukan tag/kategori/penulis).
- Urut `published_at` desc, hanya published + berisi.
- **Penting (produksi PostgreSQL):** query memakai `LIKE` biasa, yang **case-sensitive** di PostgreSQL. `q=konut` tidak menemukan "Konut". Kirim apa adanya dari input user, tapi siapkan saran "coba huruf kecil/sama persis". (Di SQLite lokal perilakunya case-insensitive — jangan mengandalkan itu.)
- Belum ada endpoint "trending pencarian" maupun saran autocomplete.

---

## 4. Aturan bisnis yang wajib ditiru aplikasi

### 4.1 Hanya konten publik yang tampil

Semua endpoint memakai `status = published` **dan** `withContent`:

- artikel/opini wajib punya `body` tidak kosong;
- video tetap dianggap berisi jika punya `video_path` walau body kosong.

Konten `draft`, `pending`, `rejected` tidak pernah muncul. Jangan menampilkan preview dari sumber lain.

### 4.2 Tipe konten (`type`)

| Nilai | Tampil di | Catatan |
|---|---|---|
| `article` | Terbaru, Trending, Pilihan, kategori, pencarian | berita biasa |
| `video` | Terbaru, Trending, Pilihan, `/videos`, kategori | punya pemutar video |
| `opini` | **hanya** tab Opini + Trending + Pilihan (jika memenuhi syarat) | tidak masuk tab Terbaru |

Gunakan `type` untuk badge dan pemilihan layout, jangan menebak dari judul.

### 4.3 Headline

- `is_headline = true` dan `headline_expires_at` belum lewat (masa berlaku **7 hari**).
- Maksimal **6** headline aktif; menyimpan headline baru otomatis mencabut yang paling lama.
- Hanya dipakai untuk menyusun blok hero di `/home` (`headline.main` + `headline.side`).
- **Flag `is_headline` di item feed sudah dihitung efektif**: bernilai `false` bila sudah kedaluwarsa. Tetap jangan memakai flag ini untuk menyusun hero sendiri — pakai `data.headline`.

### 4.4 Pilihan (`is_featured`)

- Aktif bila `is_featured = true` **dan** `published_at` dalam **14 hari** terakhir.
- `/featured` hanya mengembalikan yang aktif.
- Field `is_featured` di item feed juga sudah efektif (false bila kedaluwarsa), walau flag di database masih menyala sampai scheduler membersihkannya.
- Menyimpan ulang artikel lama di admin akan mengaktifkan kembali masa 14 hari.

### 4.5 Breaking news

- Flag `is_breaking` aktif **3 hari** sejak diset (`breaking_expires_at`).
- **Tidak ada endpoint `/breaking`.** Breaking hanya bisa dideteksi dari field `is_breaking` pada item feed (sudah efektif).
- Untuk banner breaking, ambil dari `/latest`/`/home` dan filter `is_breaking == true`; jangan mengarang section baru.

### 4.6 Opini

- Opini dikirim user → `pending` → harus disetujui admin → `published`.
- Hanya opini `published` yang tampil; `rejection_reason` **tidak** diekspos API.
- Kirim opini dari aplikasi **belum tersedia** (butuh auth + endpoint tulis).

### 4.7 Kategori = pivot, maksimal 3

- Satu post bisa punya **maksimal 3 kategori** (dipotong server saat simpan).
- Kategori ada di array `categories` (bisa 0–3 item). Untuk badge, pakai item pertama.
- Jangan memakai `category_id` legacy; API sudah memakai pivot.

### 4.8 `views_count` tidak bertambah dari aplikasi

- Penghitung dibaca (`views_count`) hanya bertambah lewat **website**, dengan dedupe per IP 10 menit (job antrean).
- Request `GET /api/v1/...` **tidak** menambah `views_count`.
- Konsekuensi: pembaca aplikasi tidak memengaruhi peringkat Trending. Jangan menaikkan angka secara lokal seolah-olah tercatat. Kalau nanti ingin dihitung, perlu endpoint POST khusus + aturan anti-spam.

### 4.9 Like & komentar

- `likes_count` dan `comments_count` adalah **total** (bukan status user).
- Belum ada `is_liked` / `is_bookmarked` di response.
- Komentar hanya yang approved, urut terbaru.
- Like/komentar/bookmark dari aplikasi belum tersedia (butuh auth).

### 4.10 Duplikat konten

- Artikel dengan `duplicate_of_id` tetap bisa muncul di feed API (tidak difilter), tapi di web diberi `noindex` dan dikeluarkan dari sitemap.
- Field `duplicate_of_id` **tidak diekspos**; aplikasi tidak bisa membedakannya.
- Jangan menampilkan dua artikel berjudul sama sebagai dua berita berbeda bila terlihat jelas duplikat; cukup tampilkan keduanya apa adanya (tidak ada data pembeda).

### 4.11 Story

- Story bukan tipe post; punya tabel sendiri.
- `title` di database berasal dari kategori yang dipilih admin; **judul tampil = nama kategori terkini** (bisa berubah bila kategori di-rename).
- Aktif 7 hari sejak dibuat; admin bisa memperpanjang.
- `image` bisa `null`; `url` bisa `null`.
- Tidak ada batas jumlah story; jangan asumsikan maksimal 10.

### 4.12 Urutan data

Jangan mengurutkan ulang di aplikasi:

| Data | Urutan dari server |
|---|---|
| Terbaru, Opini, Pilihan, kategori, video | `published_at` desc |
| Trending | `views_count` desc, lalu `published_at` desc |
| Komentar | `created_at` desc |
| Kategori | nama A–Z |
| Kecamatan | `sort_order`, lalu nama |
| Tag populer | jumlah post desc |
| Story | `sort_order`, lalu `created_at` desc |

### 4.13 Zona waktu

Semua tanggal memakai offset `+08:00` (WITA). Tampilkan langsung atau konversi ke waktu lokal perangkat; jangan menambahkan offset manual.

---

## 5. Kamus field

### PostSummary (dipakai semua feed)

| Field | Tipe | Catatan |
|---|---|---|
| `id` | int | |
| `title` | string | |
| `slug` | string | untuk membuka detail |
| `excerpt` | string/null | ringkasan |
| `thumbnail` | string/null | URL absolut WebP 1200×675 |
| `image_alt` | string/null | alt text; fallback ke judul bila kosong |
| `type` | string | `article`/`video`/`opini` |
| `published_at` | string ISO-8601 | |
| `url` | string | URL web artikel (bukan deep link app) |
| `author` | object/null | `{id,name,slug,avatar,profile_url}` |
| `categories` | array | 0–3 item `{id,name,slug,description}` |
| `kecamatan` | object/null | `{id,name,slug}` |
| `views_count` | int | |
| `likes_count` | int | total |
| `comments_count` | int | total |
| `is_breaking` | bool | efektif (3 hari) |
| `is_headline` | bool | efektif (7 hari) |
| `is_featured` | bool | efektif (14 hari) |

### PostDetail

`PostSummary` + `body`, `content_updated_at`, `tags[]`, `related[]`, `video_path`, `video_url`, `video_embed_url`, `video_poster`.

### Author

`{id, name, slug, avatar, profile_url}` — `avatar` dan `profile_url` bisa `null`. Profil penulis di web: `/penulis/{slug}`.

### Comment

`{id, name, content, created_at, created_at_human}` — tanpa email/avatar.

### Category

`{id, name, slug, description, posts_count}` — `description` bisa `null`; `posts_count` hanya dikirim di `/categories` dan `/categories/{slug}`, tidak ada di `categories[]` milik post.

### Tag / Kecamatan

`{id, name, slug, posts_count}`.

### Story

`{id, title, image, url, category, expires_at}` — `image`, `url`, `category`, `expires_at` bisa `null`.

### VideoItem

`{id, title, slug, excerpt, thumbnail, video_url, video_embed_url, published_at, url, views_count, likes_count, comments_count}`.

---

## 6. Yang belum tersedia (jangan dipanggil)

| Fitur | Status |
|---|---|
| Login/register/logout, token | belum ada |
| Profil user, avatar | belum ada |
| Like / bookmark / toggle | belum ada (`is_liked`, `is_bookmarked` juga tidak dikirim) |
| Kirim komentar | belum ada |
| Kirim opini + upload gambar | belum ada |
| Riwayat opini | belum ada |
| Notifikasi / FCM / device token | belum ada |
| Endpoint `/breaking`, `/tags`, `/kecamatan` | belum ada |
| Saran pencarian / trending pencarian | belum ada |

Semua tombol interaktif tersebut di aplikasi harus dinonaktifkan atau diberi label "segera hadir" agar tidak menembak endpoint 404.

---

## 7. Logika yang disarankan di aplikasi

### 7.1 Tab beranda

| Tab | Request | Penanganan |
|---|---|---|
| Terbaru | `/latest` | infinite scroll; bisa berisi video |
| Trending | `/trending` | infinite scroll |
| Opini | `/opini` | empty state khusus ("Belum ada opini published") |
| Pilihan | `/featured` | empty state ("Belum ada konten pilihan") |

- Simpan state per tab (list + page + scroll offset); jangan reload saat berpindah tab.
- Saat pertama membuka beranda, panggil `/home` untuk hero + story + section, lalu tab memakai endpoint masing-masing.
- Refresh: pull-to-refresh per tab; jangan langsung memanggil 4 endpoint sekaligus.

### 7.2 Detail artikel

1. Buka dengan `slug` dari item feed.
2. `GET /posts/{slug}`; render `body` sebagai HTML (sanitasi tetap dilakukan server, tapi gunakan renderer yang tidak menjalankan script).
3. Tampilkan `related`; jika kosong sembunyikan section.
4. Jika `type = video`, utamakan `video_embed_url` (WebView/player), fallback `video_url`.
5. 404 → halaman "Berita tidak ditemukan" tanpa retry.

### 7.3 Gambar

- Gunakan URL apa adanya.
- `thumbnail == null` → placeholder abu-abu dengan rasio 16:9.
- `avatar == null` → inisial nama.

### 7.4 Deep link

- `url` adalah URL web (`https://konutupdate.com/{slug}`) — berguna untuk share.
- Untuk membuka di aplikasi, ambil `slug` dan arahkan ke layar detail internal.

### 7.5 Cache lokal

- Cache feed 3–5 menit; detail artikel 10 menit.
- Tetap izinkan pull-to-refresh memaksa request baru.
- Jangan cache respons error.

---

## 8. Verifikasi cepat

```bash
BASE=https://konutupdate.com/api/v1

curl -s -o /dev/null -w "%{http_code}  /home\n"      $BASE/home
curl -s -o /dev/null -w "%{http_code}  /latest\n"    "$BASE/latest?per_page=2"
curl -s -o /dev/null -w "%{http_code}  /trending\n"  $BASE/trending
curl -s -o /dev/null -w "%{http_code}  /opini\n"     $BASE/opini
curl -s -o /dev/null -w "%{http_code}  /featured\n"  $BASE/featured
curl -s -o /dev/null -w "%{http_code}  /categories\n" $BASE/categories
curl -s -o /dev/null -w "%{http_code}  /videos\n"    $BASE/videos
```

Yang harus benar:

- semua **200** (kecuali `/api/v1` tanpa path = 404, itu normal);
- `success: true` dan `data` sesuai bentuk di atas;
- `thumbnail`/`url` memakai `https://konutupdate.com`, bukan `localhost`/IP;
- `meta.last_page >= 1` dan `links.next` `null` hanya di halaman terakhir.

---

## 9. Catatan deploy yang memengaruhi API

- `APP_URL` harus `https://konutupdate.com` — dipakai `asset()`/`url()` untuk gambar dan `links`.
- `storage:link` harus ada, jika tidak semua `thumbnail` 404.
- Jika memakai `route:cache`, **wajib** `php artisan route:cache` ulang setiap ada endpoint baru — kalau tidak, endpoint baru 404.
- `APP_DEBUG` **harus `false`** di produksi (saat ini masih `true`; lihat §2.6).
- Perubahan konfigurasi setelah edit `.env`: `php artisan config:cache` (atau `config:clear` bila tidak memakai cache).
- API tidak memakai cache server, jadi konten yang baru dipublish langsung terlihat (kecuali cache lokal aplikasi).

---

## 10. Perubahan yang memutus kompatibilitas

Jika nanti ditambahkan auth/token, like/bookmark, atau endpoint tulis:

- tambah field baru boleh (aditif);
- **jangan** mengubah/menghapus field yang sudah ada tanpa menaikkan versi (`/api/v2`);
- `is_liked`/`is_bookmarked` cukup ditambahkan sebagai field baru (default `false` untuk tamu).

Dokumen ini harus diperbarui bersamaan dengan perubahan `routes/api.php` atau resource terkait.
