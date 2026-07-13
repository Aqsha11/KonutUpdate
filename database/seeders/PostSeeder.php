<?php

namespace Database\Seeders;

use App\Models\Post;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    private array $titles = [
        'Bupati Konawe Utara Resmikan Gedung Serbaguna di Lasolo',
        'Polres Konut Tangkap Pelaku Pencurian di Kecamatan Asera',
        'PT VDN Resmi Beroperasi Penuh di Kawasan Industri Konawe Utara',
        'DPRD Konut Sahkan APBD Perubahan Tahun 2026',
        'Kecelakaan Lalu Lintas di Jalur Trans Sulawesi Libatkan Dua Truk',
        'Pemerintah Konut Canangkan Program Desa Digital 2026',
        'Ribuan Warga Konut Hadiri Festival Budaya Konawe',
        'Pelaku Tambang Ilegal di Konut Diamankan Tim Gabungan',
        'SMA Negeri 1 Lasolo Raih Juara Umum Olimpiade Sains Tingkat Sultra',
        'Harga Komoditas Kakao di Konut Alami Kenaikan Signifikan',
        'Pemkab Konut Salurkan Bantuan Sosial untuk Korban Banjir',
        'Turnamen Sepak Bola Bupati Cup 2026 Resmi Digelar',
        'Polda Sultra Bongkar Jaringan Narkoba di Konawe Utara',
        'Universitas Halu Oleo Buka Kampus Cabang di Konut',
        'Danramil Lasolo Beri Penyuluhan Bela Negara ke Pelajar',
        'Kadis Pendidikan Konut Tinjau Pembangunan Sekolah di Wawolesea',
        'Pelaku Pembakaran Lahan di Konut Terancam Hukum 10 Tahun',
        'Pertumbuhan Ekonomi Konut Triwulan I Capai 5,8 Persen',
        'Jembatan Penghubung Dua Kecamatan di Konut Rampung Dibangun',
        'Satpol PP Konut Tertibkan Pedagang Kaki Lima di Pasar Lasolo',
        'BPBD Konut Kirim Bantuan Logistik ke Desa Terisolir',
        'Pj Bupati Konut Hadiri Peringatan HUT RI di Kendari',
        'Konut Raih Penghargaan Kabupaten Layak Anak dari KemenPPPA',
        'Pelajar Konut Ikuti Program Pertukaran Budaya ke Jepang',
        'Produksi Nikel Konut Capai Rekor Tertinggi pada Semester I',
        'Peringatan Maulid Nabi di Konut Berlangsung Khidmat',
        'Konut Jadi Tuan Rumah Pekan Olahraga Provinsi 2027',
        'Rehabilitasi Mangrove di Pesisir Konut Libatkan Masyarakat',
        'Baznas Konut Salurkan Zakat untuk Ribuan Mustahik',
        'DKPP Konut Intensifkan Pengawasan Hewan Ternak Jelang Idul Adha',
    ];

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

    private array $kecamatan = ['Lasolo', 'Asera', 'Langgikima', 'Oheo', 'Andowia', 'Wawolesea', 'Molawe', 'Landawe', 'Kulisusu', 'Tongauna'];

    private array $pejabat = ['Bupati Konawe Utara', 'Wakil Bupati Konut', 'Sekretaris Daerah Konut', 'Ketua DPRD Konut', 'Kapolres Konut', 'Dandim 1417 Konut', 'Kajari Konut'];

    private array $narasumber = ['Kadis Kominfo Konut', 'Kadis Pendidikan Konut', 'Kabag Humas Pemkab Konut', 'Kasi Humas Polres Konut', 'Camat setempat', 'Kepala Desa setempat'];

    private function kec(): string
    {
        return $this->kecamatan[array_rand($this->kecamatan)];
    }

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
                '{kecamatan}' => $this->kec(),
                '{pembukaan}' => fake()->randomElement([
                    'Pemerintah Kabupaten Konawe Utara menggelar kegiatan pembangunan infrastruktur di wilayah ini.',
                    'Warga masyarakat antusias mengikuti acara yang diselenggarakan oleh pemerintah daerah.',
                    'Suasana penuh kehangatan terasa dalam kegiatan yang berlangsung pagi tadi.',
                    'Sejumlah agenda penting dibahas dalam pertemuan yang digelar di Apertemuan Kantor Bupati.',
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
                    'beberapa saksi telah dimintai keteranganoleh penyidik.',
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
                '{lokasi}' => fake()->randomElement([
                    'Kecamatan Lasolo', 'Kecamatan Asera', 'Kecamatan Langgikima',
                    'Kecamatan Oheo', 'Kecamatan Andowia', 'Kecamatan Wawolesea',
                ]),
                '{peserta}' => fake()->randomElement([
                    'ratusan warga yang antusias',
                    'perwakilan dari 10 kecamatan se-Konut',
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

    public function run(): void
    {
        $faker = Faker::create('id_ID');

        foreach ($this->titles as $index => $title) {
            if ($index < 4) {
                $status = 'published';
                $isBreaking = in_array($index, [2]);
                $isFeatured = in_array($index, [0, 1]);
            } elseif ($index < 10) {
                $status = 'published';
                $isBreaking = in_array($index, [5, 7]);
                $isFeatured = in_array($index, [3, 5]);
            } elseif ($index < 27) {
                $status = 'published';
                $isBreaking = false;
                $isFeatured = in_array($index, [12, 15]);
            } else {
                $status = 'draft';
                $isBreaking = false;
                $isFeatured = false;
            }

            $body = $this->generateBody();
            $publishedAt = $faker->dateTimeBetween('-1 month', 'now');
            $createdAt = $publishedAt;
            $updatedAt = $publishedAt;

            $postData = [
                'user_id' => rand(1, 3),
                'category_id' => rand(1, 10),
                'title' => $title,
                'slug' => Str::slug($title),
                'excerpt' => $this->generateExcerpt($body),
                'body' => $body,
                'thumbnail' => null,
                'status' => $status,
                'is_breaking' => $isBreaking,
                'is_featured' => $isFeatured,
                'published_at' => $publishedAt,
                'views_count' => rand(50, 5000),
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];

            Post::create($postData);
        }
    }
}
