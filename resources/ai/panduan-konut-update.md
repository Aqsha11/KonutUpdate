# PANDUAN PENGGUNAAN SITUS KONUT UPDATE

Dokumen ini adalah sumber kebenaran untuk menjawab pertanyaan pengguna tentang cara memakai
portal Konut Update. Jawab pertanyaan hanya berdasarkan isi panduan ini, jangan mengarang
langkah, fitur, batas, atau alamat halaman yang tidak tertulis di bawah.

## 1. Tentang Konut Update

Konut Update adalah portal berita daring yang mencakup Kota dan Kabupaten Konawe Utara,
Sulawesi Tenggara. Situs memuat berita, opini tulisan pembaca, video, dan ringkasan berita
per kecamatan. Bahasa pengantar: Bahasa Indonesia.

- Hanya artikel berstatus terbit (published) yang tampil di halaman publik.
- Opini pembaca harus lolos verifikasi redaksi lebih dulu sebelum tayang.
- Tidak ada biaya langganan dan tidak ada layanan berbayar.

## 2. Cara membuat akun (registrasi)

1. Buka halaman `/daftar`. Tombol "Daftar" tersedia di halaman masuk, di menu akun, dan di
   tombol mengambang di kanan bawah layar (menu "Masuk / Daftar").
2. Isi formulir:
   - Nama: 3-100 karakter, hanya huruf, spasi, dan tanda baca dasar (apostrof, tanda hubung).
   - Email: alamat email valid dan belum terdaftar (satu akun per email).
   - Kata sandi: minimal 8 karakter, harus ada satu huruf kapital, satu huruf kecil, dan satu angka.
   - Ulangi kata sandi: sama persis dengan kata sandi.
3. Selesaikan verifikasi "bukan robot" (Cloudflare Turnstile) bila widget muncul di halaman.
4. Klik tombol "Daftar". Akun berhasil dibuat dan email verifikasi dikirim seketika.
5. Buka email yang didaftarkan lalu klik tautan verifikasi di dalam email tersebut.
   Akun yang emailnya belum diverifikasi tidak dapat masuk.
6. Setelah verifikasi, akun langsung aktif dengan peran **Pembaca**.

Catatan penting:
- Pendaftaran gratis, tidak ada biaya, dan tidak perlu diaktifkan oleh admin.
- Tidak ada pendaftaran mandiri untuk peran Admin, Editor, Reporter, atau Kontributor.
  Peran tersebut hanya diberikan pihak redaksi.
- Peran Kontributor tetap mendapat halaman profil publik dan hak mengirim opini, persis
  seperti Pembaca. Tidak ada panel khusus kontributor.

## 3. Masuk, lupa kata sandi, dan verifikasi email

- **Masuk**: buka `/login`, isi email dan kata sandi, lalu klik "Masuk".
  Batas 10 percobaan login per menit per alamat IP.
- **Lupa kata sandi**: buka `/lupa-password`, isi email, sistem mengirim tautan atur ulang.
  Buka tautan itu, lalu buat kata sandi baru dengan syarat yang sama seperti saat pendaftaran.
- **Verifikasi ulang**: buka `/email/verifikasi/ulang` untuk meminta email verifikasi baru
  (maksimal 6 permintaan per menit).
- Sesi di halaman redaksi (admin) otomatis keluar setelah 5 menit tanpa aktivitas.

## 4. Halaman profil (/profil)

Setelah masuk, buka `/profil` (tautan "Profil" di menu, atau "Akun Saya" di tombol mengambang).
Halaman ini punya dua tab:

- **Kirman Saya**: daftar seluruh opini yang pernah dikirim (10 per halaman) dengan ringkasan
  jumlah kiriman terbit, menunggu verifikasi, dan ditolak. Tersedia filter status
  (semua / terbit / menunggu / ditolak). Opini yang ditolak menampilkan alasan penolakan.
- **Edit Profil**: mengubah nama, email, dan kata sandi (perlu mengisi kata sandi lama),
  serta mengunggah foto profil (JPG/PNG/WebP, maksimal 2 MB).

Setiap akun punya satu halaman profil publik di `/penulis/{slug}` yang menampilkan foto, nama,
singkatan, dan daftar artikel penulis. Halaman profil publik itulah yang muncul sebagai
nama penulis di bawah judul artikel.

## 5. Cara mengirim opini (langkah lengkap)

Syarat: sudah masuk ke akun dan email sudah terverifikasi. Tidak perlu menjadi kontributor.

1. Masuk ke akun, lalu buka `/profil`.
2. Klik tombol "Kirim Opini". Tombol yang sama tersedia di menu tombol mengambang, dan
   membuka halaman `/kirim-opini` yang langsung memunculkan formulir.
3. Isi formulir:
   - **Judul Opini** (wajib, maksimal 255 karakter).
   - **Ringkasan** (opsional, maksimal 500 karakter).
   - **Isi Opini** (wajib, minimal 50 karakter). Gunakan editor teks untuk tebal, miring,
     daftar, dan menyisipkan gambar di dalam tulisan (unggah gambar lewat ikon gambar pada
     editor; format JPG/PNG/GIF/WebP, maksimal 5 MB per file).
   - **Gambar Utama** (opsional, JPG/PNG/WebP maksimal 2 MB). Gambar otomatis diperkecil
     menjadi 1200x675 piksel.
   - **Kategori** (opsional, pilih maksimal 3 kategori).
   - **Kecamatan** (opsional) supaya tulisan lebih mudah ditemukan pembaca daerah.
   - **Tag** (opsional) — tulis beberapa tag dipisahkan tanda koma.
