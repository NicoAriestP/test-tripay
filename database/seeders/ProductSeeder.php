<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Product::insert([
            [
                'category_id' => 1,
                'name' => 'Smartphone XYZ',
                'sku' => 'SPH-XYZ-001',
                'price' => 5000000,
                'reference' => 'Ref001',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 1,
                'name' => 'Laptop ABC',
                'sku' => 'LTP-ABC-002',
                'price' => 10000000,
                'reference' => 'Ref002',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 2,
                'name' => 'Kaos Polos',
                'sku' => 'KOS-POL-003',
                'price' => 75000,
                'reference' => 'Ref003',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 2,
                'name' => 'Celana Jeans',
                'sku' => 'CLN-JNS-004',
                'price' => 150000,
                'reference' => 'Ref004',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 4,
                'name' => 'Lipstik Merah',
                'sku' => 'LPS-MRH-005',
                'price' => 120000,
                'reference' => 'Ref005',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 4,
                'name' => 'Parfum Wanita',
                'sku' => 'PRF-WNT-006',
                'price' => 300000,
                'reference' => 'Ref006',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 5,
                'name' => 'Meja Kayu',
                'sku' => 'MJA-KYW-007',
                'price' => 800000,
                'reference' => 'Ref007',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 5,
                'name' => 'Kursi Lipat',
                'sku' => 'KRS-LPT-008',
                'price' => 200000,
                'reference' => 'Ref008',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 6,
                'name' => 'Sepeda Gunung',
                'sku' => 'SPD-GNT-009',
                'price' => 2500000,
                'reference' => 'Ref009',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => 6,
                'name' => 'Tenda Camping',
                'sku' => 'TND-CMP-010',
                'price' => 600000,
                'reference' => 'Ref010',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
