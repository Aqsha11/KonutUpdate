<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Kriminal', 'slug' => 'kriminal', 'description' => 'Berita seputar kriminalitas dan hukum'],
            ['name' => 'Kecelakaan', 'slug' => 'kecelakaan', 'description' => 'Berita kecelakaan lalu lintas dan lainnya'],
            ['name' => 'Tambang', 'slug' => 'tambang', 'description' => 'Berita seputar pertambangan dan industri'],
            ['name' => 'Politik', 'slug' => 'politik', 'description' => 'Berita politik lokal dan nasional'],
            ['name' => 'Pemerintahan', 'slug' => 'pemerintahan', 'description' => 'Berita kegiatan pemerintahan'],
            ['name' => 'Ekonomi', 'slug' => 'ekonomi', 'description' => 'Berita ekonomi dan bisnis'],
            ['name' => 'Pendidikan', 'slug' => 'pendidikan', 'description' => 'Berita pendidikan dan akademik'],
            ['name' => 'Olahraga', 'slug' => 'olahraga', 'description' => 'Berita olahraga dan kesehatan'],
            ['name' => 'Event', 'slug' => 'event', 'description' => 'Berita acara dan kegiatan'],
            ['name' => 'Nasional', 'slug' => 'nasional', 'description' => 'Berita nasional Indonesia'],
        ];

        // firstOrCreate (bukan create): seeder ini dijalankan pada setiap build
        // Render, sementara tabel categories sudah berisi baris (dibuat lewat
        // admin). categories.slug punya unique index, jadi create() akan
        // melempar QueryException dan menghentikan build sebelum storage:link.
        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name'], 'description' => $category['description']],
            );
        }
    }
}
