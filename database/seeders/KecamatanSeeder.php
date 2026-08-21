<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        // Deskripsi unik per kecamatan → memperkuat halaman hub untuk
        // keyword "berita kecamatan X" (SEO lokal Konawe Utara).
        $kecamatans = [
            ['name' => 'Andowia', 'description' => 'Kecamatan Andowia adalah wilayah pesisir di Kabupaten Konawe Utara yang aktivitas masyarakatnya bertumpu pada perikanan tangkap, kelautan, dan perdagangan antardesa di pesisir timur Sulawesi Tenggara.', 'sort_order' => 1],
            ['name' => 'Asera', 'description' => 'Asera merupakan pusat pemerintahan Kabupaten Konawe Utara tempat berkumpulnya aktivitas administrasi, pendidikan, kesehatan, dan ekonomi utama, termasuk kawasan Wanggudu sebagai lokasi instansi daerah.', 'sort_order' => 2],
            ['name' => 'Landawe', 'description' => 'Kecamatan Landawe adalah wilayah pesisir Konawe Utara dengan potensi perikanan tangkap, budidaya kelapa, serta jalur lintas pesisir yang menghubungkan desa-desa pesisir timur.', 'sort_order' => 3],
            ['name' => 'Langgikima', 'description' => 'Langgikima dikenal sebagai kawasan permukiman transmigrasi dan pusat aktivitas pertambangan di Konawe Utara yang dilintasi jalur lintas timur Sulawesi.', 'sort_order' => 4],
            ['name' => 'Lasolo', 'description' => 'Kecamatan Lasolo berada di pesisir Teluk Lasolo, Kabupaten Konawe Utara, dengan lanskap teluk sebagai urat nadi kehidupan nelayan, perikanan, dan pertanian masyarakatnya.', 'sort_order' => 5],
            ['name' => 'Lasolo Kepulauan', 'description' => 'Lasolo Kepulauan mencakup rangkaian pulau-pulau kecil di Teluk Lasolo dengan kehidupan masyarakat bahari yang khas serta potensi wisata bahari Konawe Utara.', 'sort_order' => 6],
            ['name' => 'Lembo', 'description' => 'Kecamatan Lembo adalah wilayah pesisir Konawe Utara dengan potensi perikanan, perkebunan rakyat, dan pertanian yang menjadi sumber penghidupan utama warganya.', 'sort_order' => 7],
            ['name' => 'Molawe', 'description' => 'Molawe memiliki Pelabuhan Perikanan yang menjadi pintu gerbang ekonomi maritim Kabupaten Konawe Utara, mendukung aktivitas nelayan dan distribusi hasil laut.', 'sort_order' => 8],
            ['name' => 'Motui', 'description' => 'Motui adalah kecamatan dengan wilayah terkecil di Konawe Utara yang kehidupan masyarakatnya erat dengan laut, perahu nelayan tradisional, dan hasil perikanan.', 'sort_order' => 9],
            ['name' => 'Oheo', 'description' => 'Oheo merupakan kecamatan di Konawe Utara dengan lahan pertanian, perkebunan, dan deretan desa berkembang di jalur penghubung wilayah pedalaman kabupaten.', 'sort_order' => 10],
            ['name' => 'Sawa', 'description' => 'Sawa adalah kecamatan pedalaman Konawe Utara dengan sentra pertanian dan perkebunan masyarakat serta desa-desa agraris yang terus berkembang.', 'sort_order' => 11],
            ['name' => 'Wawolesea', 'description' => 'Wawolesea dikenal dengan pantai berpasir putih dan air panas alami di pesisir timur Konawe Utara yang menjadi destinasi wisata alam andalan kabupaten.', 'sort_order' => 12],
            ['name' => 'Wiwirano', 'description' => 'Wiwirano adalah kecamatan dengan wilayah terluas di Konawe Utara, dominan hutan dan perkebunan dengan desa-desa yang tersebar dari pedalaman hingga pesisir.', 'sort_order' => 13],
        ];

        foreach ($kecamatans as $k) {
            Kecamatan::updateOrCreate(
                ['slug' => Str::slug($k['name'])],
                $k
            );
        }
    }
}
