<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Tentang Kami',
                'slug' => 'tentang-kami',
                'content' => '<h2>Selamat Datang di Konut.Update</h2>
<p>Portal berita online yang menyajikan informasi terkini, akurat, dan terpercaya dari Konawe Utara (Konut), Sulawesi Tenggara.</p>
<hr />
<h3>Visi</h3>
<p>Menjadi portal berita terdepan dan terpercaya di Konawe Utara yang mampu memberikan informasi yang mendidik, mencerahkan, dan memberdayakan masyarakat.</p>
<h3>Misi</h3>
<ul>
<li>Menyajikan berita yang akurat, berimbang, dan terpercaya kepada masyarakat Konawe Utara dan sekitarnya.</li>
<li>Menjadi jembatan informasi antara pemerintah daerah, pelaku bisnis, dan masyarakat.</li>
<li>Mengedepankan jurnalisme yang bertanggung jawab dan berintegritas.</li>
<li>Memanfaatkan teknologi digital untuk menyebarkan informasi secara cepat dan luas.</li>
</ul>',
                'is_published' => true,
            ],
            [
                'title' => 'Kontak',
                'slug' => 'kontak',
                'content' => '<h2>Hubungi Kami</h2>
<p>Jika Anda memiliki pertanyaan, saran, atau ingin menghubungi redaksi, silakan gunakan formulir kontak di halaman ini atau hubungi kami melalui informasi di bawah ini.</p>
<p>Email: redaksi@konut.update</p>',
                'is_published' => true,
            ],
            [
                'title' => 'Pedoman Media Siber',
                'slug' => 'pedoman-media-siber',
                'content' => '<h2>Pedoman Media Siber</h2>
<p>Pedoman Media Siber ini berlaku untuk seluruh konten yang dipublikasikan melalui platform Konut.Update, termasuk namun tidak terbatas pada artikel berita, opini, foto, video, dan konten multimedia lainnya.</p>
<h3>1. Prinsip Dasar</h3>
<ul>
<li>Kami berkomitmen untuk menyajikan informasi yang akurat, berimbang, dan tidak memihak.</li>
<li>Setiap berita yang dipublikasikan telah melalui proses verifikasi dan editing yang ketat.</li>
<li>Kami menjunjung tinggi etika jurnalistik dan kode etik jurnalistik Indonesia.</li>
</ul>
<h3>2. Verifikasi dan Akurasi</h3>
<ul>
<li>Setiap informasi wajib diverifikasi minimal dari dua sumber yang terpercaya.</li>
<li>Informasi yang bersifat merugikan pihak lain harus melalui konfirmasi terlebih dahulu.</li>
</ul>
<h3>3. Hak Jawab dan Hak Koreksi</h3>
<ul>
<li>Setiap pihak yang dirugikan oleh pemberitaan berhak mengajukan hak jawab atau hak koreksi.</li>
<li>Redaksi wajib memuat hak jawab atau koreksi secara proporsional dan tidak ditunda-tunda.</li>
</ul>',
                'is_published' => true,
            ],
            [
                'title' => 'Kebijakan Privasi',
                'slug' => 'privacy-policy',
                'content' => '<h2>Kebijakan Privasi</h2>
<p>Kebijakan Privasi ini menjelaskan bagaimana Konut.Update mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan layanan kami.</p>
<h3>1. Informasi yang Kami Kumpulkan</h3>
<ul>
<li>Informasi yang Anda berikan: Nama, alamat email, dan informasi lainnya saat Anda mengisi formulir kontak.</li>
<li>Informasi otomatis: Alamat IP, jenis browser, halaman yang dikunjungi, waktu akses.</li>
<li>Cookie: Kami menggunakan cookie untuk meningkatkan pengalaman browsing Anda.</li>
</ul>
<h3>2. Penggunaan Informasi</h3>
<ul>
<li>Menyediakan dan memelihara layanan portal berita.</li>
<li>Mengirimkan newsletter dan update berita (dengan persetujuan Anda).</li>
<li>Menjawab pertanyaan dan menanggapi permintaan Anda.</li>
</ul>
<h3>3. Perlindungan Data</h3>
<p>Kami menerapkan langkah-langkah keamanan teknis dan organisasi yang tepat untuk melindungi informasi pribadi Anda.</p>',
                'is_published' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }
    }
}
