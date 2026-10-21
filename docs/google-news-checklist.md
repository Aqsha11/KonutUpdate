# Checklist Kesiapan Google News — Konut Update

Dokumen ini adalah **checklist kerja**, bukan jaminan hasil.
Google News tidak memberi jaminan penempatan: entri hanya tampil bila Google
menilai artikel relevan, baru, dan layak. Yang bisa kita lakukan adalah
memenuhi seluruh persyaratan teknis dan menjaga konsistensi redaksional.

> **Status saat ini (22 Okt 2026):** seluruh item teknis di bawah sudah
> diimplementasikan **di repo lokal** dan ter-cover test, tetapi **belum
> ter-deploy** ke `https://konutupdate.com` (lihat `docs/audit-seo.md` §7).
> Item yang butuh Search Console / Publisher Center **belum bisa diverifikasi**
> dari sisi tim developers.

---

## 0. Ringkasan status

| Area | Status | Catatan |
|---|---|---|
| News sitemap | ✅ Kode siap | `/sitemap-news.xml` + alias `/news-sitemap.xml`, artikel ≤ 2 hari, maks 1.000 URL |
| `robots.txt` mendaftarkan sitemap | ✅ Kode siap | Dua baris `Sitemap:` |
| Halaman artikel: satu canonical | ✅ Kode siap | Canonical absolut di layout |
| Halaman artikel: `noindex` tidak dipakai | ✅ Kode siap | Hanya artikel **duplikat** yang di-`noindex` |
| JSON-LD `NewsArticle` | ✅ Kode siap | `headline`, `datePublished`, `dateModified`, `author`, `publisher`, `image` |
| Byline yang dapat diverifikasi | ✅ Kode siap (isi data menyusul) | Field CMS `author_id` + profil `/penulis/{slug}` + `Person` JSON-LD |
| Gambar artikel | ✅ Kode siap | `og:image` 1200×675 (thumbnail → logo → `/og-default.jpg`) |
| Mobile-friendly & HTTPS | ✅ Sudah ada | HSTS `max-age=31536000; includeSubDomains` |
| Core Web Vitals | ⚠️ Belum diukur | Perlu PageSpeed Insights/CrUX setelah deploy |
| Sitemap terverifikasi di GSC | ❌ Belum | Butuh akses Search Console |
| Publisher terverifikasi (Publisher Center / GSC News) | ❌ Belum | Butuh akun Google News |
| Konsistensi terbit | ❌ Belum dinilai | Butuh data redaksi (target: minimal 1 artikel/hari) |

---

## 1. Checklist teknis (sudah dikerjakan)

### 1.1 URL & aksesibilitas
- [x] Artikel dapat diakses tanpa login, tanpa paywall, tanpa interstitial.
- [x] Tidak ada `Disallow` di `robots.txt` untuk halaman artikel.
- [x] `Disallow` hanya untuk area utilitas: `/admin`, `/login`, `/daftar`, `/email/`, `/search`, `/kirim-opini`.
- [x] URL artikel ramah (root-level slug), tidak berubah-ubah setelah terbit.
- [x] `GET /berita/{slug}` → **301** ke URL kanonik (bukan 302).
- [x] Halaman statis punya URL friendly + **301** dari `/p/{slug}`.

### 1.2 Canonical & duplikat
- [x] Tepat satu `<link rel="canonical">` per halaman, absolut.
- [x] Artikel yang ditandai duplikat (`posts.duplicate_of_id`) otomatis `noindex, follow` **dan** dikeluarkan dari sitemap.
- [x] Deteksi judul mirip (skor ≥ 85%) tampil di form admin sebagai peringatan.
- [x] Halaman tag dengan < 2 artikel, kategori/kecamatan kosong → `noindex, follow` (bukan `nofollow`, supaya tautan tetap dialirkan).
- [x] Halaman pencarian & profil akun → `noindex, nofollow`.

