<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatans = [
            ['name' => 'Andowia', 'description' => null, 'sort_order' => 1],
            ['name' => 'Asera', 'description' => 'Pusat pemerintahan kabupaten', 'sort_order' => 2],
            ['name' => 'Landawe', 'description' => null, 'sort_order' => 3],
            ['name' => 'Langgikima', 'description' => null, 'sort_order' => 4],
            ['name' => 'Lasolo', 'description' => null, 'sort_order' => 5],
            ['name' => 'Lasolo Kepulauan', 'description' => null, 'sort_order' => 6],
            ['name' => 'Lembo', 'description' => null, 'sort_order' => 7],
            ['name' => 'Molawe', 'description' => null, 'sort_order' => 8],
            ['name' => 'Motui', 'description' => 'Kecamatan dengan wilayah terkecil', 'sort_order' => 9],
            ['name' => 'Oheo', 'description' => null, 'sort_order' => 10],
            ['name' => 'Sawa', 'description' => null, 'sort_order' => 11],
            ['name' => 'Wawolesea', 'description' => null, 'sort_order' => 12],
            ['name' => 'Wiwirano', 'description' => 'Kecamatan dengan wilayah terluas', 'sort_order' => 13],
        ];

        foreach ($kecamatans as $k) {
            Kecamatan::updateOrCreate(
                ['slug' => Str::slug($k['name'])],
                $k
            );
        }
    }
}
