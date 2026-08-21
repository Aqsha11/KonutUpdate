<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Post;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    private array $templates = [
        [
            'Kecamatan {kecamatan}, Konawe Utara — {pembukaan} Kegiatan ini dihadiri oleh {pejabat} dan sejumlah {tamu}. Dalam sambutannya, {subjek} menyampaikan bahwa {pernyataan}.',
            '{pengembangan} {harapan}. Program ini merupakan bagian dari {program} yang dicanangkan pemerintah daerah. Masyarakat diharapkan dapat {manfaat}.',
            'Sementara itu, {narasumber} menambahkan, "{kutipan}" Langkah ini dinilai positif oleh {pemantau} yang menyebutkan bahwa {dampak}.',
            'Ke depannya, pemerintah daerah berkomitmen untuk {komitmen}. Hal ini sejalan dengan visi misi Kabupaten Konawe Utara yang {visi}. Pihak terkait akan terus berkoordinasi untuk memastikan {kelanjutan}.',
        ],
        [
            '{kecamatan}, Konawe Utara — Telah terjadi {peristiwa} di wilayah Konawe Utara pada {waktu}. Peristiwa ini diketahui oleh {saksi} yang melaporkannya ke pihak berwenang.',
            'Kapolres Konut AKBP {kapolres} membenarkan kejadian tersebut. "Ya benar, kami telah menerima laporan dan saat ini sedang melakukan penyelidikan lebih lanjut," ujarnya saat dikonfirmasi.',
            'Berdasarkan keterangan di lapangan, {rincian}. Kerugian akibat kejadian ini ditaksir mencapai {nominal}. Pihak kepolisian mengimbau {imbauan}.',
            'Penanganan kasus ini akan dilakukan secara profesional dan transparan. Masyarakat diminta untuk tetap tenang dan tidak terprovokasi dengan informasi yang belum jelas kebenarannya.',
        ],
        [
            'Pemerintah Kabupaten Konawe Utara kembali menunjukkan komitmennya dalam {bidang}. Hal ini dibuktikan dengan {kegiatan} yang digelar di {tempat} pada hari ini.',
            '{pejabat_daerah} menyampaikan bahwa kegiatan ini bertujuan untuk {tujuan}. "Kami berharap ini dapat memberikan manfaat yang sebesar-besarnya bagi masyarakat," ungkapnya.',
            'Acara yang berlangsung di {lokasi} ini dihadiri oleh {peserta}. Mereka mengapresiasi langkah pemerintah daerah dalam {apresiasi}.',
            '{penutup}. Pemerintah daerah berencana melanjutkan program serupa di kecamatan-kecamatan lain di Konawe Utara dalam waktu dekat.',
        ],
    ];

    private array $kecamatanNames = ['Lasolo', 'Asera', 'Langgikima', 'Oheo', 'Andowia', 'Wawolesea', 'Molawe', 'Landawe', 'Lasolo Kepulauan', 'Lembo', 'Motui', 'Sawa', 'Wiwirano'];

    private array $pejabat = ['Bupati Konawe Utara', 'Wakil Bupati Konut', 'Sekretaris Daerah Konut', 'Ketua DPRD Konut', 'Kapolres Konut', 'Dandim 1417 Konut', 'Kajari Konut'];

    private array $narasumber = ['Kadis Kominfo Konut', 'Kadis Pendidikan Konut', 'Kabag Humas Pemkab Konut', 'Kasi Humas Polres Konut', 'Camat setempat', 'Kepala Desa setempat'];

    private function randomTemplate(): array
    {
        return $this->templates[array_rand($this->templates)];
    }

    private function generateBody(): string
    {
        $paragraphs = $this->randomTemplate();
        $body = '';

        foreach ($paragraphs as $para) {
            $replacements = [
                '{kecamatan}' => $this->kecamatanNames[array_rand($this->kecamatanNames)],
                '{pembukaan}' => fake()->randomElement([
                    'Pemerintah Kabupaten Konawe Utara menggelar kegiatan pembangunan infrastruktur di wilayah ini.',
                    'Warga masyarakat antusias mengikuti acara yang diselenggarakan oleh pemerintah daerah.',
                    'Suasana penuh kehangatan terasa dalam kegiatan yang berlangsung pagi tadi.',
                    'Sejumlah agenda penting dibahas dalam pertemuan di Aula Kantor Bupati.',
                ]),
                '{pejabat}' => $this->pejabat[array_rand($this->pejabat)],
                '{tamu}' => fake()->randomElement(['tokoh masyarakat', 'perangkat desa', 'perwakilan kecamatan', 'stakeholder terkait', 'unsur Forkopimda']),
                '{subjek}' => $this->pejabat[array_rand($this->pejabat)],
                '{pernyataan}' => fake()->randomElement([
                    'program ini akan berdampak positif bagi perkembangan daerah.',
                    'pembangunan harus dimulai dari daerah-daerah terpencil.',
                    'sinergi antara pemerintah dan masyarakat sangat diperlukan.',
                    'komitmen pembangunan di Konut terus ditingkatkan setiap tahun.',
                ]),
                '{pengembangan}' => fake()->randomElement([
                    'Pengembangan kawasan ini akan dilaksanakan secara bertahap.',
                    'Pembangunan infrastruktur terus dikebut oleh kontraktor pelaksana.',
                    'Masyarakat menyambut baik program pembangunan ini.',
                    'Koordinasi lintas sektor terus dilakukan demi kelancaran program.',
                ]),
                '{harapan}' => fake()->randomElement([
                    'Diharapkan dapat selesai tepat waktu sesuai kontrak kerja.',
                    'Semua pihak berharap program ini berjalan lancar.',
                    'Target penyelesaian diharapkan sesuai jadwal yang ditentukan.',
                ]),
                '{program}' => fake()->randomElement([
                    'rencana pembangunan jangka menengah daerah (RPJMD)',
                    'program prioritas nasional',
                    'agenda pembangunan Kabupaten Konawe Utara',
                ]),
                '{manfaat}' => fake()->randomElement([
                    'menikmati hasil pembangunan secara merata',
                    'berpartisipasi aktif dalam pembangunan daerah',
                    'memperoleh akses yang lebih baik terhadap layanan publik',
                ]),
                '{narasumber}' => $this->narasumber[array_rand($this->narasumber)],
                '{kutipan}' => fake()->randomElement([
                    'Ini adalah bentuk komitmen kami dalam melayani masyarakat Konawe Utara.',
                    'Kami akan terus berupaya memberikan pelayanan terbaik kepada masyarakat.',
                    'Mari kita bersama-sama membangun Konawe Utara yang lebih baik.',
                    'Kami berharap dukungan dari semua pihak untuk kelancaran program ini.',
                ]),
                '{pemantau}' => fake()->randomElement(['pengamat politik', 'akademisi', 'tokoh pemuda', 'budayawan setempat']),
                '{dampak}' => fake()->randomElement([
                    'dampaknya akan terasa langsung oleh masyarakat luas.',
                    'ini bisa menjadi contoh bagi daerah lain di Sulawesi Tenggara.',
                    'akan mendorong pertumbuhan ekonomi di wilayah timur Konut.',
                ]),
                '{komitmen}' => fake()->randomElement([
                    'mempercepat pembangunan infrastruktur dasar.',
                    'meningkatkan kualitas sumber daya manusia di daerah.',
                    'mendorong investasi yang ramah lingkungan dan berkelanjutan.',
                ]),
                '{visi}' => fake()->randomElement([
                    'berkomitmen pada pembangunan yang merata dan berkeadilan',
                    'mengedepankan kepentingan rakyat dalam setiap kebijakan',
                ]),
                '{kelanjutan}' => fake()->randomElement([
                    'program ini berjalan sesuai dengan target yang ditetapkan.',
                    'semua tahapan kegiatan dapat diselesaikan tepat waktu.',
                ]),
                '{peristiwa}' => fake()->randomElement([
                    'sebuah insiden yang mengguncang warga',
                    'peristiwa yang mengejutkan masyarakat',
                    'kejadian yang menarik perhatian publik',
                ]),
                '{waktu}' => fake()->randomElement([
                    'pagi hari sekitar pukul 09.00 WITA',
                    'siang hari saat warga sedang beristirahat',
                    'dini hari saat sebagian besar warga masih terlelap',
                    'sore hari menjelang magrib',
                ]),
                '{saksi}' => fake()->randomElement(['warga setempat', 'seorang saksi mata', 'petugas keamanan', 'karyawan yang kebetulan melintas']),
                '{kapolres}' => fake()->randomElement(['Arif Budiman', 'Bambang Suprapto', 'Dwi Jatmiko', 'Hendra Gunawan', 'Rudi Setiawan']),
                '{rincian}' => fake()->randomElement([
                    'kronologi kejadian masih dalam penyelidikan pihak berwajib.',
                    'barang bukti telah diamankan oleh petugas kepolisian.',
                    'beberapa saksi telah dimintai keterangan oleh penyidik.',
                ]),
                '{nominal}' => fake()->randomElement(['puluhan juta rupiah', 'ratusan juta rupiah', 'sekitar Rp 50 juta', 'kurang lebih Rp 100 juta']),
                '{imbauan}' => fake()->randomElement([
                    'agar selalu waspada dan menjaga keamanan lingkungan masing-masing.',
                    'untuk melapor jika melihat hal-hal mencurigakan di lingkungan sekitar.',
                    'agar tidak main hakim sendiri dan menyerahkan sepenuhnya pada proses hukum.',
                ]),
                '{bidang}' => fake()->randomElement(['pembangunan infrastruktur', 'peningkatan kualitas pendidikan', 'pemberdayaan ekonomi masyarakat', 'pelestarian budaya daerah']),
                '{kegiatan}' => fake()->randomElement([
                    'program pembangunan jalan poros kecamatan',
                    'sosialisasi pendidikan gratis untuk warga kurang mampu',
                    'pelatihan kewirausahaan bagi pemuda dan ibu rumah tangga',
                    'festival seni dan budaya Konawe',
                ]),
                '{tempat}' => fake()->randomElement([
                    'halaman Kantor Bupati Konawe Utara',
                    'Aula Pertemuan Kecamatan Lasolo',
                    'Lapangan Serbaguna Desa Wawolesea',
                ]),
                '{pejabat_daerah}' => $this->pejabat[array_rand($this->pejabat)],
                '{tujuan}' => fake()->randomElement([
                    'meningkatkan kesejahteraan masyarakat secara berkelanjutan',
                    'mempercepat pembangunan di wilayah pelosok',
                    'memberikan pelayanan publik yang lebih baik',
                    'mengembangkan potensi lokal yang ada di daerah',
                ]),
                '{lokasi}' => fake()->randomElement(array_map(fn ($k) => 'Kecamatan '.$k, array_slice($this->kecamatanNames, 0, 6))),
                '{peserta}' => fake()->randomElement([
                    'ratusan warga yang antusias',
                    'perwakilan dari 13 kecamatan se-Konut',
                    'para tokoh agama dan tokoh masyarakat',
                ]),
                '{apresiasi}' => fake()->randomElement([
                    'perhatiannya terhadap pembangunan di daerah tersebut.',
                    'program-program yang berpihak pada rakyat kecil.',
                    'upaya pemerintah dalam melestarikan budaya lokal.',
                ]),
                '{penutup}' => fake()->randomElement([
                    'Kegiatan berlangsung aman, tertib, dan penuh kekeluargaan.',
                    'Acara diakhiri dengan doa bersama yang dipimpin oleh tokoh agama setempat.',
                    'Suasana haru dan kebersamaan mewarnai akhir acara tersebut.',
                ]),
            ];

            $body .= '<p>'.str_replace(array_keys($replacements), array_values($replacements), $para).'</p>';
            $body .= "\n\n";
        }

        return trim($body);
    }

    private function generateExcerpt(string $body): string
    {
        $plainText = strip_tags($body);
        $words = explode(' ', $plainText);
        $excerptWords = array_slice($words, 0, 40);
        $excerpt = implode(' ', $excerptWords);

        if (count($words) > 40) {
            $excerpt .= '...';
        }

        return $excerpt;
    }

    /**
     * Spesifikasi post, urut dari yang PALING BARU.
     * Flag: H = headline, B = breaking, F = featured, D = draft.
     */
    private function specs(): array
    {
        return [
            // ── HEADLINE + BREAKING (jam-jam terakhir) ──
            ['Dua Truk Tabrakan di Jalur Trans Sulawesi, Arus Lasolo-Oheo Lumpuh Total', 'kecelakaan', 'HB'],
            ['Banjir Bandang Rendam Ratusan Rumah di Andowia, Warga Mengungsi ke Kantor Camat', 'pemerintahan', 'H'],
            ['Bupati Konut Resmikan Jembatan Penghubung Asera-Lasolo Senilai Rp87 Miliar', 'pemerintahan', 'HB'],
            ['PT VDN Umumkan Ekspansi Pabrik Pengolahan Nikel, Siap Serap 2.000 Tenaga Kerja', 'tambang', 'H'],
            ['Polres Konut Bongkar Gudang Pengepul BBM Ilegal di Langgikima', 'kriminal', 'HB'],
            ['Konut Siap Gelar Porprov Sultra 2027, Renovasi Stadion Andi Jemma Dimulai', 'olahraga', 'H'],

            // ── BREAKING non-headline ──
            ['Gempa M5,1 Guncang Perairan Utara Konawe Utara, Tidak Berpotensi Tsunami', 'nasional', 'B'],

            // ── KABAR TERKINI (feed jam & hari ini) ──
            ['Truk Muat Sawit Terguling di Km 32 Jalur Trans, Satu Sopir Terluka', 'kecelakaan', ''],
            ['Satgas Gabungan Amankan Tambang Ilegal di Wilayah Oheo', 'kriminal', ''],
            ['Pemkab Konut Canangkan Program Desa Digital 2026 untuk 13 Kecamatan', 'pemerintahan', ''],
            ['Harga Kakao di Konut Naik 12 Persen, Petani Antusias Musim Panen Raya', 'ekonomi', ''],
            ['SMAN 1 Lasolo Raih Juara Umum Olimpiade Sains Tingkat Sultra', 'pendidikan', ''],
            ['Timnas Sepak Bola Bupati Cup 2026 Lolos Babak Semifinal Hari Ini', 'olahraga', ''],
            ['BPBD Konut Kirim Bantuan Logistik ke Desa Terisolir Pasca Banjir', 'pemerintahan', ''],
            ['Polisi Tetapkan Tersangka Baru Kasus Pembakaran Lahan di Wiwirano', 'kriminal', ''],
            ['DPRD Konut Sahkan APBD Perubahan 2026, Fokus pada Infrastruktur Dasar', 'politik', ''],
            ['Ribuan Warga Padati Festival Budaya Konawe 2026 di Andowia', 'event', ''],
            ['Universitas Halu Oleo Resmi Buka Kampus Cabang di Konawe Utara', 'pendidikan', ''],
            ['Produksi Nikel Konut Capai Rekor Tertinggi pada Semester I 2026', 'tambang', ''],
            ['Jalan Poros Wawolesea-Molawe Tahap Akhir, Ditargetkan Rampung Bulan Depan', 'pemerintahan', ''],
            ['Pertumbuhan Ekonomi Konut Triwulan II Tembus 5,8 Persen', 'ekonomi', ''],
            ['Polda Sultra Bongkar Jaringan Narkoba Lintas Kabupaten di Konut', 'kriminal', ''],
            ['Pelajar Konut Ikuti Program Pertukaran Budaya Pelajar ke Jepang', 'pendidikan', ''],
            ['Konut Raih Penghargaan Kabupaten Layak Anak Tingkat Provinsi', 'pemerintahan', ''],
            ['Danramil Lasolo Beri Penyuluhan Bela Negara kepada Pelajar', 'pemerintahan', ''],

            // ── VIDEO ──
            ['Video: Suasana Meriah Festival Budaya Konawe 2026', 'event', ''],
            ['Video: Progres Pembangunan Jalan Poros Wawolesea-Molawe', 'pemerintahan', ''],
            ['Video: Keseruan Turnamen Sepak Bola Bupati Cup 2026', 'olahraga', ''],
            ['Video: Panen Raya Kakao Petani Kecamatan Lasolo', 'ekonomi', ''],
            ['Video: Latihan Bersama Polres Konut dan Masyarakat', 'kriminal', ''],

            // ── KONTEN PILIHAN / FEATURED ──
            ['Pasar Modern Andowia Mulai Beroperasi, Ratusan Pedagang Pindah Hari Ini', 'ekonomi', 'F'],
            ['Sekolah Adat Wawolesea Lestarikan Bahasa Tolaki Lewat Kurikulum Lokal', 'pendidikan', 'F'],
            ['UMKM Konut Go Digital Setelah Ikut Pelatihan Marketplace Pemkab', 'ekonomi', 'F'],
            ['Wisata Pantai Molawe Ditargetkan Sedot 50 Ribu Wisatawan Tahun Ini', 'event', 'F'],
            ['Peternak Konut Terima Bantuan Bibit Sapi dari Pemda Tahap Ketiga', 'pemerintahan', 'F'],
            ['Rehabilitasi Mangrove di Pesisir Lasolo Kepulauan Libatkan Nelayan', 'nasional', 'F'],

            // ── OPINI ──
            ['Opini: Membangun Konawe Utara Harus Dimulai dari Desa', 'pemerintahan', 'O'],
            ['Opini: Tantangan Industri Nikel yang Ramah Lingkungan di Konut', 'tambang', 'O'],
            ['Opini: Pendidikan Berkualitas adalah Hak Setiap Anak Konut', 'pendidikan', 'O'],
            ['Opini: Generasi Muda Konut dan Peluang Ekonomi Digital', 'ekonomi', 'O'],
            ['Opini: Jaga Hutan Konawe Utara demi Masa Depan Bersama', 'nasional', 'O'],

            // ── REGULER (hari-hari sebelumnya) ──
            ['Baznas Konut Salurkan Zakat untuk Ribuan Mustahik di 13 Kecamatan', 'pemerintahan', ''],
            ['DKPP Konut Intensifkan Vaksinasi Hewan Ternak Jelang Idul Adha', 'pemerintahan', ''],
            ['Peringatan Maulid Nabi di Konut Berlangsung Khidmat dan Meriah', 'event', ''],
            ['Konut Jadi Tuan Rumah Pekan Olahraga Provinsi Sultra 2027', 'olahraga', ''],
            ['Pj Bupati Konut Hadiri Upacara Peringatan HUT RI ke-81', 'pemerintahan', ''],
            ['Satpol PP Konut Tertibkan Pedagang Kaki Lima di Pasar Lasolo', 'pemerintahan', ''],
            ['Kadis Pendidikan Konut Tinjau Pembangunan Ruang Kelas di Sawa', 'pendidikan', ''],
            ['Kecelakaan Motor vs Mobil di Langgikima, Dua Orang Alami Luka Ringan', 'kecelakaan', ''],
            ['Polres Konut Ringkus Penadah Kendaraan Bermotor di Asera', 'kriminal', ''],
            ['Program KB Lingkar Desa Diperluas ke Wilayah Motui dan Lembo', 'pemerintahan', ''],
            ['Harga Beras Lokal Stabil Menjelang Panen Raya di Konut', 'ekonomi', ''],
            ['Turnamen Voli Antar-Kecamatan Resmi Dibuka di Lapangan Wiwirano', 'olahraga', ''],
            ['Puskesmas Oheo Tambah Tenaga Medis untuk Wilayah Perbatasan', 'pemerintahan', ''],
            ['Belanja Online Warga Konut Naik, Dinas Kopirdagin Dorong Koperasi Digital', 'ekonomi', ''],
            ['Kebakaran Warung di Pasar Langgikima Padam dalam 30 Menit', 'kriminal', ''],
            ['Beasiswa Anak Petani Konut Dibuka untuk 200 Kuota Mahasiswa', 'pendidikan', ''],
            ['Jembatan Gantungan Penghubung Dusun di Landawe Mulai Direnovasi', 'pemerintahan', ''],
            ['Kasus Pencurian Ternak di Motui Terungkap Berkat CCTV Warga', 'kriminal', ''],
            ['Festival Kuliner Sagu Lasolo Kepulauan Siap Digelar Akhir Bulan', 'event', ''],
            ['Tim Bulu Tangkis Konut Persiapkan Kejurnas di Kendari', 'olahraga', ''],
            ['Air Bersih Mengalir ke 500 KK di Wiwirano Lewat Program Pamsimas', 'pemerintahan', ''],
            ['Investor Cina Tinjau Kawasan Industri Nikel di Konut Timur', 'tambang', ''],
            ['Guru Honorer Konut Ikuti Uji Kompetensi Nasional Tahap II', 'pendidikan', ''],
            ['Operasi Zebra Hari Santri, Belasan Pengendali Motor Tanpa Helm Ditilang', 'kriminal', ''],

            // ── REGULER TAMBAHAN (biar tiap kategori & kecamatan berisi) ──
            ['Pemkab Konut Realokasi Dana Desa untuk Pembangunan Jalan Antar-Dusun', 'pemerintahan', ''],
            ['Dua Pemuda Ditangkap Gara-Gara Video Aksi Balapan Liar di Andowia', 'kriminal', ''],
            ['Angkot Rute Konut-Kendari Mulai Terapkan Tarif Elektronik', 'ekonomi', ''],
            ['Lomba Desa Tingkat Kabupaten Dimenangkan Desa Wiwirano', 'pemerintahan', ''],
            ['Siswa Sawa Raih Medali Emas Kompetisi Matematika Sulawesi', 'pendidikan', ''],
            ['Kecelakaan Tandem di Sirkuit Molawe, Satu Pebalap Cedera', 'kecelakaan', ''],
            ['Rapat Koordinasi Pilpres Damai Digelar Pemkab Konut', 'politik', ''],
            ['Ekspor Kakao Konut Tembus 4 Ribu Ton pada Semester Pertama', 'ekonomi', ''],
            ['Polsek Asera Gelar Sosialisasi Antipickpocket di Pasar Tradisional', 'kriminal', ''],
            ['Pantai Oheo Jadi Venue Kejuaraan Surfing Junior Nasional', 'olahraga', ''],
            ['Banjir Rob Genangi Pesisir Lasolo Kepulauan Selama Dua Hari', 'nasional', ''],
            ['Penerimaan CPNS Formasi Konut Naik Jadi 150 Orang', 'politik', ''],
            ['Galeri Seni Pertama di Andowia Resmi Dibuka untuk Umum', 'event', ''],
            ['Beasiswa Prestasi Pemkab Konut Untuk 80 Pelajar SMA', 'pendidikan', ''],
            ['Truk Tangki Solar Tercecer di Jalur Trans, Lalu Lintas Diatur Satu Arah', 'kecelakaan', ''],
            ['Harga Gabah Petani Motui Naik Seiring Musim Panen Padi', 'ekonomi', ''],
            ['Kasus Penyelundupan Kayu Ilegal di Perbatasan Lembo Terungkap', 'kriminal', ''],
            ['Turnamen Bola Voli Pantai Sawa Sumbang Dua Medali bagi Konut', 'olahraga', ''],
            ['BMCG Peringatkan Cuaca Ekstrem Potensi Hujan Lebat di Konut', 'nasional', ''],
            ['Festival Tari Tolaki Kembali Digelar di Lapangan Andowia', 'event', ''],
            ['Perpustakaan Keliling Jangkau Sekolah-Sekolah Terpencil Landawe', 'pendidikan', ''],
            ['Tabrakan Kereta Tambang dan Truk di Kawasan Industri, Tiga Korban', 'kecelakaan', ''],
            ['Sengketa Lahan Sawit di Asera Masuki Tahap Mediasi', 'politik', ''],
            ['UMKM Tahu Tempe Langgikima Tembus Pasar Supermarket Kendari', 'ekonomi', ''],
            ['Posyandu Desa Lembo Raih Predikat Terbaik Tingkat Kabupaten', 'pemerintahan', ''],
            ['Klub Sepakbola Konut Utama Rekrut Tiga Pemain Muda Lokal', 'olahraga', ''],
            ['Longsor Kecil Tutup Jalur Wiwirano-Tinukoren, Alat Berat Diterjunkan', 'kecelakaan', ''],
            ['Anggota DPRD Konut Lakukan Kunjungan Kerja ke Kecamatan Sawa', 'politik', ''],
            ['Pameran Kerajinan Kayu Ulin Tarik Pengunjung di Molawe', 'event', ''],
            ['Program Literasi Digital Sasar 2.000 Ibu Rumah Tangga di Konut', 'pendidikan', ''],
            ['Penambangan Ilegal Pasir Sungai di Oheo Digelar Operasi Gabungan', 'tambang', ''],
            ['Konawe Utara Kirim 30 Ton Bantuan Logistik untuk Daerah Terdampak', 'nasional', ''],
            ['Investasi Data Center Pertama Konut Ditargetkan Beroperasi 2027', 'ekonomi', ''],
            ['Reklamasi Tambak Garam Sawa Perkuat Ketahanan Produksi Garam', 'ekonomi', ''],
            ['Pemilihan Ketua TNI-Polri Cabang Konut Berlangsung Kondusif', 'politik', ''],
            ['Wisata Mangrove Wawolesea Masuk 10 Besar Anugerah Wisata Sultra', 'event', ''],

            // ── DRAFT (tidak tampil) ──
            ['Naskah Berita: Rencana Pembangunan Bandara Konut Masih Dikaji', 'pemerintahan', 'D'],
            ['Naskah Berita: BPS Sultra Rilis Data Kemiskinan Kabupaten', 'ekonomi', 'D'],
        ];
    }

    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $catIds = Category::pluck('id', 'slug');
        $kecIds = Kecamatan::orderBy('name')->pluck('id')->values()->all();
        $userIds = User::pluck('id')->all() ?: [1];

        if ($catIds->isEmpty()) {
            $this->command?->warn('CategorySeeder harus dijalankan lebih dulu.');

            return;
        }

        // Umur eksplisit per blok (menit): headline menit-menit terakhir,
        // feed terkini jam-jam ini, video/featured/opini beberapa hari,
        // reguler mingguan. Draft tanpa tanggal.
        $ageFor = fn (int $i): int => match (true) {
            $i < 7 => 20 + $i * 22,                       // ±20 mnt - 2 jam
            $i < 20 => 200 + ($i - 7) * 95,               // ±3 jam - 22 jam
            $i < 36 => 2900 + ($i - 20) * 160,            // ±2 hari - 3,7 hari
            default => min(9000 + ($i - 36) * 700, 42000) // ±6 hari - ±29 hari
        };

        foreach ($this->specs() as $index => [$title, $categorySlug, $flags]) {
            $isHeadline = str_contains($flags, 'H');
            $isBreaking = str_contains($flags, 'B');
            $isFeatured = str_contains($flags, 'F');
            $isDraft = str_contains($flags, 'D');

            $type = str_starts_with($title, 'Video:') ? 'video' : (str_starts_with($title, 'Opini:') ? 'opini' : 'article');

            $publishedAt = $isDraft ? null : now()->subMinutes($ageFor($index));

            $viewsBoost = ($isFeatured || $isBreaking || $index < 10) ? rand(4000, 22000) : rand(150, 6000);

            $primaryCatId = $catIds[$categorySlug] ?? $catIds->first();

            $post = Post::create([
                'user_id' => $userIds[array_rand($userIds)],
                'category_id' => $primaryCatId,
                'kecamatan_id' => $kecIds !== [] ? $kecIds[$index % count($kecIds)] : null,
                'title' => $title,
                'slug' => Str::slug($title),
                'excerpt' => $this->generateExcerpt($body = $this->generateBody()),
                'body' => $body,
                'thumbnail' => null,
                'type' => $type,
                'video_path' => $type === 'video'
                    ? sprintf('https://www.tiktok.com/@konutupdate/video/%d', 7400000000000000000 + $index)
                    : null,
                'status' => $isDraft ? 'draft' : 'published',
                'is_breaking' => $isBreaking,
                'is_featured' => $isFeatured,
                'is_headline' => $isHeadline,
                'breaking_expires_at' => $isBreaking ? now()->addDays(3) : null,
                'headline_expires_at' => $isHeadline ? now()->addDays(7) : null,
                'published_at' => $publishedAt,
                'views_count' => $viewsBoost,
                'created_at' => $publishedAt ?? now(),
                'updated_at' => $publishedAt ?? now(),
            ]);

            // Relasi many-to-many kategori (pivot post_categories) — inilah
            // yang dipakai menu nav & section kategori via whereHas('allPosts').
            $attach = [$primaryCatId];
            if (! $isDraft && rand(1, 100) <= 25) {
                $others = $catIds->reject(fn ($id, $slug) => $slug === $categorySlug);
                if ($others->isNotEmpty()) {
                    $attach[] = $others->random();
                }
            }
            $post->categories()->sync($attach);
        }
    }
}