### 1.3 Metadata artikel
- [x] `title` unik per artikel (judul, bukan "Judul – Situs").
- [x] `meta description` deskriptif, 120–160 karakter.
- [x] `og:type` = `article` pada halaman artikel, `website` pada halaman lain.
- [x] `og:url` = canonical, `og:title`, `og:description`, `og:image` (+ `og:image:width/height/alt`).
- [x] `twitter:card` = `summary_large_image`.
- [x] Tidak ada `meta keywords` (diabaikan mesin pencari modern).

### 1.4 Structured data
- [x] `Organization` + `WebSite` dengan `@id` absolut (`https://konutupdate.com/#organization`).
- [x] `NewsArticle` pada halaman artikel: `headline`, `description`, `datePublished`, `dateModified`, `author`, `publisher`, `image`, `mainEntityOfPage`, `keywords`.
- [x] `dateModified` memakai `content_updated_at` (hanya berubah bila isi artikel benar-benar berubah) — bukan `updated_at` yang melompat saat flag admin di-toggle.
- [x] `Person` pada halaman profil penulis (`/penulis/{slug}`), tertaut dari byline artikel.
- [x] `VideoObject` untuk `type=video` (terpisah dari `NewsArticle`).
- [x] `BreadcrumbList` pada halaman indeks.

### 1.5 Sitemap
- [x] News sitemap: hanya artikel published, hanya 2 hari terakhir, tanpa duplikat, dengan `news:publication_date` (ISO 8601), `news:title`, `news:keywords`.
- [x] Sitemap utama: artikel, arsip (`/terkini`, `/trending`, `/semua-berita`, `/opini`, `/video`), kategori (lewat pivot `post_categories`), kecamatan, tag (≥ 2 artikel), halaman statis friendly + `/p/*`.
- [x] Kedua sitemap didaftarkan di `robots.txt`.
- [x] `lastmod` memakai tanggal publikasi, bukan tanggal admin terakhir menyimpan.

### 1.6 Performa
- [x] Gambar listing `loading="lazy"`; hero & gambar artikel `loading="eager" fetchpriority="high"`.
- [x] Jumlah query per homepage turun dari **170 → 47** (cache dingin; lihat `docs/audit-seo.md` §3.5).
- [ ] **Belum:** mengukur Core Web Vitals di field (butuh data setelah deploy).

---

## 2. Checklist redaksi (di luar kode)

Sinyal editorial adalah bagian terbesar dari penempatan Google News.

- [ ] Terbit **minimal 1 artikel berita per hari** (hari libur termasuk). Inkonsisten adalah penyebab paling umum ditolak.
- [ ] Judul informatif & spesifik, tidak clickbait, tidak fitnah/kebohongan, tidak menyalin judul kompetitor.
- [ ] Artikel asli (bukan salinan/rehash dari sumber lain) — tulis ulang fakta dengan sudut pandang sendiri.
- [ ] Foto deskriptif dengan **alt text** terisi (field ada di form admin).
- [ ] Tanggal & jam publikasi benar (zona waktu situs: **Asia/Makassar**).
- [ ] Byline diisi (pilih **Akun Penulis**) dan profil penulis punya bio + foto.
- [ ] Artikel lama yang diperbarui **tidak** otomatis menjadi "baru" (sudah ditangani lewat `content_updated_at`) — jangan menyentuh artikel lama hanya demi "menyamakan tanggal".
- [ ] Koreksi kesalahan pada berita: perbarui isi (bukan hanya tambah catatan) agar `dateModified` mencerminkan perubahan nyata.
- [ ] Hapus/tandai duplikat (menu **Artikel Duplikat**) daripada menerbitkan berita kembar.
- [ ] Halaman tag dipakai seperlunya; 200+ tag dengan sedikit artikel sebaiknya dibersihkan (lihat `docs/audit-seo.md` §3.8 R1).
- [ ] Tidak ada halaman jebakan yang memaksa pengguna mengeklik (Google News bebas memotong).

---

## 3. Verifikasi teknis setelah deploy

