# Blueprint Aplikasi Mobile KonutUpdate

Status: **rancangan awal, belum implementasi**
Target awal: aplikasi Android yang bisa di-install sebagai APK
Target berikutnya: iOS setelah tersedia Mac/Xcode atau build cloud
Prinsip: website tetap berjalan; aplikasi mobile memakai API Laravel yang sama

---

## 1. Tujuan

Membuat aplikasi berita KonutUpdate yang:

- menampilkan berita terkini, headline, trending, video, dan opini;
- nyaman dipakai di HP dengan interaksi cepat seperti timeline media sosial;
- memungkinkan pembaca login, menyimpan berita, memberi like, berkomentar, dan mengirim opini;
- mengirim notifikasi breaking news;
- tetap memakai database, admin, dan logika bisnis Laravel yang sudah ada.

## 2. Non-goals (ditunda)

- Publikasi Play Store dan App Store.
- Aset store (screenshot, feature graphic, deskripsi store).
- Proses review Apple/Google.
- Fitur report/block UGC tingkat lanjut.
- Versi iOS sebelum ada perangkat build.

Store dapat dipersiapkan setelah aplikasi stabil.

---

## 3. Arsitektur

```text
Website Blade + admin  ─┐
                        ├─ Database + logika Laravel
Aplikasi Flutter ───────┘        │
        │                        │
        └──── HTTPS + token ─────┘
                 API /api/v1
```

- Website tetap memakai Blade seperti sekarang.
- Aplikasi Flutter **tidak** merender Blade.
- API mengembalikan JSON.
- Admin dan redaksi tetap memakai website.
- Satu database, satu sumber konten, tanpa sinkronisasi manual.

### Stack

| Bagian | Pilihan |
|---|---|
| Backend | Laravel 12 |
| Auth API | Laravel Sanctum |
| Mobile | Flutter |
| State management | Riverpod |
| HTTP client | Dio |
| Local storage | Hive/Isar |
| Push notification | Firebase Cloud Messaging |
| Analytics | Firebase Analytics + GA4 |
| Crash report | Firebase Crashlytics |
| Deep link | App Links Android, Universal Links iOS |

---

## 4. Arah UI: editorial modern ala X

Yang diadopsi dari X/Twitter adalah **pola interaksi dan kepadatan informasi**, bukan menyalin logo atau identitasnya. Kesan modern datang dari tipografi, gambar, dan transisi halus — bukan dari banyak efek atau menu tersembunyi.

### Prinsip UX

- **Baca dulu, interaksi kemudian.** Tamu bisa membaca tanpa login.
- **Satu layar satu tujuan.** Jangan menumpuk terlalu banyak aksi dalam satu tampilan.
- **Navigasi terlihat.** Jangan menggantungkan fitur penting pada gesture tersembunyi.
- **Label jelas.** Ikon penting selalu punya teks atau tooltip.
- **Konsisten.** Posisi tombol back, share, bookmark, dan like sama di semua layar.
- **Tidak ada jalan buntu.** Setiap loading, error, dan empty state punya aksi lanjutan.
- **Aksesibel.** Kontras cukup, target sentuh minimal 48dp, teks bisa diperbesar.

### Prinsip visual

- Feed edge-to-edge, sedikit kartu, banyak pemisah tipis.
- Fokus pada judul, gambar, sumber, waktu, dan aksi.
- Navigasi bawah **4 tab**; notifikasi lewat ikon di header.
- Infinite scroll + pull-to-refresh.
- Dark mode dan light mode.
- Animasi singkat 150–250 ms.
- Tidak banyak bayangan dan gradien; utamakan tipografi dan ruang.
- Gambar besar dan berkualitas, tetapi tidak menutupi judul.

### Navigasi utama

1. **Beranda**
2. **Cari**
3. **Video**
4. **Profil**

Notifikasi dibuka lewat ikon lonceng di header, bukan tab tersendiri. Ini mengurangi kepadatan dan membuat navigasi lebih mudah dipahami.

### Beranda

