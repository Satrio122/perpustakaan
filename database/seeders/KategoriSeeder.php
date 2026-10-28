<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Teknologi',
            'Sains',
            'Sastra',
            'Sejarah',
            'Komputer',
        ];

        foreach ($data as $nama) {
            Kategori::firstOrCreate([
                'nama_kategori' => $nama,
            ]);
        }
    }
}