4. Klik tombol "Kirim Opini".
5. Status kiriman:
   - "Menunggu verifikasi" (pending) — opini sudah masuk ke meja redaksi.
   - "Terbit" (published) — disetujui redaksi, tampil di halaman `/opini` dan di profil penulis.
   - "Ditolak" (rejected) — disertai alasan penolakan pada tab Kiriman Saya.

Etika menulis opini:
- Tulis argumen sendiri dengan bahasa baku, sertakan fakta dan sumber yang jelas.
- Jangan menulis berita straight, menyalin berita/surat keputusan pemerintah, atau memuat fitnah.
- Jangan menyalin tulisan orang lain; kutipan harus diberi keterangan sumber.
- Jangan memuat ujaran kebencian, diskriminasi, provokasi, atau informasi yang melanggar privasi.

Catatan: pembaca tidak dapat mengirim artikel berita biasa. Pengiriman berita dilakukan
oleh tim redaksi; pembaca hanya dapat mengirim opini.

## 6. Peran pengguna

- **Pembaca** (peran otomatis saat pendaftaran): bertanya lewat Tanya Berita AI, mengirim opini,
  memberi like, dan menulis komentar.
- **Kontributor**: peran diberikan oleh super admin lewat halaman `/admin/users` (termasuk
  proses verifikasi email). Kemampuan kontributor saat ini sama dengan pembaca: memakai
  halaman profil publik dan mengirim opini.
- **Editor, Reporter, Super Admin**: tim redaksi internal yang mengelola berita, opini, video,
  kategori, kecamatan, story, iklan, dan pengaturan situs lewat dashboard admin.

## 7. Menjelajah berita

- `/` — beranda: berita utama, berita terbaru, trending, video, opini, dan berita per kecamatan.
- `/terkini` — berita paling baru.
- `/semua-berita` — arsip seluruh berita (bisa difilter per kategori).
- `/trending` — berita yang paling banyak dibaca dan dibahas.
- `/video` — kumpulan video.
- `/opini` — opini tulisan pembaca.
- `/kategori/{slug}` — berita per kategori, `/kecamatan/{slug}` — berita per kecamatan,
  `/tag/{slug}` — berita per tag.
- `/search?q=kata+kunci` — pencarian berita, bisa digabung dengan filter kategori.
- `/feed` — RSS untuk pembaca berita, `/sitemap.xml` — peta situs.

## 8. Berinteraksi dengan berita

- **Like**: tekan tombol hati pada kartu berita. Satu like per alamat IP untuk tiap artikel
  (maksimal 30 permintaan per menit), tidak perlu login.
- **Komentar**: bagian komentar di akhir artikel. Isi nama, email, dan isi komentar
  (maksimal 2.000 karakter). Komentar menunggu moderasi redaksi sebelum tayang
  (maksimal 5 permintaan per menit).
- **Bagikan**: tombol bagikan di bawah artikel untuk menyalin tautan atau dibagikan ke media sosial.

## 9. Halaman resmi

- `/tentang-kami` — profil redaksi dan sejarah Konut Update.
- `/pedoman-media-siber` — pedoman jurnalistik, hak jawab, dan aturan moderasi.
- `/privacy-policy` — kebijakan privasi dan pengelolaan data pembaca.
- `/kontak` — alamat dan kontak redaksi.

## 10. Tanya Berita AI

- Tombol mengambang di kanan bawah layar membuka menu: "Tanya AI" (asisten berita),
  "Masuk/Daftar" atau "Akun Saya", dan "Kirim Opini".
- Yang bisa ditanyakan: berita terkini Konawe Utara, ringkasan isi artikel, perbandingan
  antar berita, dan panduan memakai situs ini (cara daftar akun, cara mengirim opini, cara
  menjadi kontributor, cara like dan komentar, dan lain-lain).
- Tamu mendapat jatah pertanyaan gratis terbatas per alamat IP (bawaan 2 pertanyaan per hari).
  Setelah jatah habis, pengunjung diminta masuk atau mendaftar. Pengguna yang sudah masuk
  tidak memakai jatah tamu, tetapi tetap tunduk pada batas 10 pertanyaan per menit.
- Jawaban dibuat oleh AI dari isi berita dan panduan ini. Selalu periksa tautan sumbernya.

## 11. Batasan dan hal yang tidak tersedia

- Tidak ada pendaftaran akun admin, editor, reporter, atau kontributor secara mandiri.
- Tidak ada layanan berlangganan, pembelian konten, atau donasi online.
- Tidak ada layanan informasi berbayar, penjualan barang/jasa, atau transaksi daring di situs ini.
- Tidak ada pengiriman berita atau informasi dari pembaca selain opini.
- Semua isi tunduk pada Pedoman Media Siber; redaksi dapat menolak, memperbaiki, atau
  menghapus kiriman yang tidak sesuai.
