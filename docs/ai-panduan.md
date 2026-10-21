# Panduan AI (Tanya Berita)

Dokumen ini menjelaskan cara kerja **Tanya Berita AI** — asisten di dalam portal yang menjawab
pertanyaan pembaca, baik **fakta berita** maupun **cara memakai situs ini** (daftar akun, kirim
opini, dan seterusnya).

## Sumber kebenaran

| Sumber | Isi | Lokasi |
|---|---|---|
| Panduan pengguna | Cara daftar akun, masuk, kirim opini, jadi kontributor, like/komentar, menjelajah berita, halaman resmi, batasan | `resources/ai/panduan-konut-update.md` |
| Basis berita | Artikel berstatus `published` yang paling mirip dengan pertanyaan | `PostRepository::searchForChat()` |

File panduan dibaca setiap request (di-memo per proses) — **tidak** perlu `cache:clear` setelah
mengeditnya, cukup `php artisan view:clear` bila ada perubahan Blade.

## Cara kerja

1. `POST /tanya-berita` → `Frontend\AiChatController@ask`.
2. Validasi: `question` wajib, maksimal 300 karakter, rate limit `ai-chat` (10/menit + 100/hari per IP).
3. Kuota tamu: `services.gemini.guest_limit` (env `GEMINI_GUEST_LIMIT`, bawaan 2) per IP, berlaku 1 hari.
   Setelah habis → `403` + `requires_auth: true` → UI menampilkan ajakan Masuk/Daftar.
4. `AiChatService::answer()`:
   - Cari maksimal 8 artikel published paling mirip (keyword + skoring PHP, tanpa embedding).
   - Susun `systemInstruction` = aturan jawaban + **isi panduan pengguna**.
   - Panggil Gemini (`gemini-2.5-flash`, ada rantai model cadangan bila model utama non-2xx).
   - Balas `answer` (escaped di sisi klien) + `sources`.
5. **Sumber jawaban** (`sources`):
   - Pertanyaan **prosedural** (ada kata "cara/bagaimana/gimana/langkah" **dan** cocok dengan
     panduan) → tautan panduan (label kategori `Panduan`), misalnya `/daftar`, `/profil?opini=1`,
     `/kontak`. Article yang kebetulan mengandung kata yang sama tidak ditampilkan.
   - Pertanyaan **berita** → judul + URL artikel published (maks. 5).
   - Tidak ada artikel cocok → tautan panduan yang sesuai kata kunci; bila tidak ada yang cocok,
     `sources` kosong (jawaban tetap diberikan dari panduan).

## Aturan jawaban (dalam system prompt)

1. Pertanyaan **cara/prosedur** → dijawab dengan langkah bernomor dari panduan + halaman relevan.
2. Pertanyaan **fakta berita** → hanya memakai isi artikel yang diberikan; tidak mengarang angka,
   tanggal, nama tokoh, atau kutipan.
3. Artikel tidak relevan/kosong + pertanyaan faktual → jujur said "belum ada di Konut Update" dan
   menawarkan hal yang tersedia (panduan, kategori, kecamatan, kontak redaksi).
4. Tidak mengarang tautan — hanya tautan dari panduan atau URL artikel.
5. Bahasa Indonesia, ringkas, maksimal ±5 paragraf pendek.

## Menambah cakupan panduan

Tambahkan atau perbarui bagian di `resources/ai/panduan-konut-update.md`:

- Judul bagian memakai `##` (level 2) agar mudah dipindai model.
- Sebutkan **alamat halaman** persis seperti yang dipakai di aplikasi (`/daftar`, `/profil`,
  `/kirim-opini`, `/pedoman-media-siber`, ...). Model memakai alamat ini untuk memberi tautan.
- Sebutkan **batas angka** yang benar (mis. minimal 50 karakter untuk isi opini, maksimal 3 kategori,
  password minimal 8 karakter) supaya AI tidak mengarang.
- Bila menambah halaman baru, tambahkan juga tautannya di `App\Services\AiGuide::links()` agar
  muncul sebagai sumber jawaban.

## Konfigurasi

| Key config | Env | Bawaan | Fungsi |
|---|---|---|---|
| `services.gemini.enabled` | `GEMINI_ENABLED` | `true` | Menyorot widget Tanya Berita |
| `services.gemini.key` | `GEMINI_API_KEY` | — | Kunci API; **kosong ⇒ widget disembunyikan & endpoint 503** |
| `services.gemini.model` | `GEMINI_MODEL` | `gemini-2.5-flash` | Model utama |
| `services.gemini.fallback_models` | `GEMINI_FALLBACK_MODELS` | `gemini-3.5-flash,gemini-flash-latest,gemini-2.5-flash-lite` | Rantai cadangan bila model utama non-2xx |
| `services.gemini.guest_limit` | `GEMINI_GUEST_LIMIT` | `2` | Jatah pertanyaan gratis per IP per hari |
| `services.gemini.daily_limit` | `GEMINI_DAILY_LIMIT` | `1000` | Batas harian global (`0` = mati) |

Gemini dipanggil **server-side** — tidak ada perubahan CSP (`SecurityHeaders::buildCsp()` tetap
hanya mengizinkan `'self'`, Turnstile, dan open-meteo pada `connect-src`).

## Testing

- `tests/Feature/AiChatTest.php` — widget, sumber berita, **jawaban prosedural dari panduan**,
  injeksi panduan ke system instruction, model cadangan, rate limit, 503 saat dimatikan.
- `tests/Feature/AiGuideTest.php` — file panduan ada & mencakup alur publik, pemilihan tautan
  sumber sesuai kata kunci, label kategori `Panduan`.

Semua test bersifat offline (`Http::fake()`), tidak memanggil Gemini sungguhan.

## Batasan

- AI bisa salah. Untuk pertanyaan faktual, selalu arahkan pembaca ke tautan sumber.
- Panduan hanya memuat alur yang benar-benar ada di aplikasi — **jika ada fitur/aturan yang
  berubah, panduan wajib diperbarui**, kalau tidak AI akan menjawab versi lama.
- Tidak ada captcha pada endpoint ini; penyalahgunakan dibatasi rate limit per IP, batas 300
  karakter per pertanyaan, dan kuota harian global.
