<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Category::insert([
            [
                'name' => 'Elektronik',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Fashion Pria',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Fashion Wanita',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Kecantikan & Kesehatan',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Rumah & Taman',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Olahraga & Outdoor',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Makanan & Minuman',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Buku & Alat Tulis',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Otomotif',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Mainan & Hobi',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
