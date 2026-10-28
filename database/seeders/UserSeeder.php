<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama' => 'Admin',
                'alamat' => 'Jakarta',
                'image' => 'profile-pic/default.jpg',
                'password' => Hash::make('123'),
                'role_id' => 1,
                'is_active' => 1,
            ]
        );
    }
}