```bash
# 1) Semua endpoint SEO harus 200
for u in / /robots.txt /sitemap.xml /sitemap-news.xml /news-sitemap.xml /feed; do
  curl -s -o /dev/null -w "%{http_code}  $u\n" "https://konutupdate.com$u"
done

# 2) Artikel contoh: cek canonical, robots, JSON-LD
curl -s https://konutupdate.com/<slug-artikel> \
  | grep -E 'rel="canonical"|name="robots"|"@type"|datePublished|dateModified'

# 3) News sitemap harus berisi <news:url>
curl -s https://konutupdate.com/sitemap-news.xml | head -20

# 4) robots.txt harus mendaftarkan kedua sitemap
curl -s https://konutupdate.com/robots.txt
```

Target: semua **200**, satu canonical, `robots` = `index, follow` pada artikel
biasa, `news:url` ada, dua baris `Sitemap:`.

---

## 4. Langkah Search Console (perlu akses)

1. **Verifikasi properti** `https://konutupdate.com/` (DOMAIN atau URL-prefix). Pastikan `www` & non-`www` + http/https归结 ke satu host (pakai URL-prefix agar jelas).
2. **Sitemaps** → submit `https://konutupdate.com/sitemap.xml` dan `https://konutupdate.com/sitemap-news.xml`. Pantau "Success" & jumlah URL dibaca.
3. **News** (tab News, hanya untuk publisher terverifikasi) → verifikasi publisher; setelah lolos, artikel baru bisa masuk Google News.
4. **Publisher Center** (`publisher.google.com`) → daftarkan konutupdate.com sebagai publisher berita (jalur alternatif verifikasi publisher).
5. **URL Inspection** → cek satu artikel: "Google selected this URL as canonical" harusnya `https://konutupdate.com/<slug>`.
6. **Page Indexing → Request indexing** untuk artikel baru (opsional; news sitemap sudah otomatis).
7. **Settings → Crawl stats** → pastikan tidak ada 5xx & "Crawled - currently not indexed" mendominasi.
8. **Core Web Vitals** & **PageSpeed Insights** → catat LCP/INP/CLS setelah deploy.
9. **Manual actions & Security issues** → pastikan kosong.

> Status "Discovered – currently not indexed" pada artikel baru biasanya normal
> selama 1–3 hari. Yang perlu dipantau justru artikel lama yang tidak pernah
> ter-index.

---

## 5. Pemantauan berkala

| Frekuensi | Yang dipantau | Tujuan |
|---|---|---|
| Harian | Artikel terbit ≥ 1; `sitemap-news.xml` berisi URL terbaru | Jaga ritme terbit & news sitemap tetap segar |
| Mingguan | Tab News impressions & klik; status sitemap | Deteksi penurunan |
| Mingguan | "Discovered – currently not indexed" | Deteksi artikel yang tak ter-index |
| Bulanan | Core Web Vitals (CWV) report | Jaga performa |
| Bulanan | Duplikasi: berapa artikel ditandai duplikat | Efektivitas alur editorial |

---

## 6. Risiko & batasan

- **Tidak ada jaminan penempatan.** Google News menampilkan artikel yang
  dianggap relevan & baru; satu artikel pun bisa tidak masuk meski semua
  syarat teknis terpenuhi.
- **Verifikasi publisher bisa ditolak** tanpa alasan yang detail.
- **Konten yang diduplikasi dari sumber lain** (bukan laporan asli) tetap
  berisiko tidak di-index meski secara teknis valid.
- **Data produksi belum diverifikasi** karena tidak ada salinan DB produksi;
  temuan tentang 210 tag & 8 kategori berasal dari HTML/sitemap publik.
- Item yang masih **terbuka**: nilai `site_name` produksi ("Konutupdate" →
  "KonutUpdate"), byline yang belum terisi, dan ambang indeksasi halaman tag.

---

*Dokumen ini dibuat sebagai panduan kerja. Status teknis diukur dari
kode & test lokal; status indeksasi harus dikonfirmasi langsung di Google Search
Console setelah deploy.*