- Header: logo KonutUpdate, tombol cari, tombol notifikasi.
- Banner breaking news jika ada.
- Hero headline: satu berita utama dengan gambar besar.
- Tab: `Terbaru`, `Trending`, `Opini`, `Pilihan`.
  - `Terbaru` = berita terbaru non-opini; `Trending` = urut jumlah dibaca; `Opini` = kiriman opini; `Pilihan` = konten `is_featured` yang belum kedaluwarsa (14 hari).
  - `Untuk Anda` tidak dipakai dulu karena belum ada sistem rekomendasi; label itu berpotensi membingungkan.
- Kartu feed:
  - avatar/sumber + nama redaksi + waktu;
  - label kategori;
  - judul 2–3 baris;
  - ringkasan 2 baris;
  - thumbnail 16:9 atau gambar kanan;
  - aksi: komentar, like, bookmark, share.
- Pull-to-refresh, skeleton loading, infinite scroll.

### Detail artikel

- Gambar hero edge-to-edge.
- Judul, penulis, tanggal, kategori.
- Tombol share dan bookmark.
- Isi artikel dengan tipografi nyaman: body 16–17, line-height 1.65, paragraf tidak terlalu lebar.
- Progress baca tipis di atas.
- Tag/terkait.
- Komentar dibuka lewat bottom sheet.
- Tombol like/komentar/share dapat sticky di bawah.
- Tidak ada popup interstitial yang menutupi artikel.

### Cari

- Search bar sticky.
- Trending pencarian.
- Riwayat pencarian lokal.
- Hasil dengan filter kategori/waktu.

### Video

- Daftar video dengan thumbnail, durasi, dan tombol play.
- Pemutar full-screen.
- Dukungan YouTube/Vimeo/TikTok dan file upload.

### Notifikasi

- Breaking news.
- Aktivitas: like dan komentar (jika login).
- Pengumuman sistem.
- Pengaturan notifikasi per jenis.

### Profil

- Avatar, nama, bio, statistik.
- Tab: `Kiriman Saya`, `Bookmark`, `Pengaturan`.
- Tombol `Kirim Opini`.
- Logout.

### Kirim opini

- Form judul, isi, gambar.
- Preview.
- Submit ke status `pending`.
- Riwayat kiriman dan alasan penolakan.

### Komentar

- Bottom sheet.
- Hanya komentar `approved` yang tampil.
- Input komentar wajib login.
- Like komentar dapat ditunda.

### Design token awal

| Token | Nilai |
|---|---|
| Font | Inter |
| Warna aksen | warna brand KonutUpdate |
| Teks utama | near-black |
| Teks sekunder | abu netral |
| Surface terang | putih / abu sangat muda |
| Surface gelap | near-black |
| Divider | 1px abu transparan |
| Radius kartu | 16 |
| Radius chip/avatar | 999 |
| Body artikel | 16–17 / line-height 1.65 |
| Judul feed | 18 / 26 |
| Target sentuh | minimal 48dp |

Warna brand tetap dipakai. Biru X tidak harus dipakai. Modernitas dicapai lewat hierarki tipografi, kualitas foto, dan konsistensi — bukan animasi berlebihan.

---

## 5. Peta layar

```text
Splash
  └─ Login / Daftar / Lanjut sebagai tamu
       └─ Shell utama
            ├─ Beranda
            │    ├─ Detail artikel
            │    ├─ Komentar
            │    └─ Share
            ├─ Cari
            │    └─ Hasil pencarian
            ├─ Video
            │    └─ Pemutar video
            ├─ Notifikasi (dari ikon header)
            └─ Profil
                 ├─ Kiriman Saya
                 ├─ Bookmark
                 ├─ Kirim Opini
                 ├─ Edit Profil
                 └─ Pengaturan
```

---

## 6. Kontrak API

Base URL: `/api/v1`
Auth: `Authorization: Bearer <token>` (belum diimplementasikan — API saat ini publik/read-only)
Format: JSON

**Kontrak lengkap, termasuk aturan bisnis yang harus ditiru aplikasi: `docs/api-mobile-kontrak.md`.**

