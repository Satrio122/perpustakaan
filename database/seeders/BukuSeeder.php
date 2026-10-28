<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriId = Kategori::first()?->id ?? 1;

        Buku::firstOrCreate(
            ['isbn' => '9786230012345'],
            [
                'judul_buku' => 'Belajar Laravel untuk Pemula',
                'id_kategori' => $kategoriId,
                'pengarang' => 'Rafi Pratama',
                'penerbit' => 'Gramedia',
                'tahun_terbit' => 2024,
                'isbn' => '9786230012345',
                'stok' => 10,
                'dipinjam' => 0,
                'dibooking' => 0,
                'image' => 'cover-buku/book-defaultcover.jpg',
            ]
        );
    }
}