### Publik

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/home` | headline, trending, terbaru, breaking, opini |
| GET | `/latest` | feed tab Terbaru (tanpa opini), paginasi |
| GET | `/trending` | feed tab Trending (urut `views_count`), paginasi |
| GET | `/opini` | feed tab Opini, paginasi |
| GET | `/featured` | feed tab Pilihan (`is_featured` aktif), paginasi |
| GET | `/posts` | daftar berita, filter kategori/kecamatan/tag |
| GET | `/posts/{slug}` | detail artikel |
| GET | `/categories` | daftar kategori |
| GET | `/kecamatan` | daftar kecamatan |
| GET | `/videos` | daftar video |
| GET | `/opini` | daftar opini |
| GET | `/search?q=` | pencarian |

### Auth

| Method | Endpoint | Keterangan |
|---|---|---|
| POST | `/auth/register` | daftar akun |
| POST | `/auth/login` | login, mengembalikan token |
| POST | `/auth/logout` | revoke token |
| GET | `/me` | profil user |
| PUT | `/me` | update profil |
| POST | `/me/avatar` | upload avatar |
| POST | `/auth/forgot-password` | kirim reset |
| POST | `/auth/reset-password` | reset password |

### Interaksi

| Method | Endpoint | Auth | Keterangan |
|---|---|---|---|
| GET | `/bookmarks` | ya | daftar bookmark |
| POST | `/posts/{post}/bookmark` | ya | toggle bookmark |
| POST | `/posts/{post}/like` | ya | toggle like |
| GET | `/posts/{post}/comments` | tidak | komentar approved |
| POST | `/posts/{post}/comments` | ya | kirim komentar |
| POST | `/opini` | ya | kirim opini |
| POST | `/opini/upload-image` | ya | upload gambar opini |
| GET | `/my-opini` | ya | riwayat opini |
| POST | `/device-tokens` | ya | daftar token FCM |

### Contoh response daftar

```json
{
  "data": [
    {
      "id": 1,
      "slug": "contoh-berita",
      "title": "Contoh Berita",
      "excerpt": "Ringkasan berita...",
      "image": "https://konutupdate.com/storage/...",
      "image_alt": "Contoh Berita",
      "category": { "name": "Politik", "slug": "politik" },
      "author": { "name": "Redaksi", "slug": "redaksi" },
      "published_at": "2026-10-23T10:00:00+08:00",
      "likes_count": 12,
      "comments_count": 3,
      "is_liked": false,
      "is_bookmarked": false
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 20
  }
}
```

### Aturan umum

- Hanya konten `published` yang dikirim ke publik.
- Konten `duplicate_of_id` tetap `noindex` di web dan tidak masuk feed utama.
- Gambar memakai URL absolut.
- Pagination awal memakai `page` + `per_page`.
- Error memakai format konsisten:

```json
{
  "message": "Validasi gagal",
  "errors": {
    "email": ["Email wajib diisi."]
  }
}
```

---

## 7. Perubahan backend

### Paket dan konfigurasi

- `php artisan install:api` untuk Sanctum dan `routes/api.php`.
- Daftarkan route API di `bootstrap/app.php`.
- Publikasikan `config/cors.php`.
- Tambah rate limiter API.

### Struktur

```text
app/Http/Controllers/Api/V1/
app/Http/Requests/Api/
app/Http/Resources/
app/Services/Api/
routes/api.php
tests/Feature/Api/
```

### Migrasi baru

| Tabel | Isi |
|---|---|
| `personal_access_tokens` | token Sanctum |
| `device_tokens` | token FCM per user/device |
| `bookmarks` | user_id + post_id unik |
| `comments.user_id` | nullable, untuk komentar dari app |
| `likes.user_id` | nullable, untuk like dari app |

Catatan penting:

- Web saat ini memakai `ip_address` untuk komentar dan like.
- Aplikasi memakai `user_id`.
- Jangan hapus kolom lama; web harus tetap berjalan.
- Hitungan like/komentar harus menggabungkan data web dan app tanpa duplikasi.

### Admin

- Moderasi komentar sudah ada; pastikan bisa melihat komentar dari app.
- Moderasi opini sudah ada.
- Tambah halaman **Kirim Notifikasi**:
  - judul;
  - isi;
  - target: semua / breaking;
  - tautan artikel;
  - jadwal atau kirim langsung.
- Tambah pengelolaan device token jika diperlukan.

---

## 8. Keamanan

- Token Sanctum, bukan session cookie.
- Password hashing dan validasi sama seperti web.
- Rate limit login/register/komentar/like/opini.
- Sanitasi HTML artikel dan komentar.
- Validasi tipe dan ukuran gambar.
- Jangan menaruh `GEMINI_API_KEY`, secret Turnstile, atau kredensial admin di aplikasi.
- AI chat tetap lewat backend.
- Gunakan HTTPS.
- Turnstile mobile:
  - opsi A: Cloudflare Turnstile SDK Android/iOS;
  - opsi B: email verification + rate limit + device attestation.
  - keputusan ditunda sampai fase auth.

---

## 9. Tahapan implementasi

### Fase 0 — Blueprint (sekarang)

- Finalisasi scope.
- Finalisasi wireframe.
- Finalisasi kontrak API.
- Finalisasi design token.

### Fase 1 — Fondasi API

- Sanctum, CORS, route versioning.
- API Resource.
- Endpoint publik read-only.
- Test API.

### Fase 2 — Flutter core

- Shell navigasi.
- Home, detail, kategori, pencarian, video, opini.
- Loading/error/empty state.
- Build APK pertama.

### Fase 3 — Akun

- Login/register/logout.
- Profil.
- Bookmark.
- Push notification.
- Pengaturan.

### Fase 4 — Interaksi

- Like.
- Komentar.
- Kirim opini + upload gambar.
- Riwayat opini.
- Moderasi di admin.

### Fase 5 — Stabilisasi

- Uji di HP nyata.
- Perbaiki performa dan cache.
- Uji jaringan lambat/offline.
- Siapkan beta internal.

---

## 10. Kriteria APK pertama dianggap berhasil

- Bisa di-install di Android.
- Home, detail, kategori, pencarian, video, dan opini memuat data nyata.
- Pull-to-refresh dan infinite scroll bekerja.
- Tidak crash saat pindah tab, rotate, atau kehilangan jaringan.
- Share artikel membuka aplikasi lain.
- Dark mode dan light mode bekerja.
- Waktu muat awal wajar di jaringan 4G.
- Artikel nyaman dibaca: teks tidak terlalu kecil, line-height cukup, kontras aman.
- Pengguna baru bisa menemukan berita tanpa membuka menu tersembunyi.
- Semua tab dan tombol utama bisa dijangkau satu tangan.

---

## 11. Keputusan yang masih terbuka

1. Warna brand: tetap hijau/oranye KonutUpdate atau netral ala X?
2. Dark mode default atau mengikuti sistem?
3. Komentar wajib login atau boleh anonim?
4. Like app memakai akun, web tetap IP-based?
5. Push notification hanya breaking news atau semua berita baru?
6. Login email/password dulu, atau langsung Google/Apple?
7. Siapa yang memoderasi komentar dan opini setiap hari?
8. Apakah opini dari app wajib upload gambar?
9. Apakah perlu fitur simpan offline penuh atau hanya cache?
10. Kapan iOS dikerjakan dan dengan Mac/cloud Mac siapa?

---

## 12. Rekomendasi keputusan awal

- Android-first.
- UI mengikuti pola X, warna mengikuti brand KonutUpdate.
- Dark mode mengikuti sistem.
- Komentar wajib login.
- Like app memakai akun; web tetap IP-based.
- Push hanya breaking news.
- Login email/password dulu.
- Bookmark server untuk user login; tamu boleh bookmark lokal.
- Opini dari app mendukung satu gambar.
- Store ditunda sampai aplikasi stabil.
