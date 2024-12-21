<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoiVipsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('goi_vips')->truncate();
        $goiVips = [
            [
                'ten_goi' => 'Gói 1 Tháng',
                'slug_goi_vip' => 'goi-1-thang',
                'thoi_han' => 1, // Duration in months
                'tien_goc' => 25000, // Original price
                'tien_sale' => 20000, // Discounted price
                'tinh_trang' => 1, // Active status
            ],
            [
                'ten_goi' => 'Gói 3 Tháng',
                'slug_goi_vip' => 'goi-3-thang',
                'thoi_han' => 3, // Duration in months
                'tien_goc' => 75000, // Original price
                'tien_sale' => 60000, // Discounted price
                'tinh_trang' => 1, // Active status
            ],
            [
                'ten_goi' => 'Gói 6 Tháng',
                'slug_goi_vip' => 'goi-6-thang',
                'thoi_han' => 6, // Duration in months
                'tien_goc' => 150000, // Original price
                'tien_sale' => 120000, // Discounted price
                'tinh_trang' => 1, // Active status
            ],
            // [
            //     'ten_goi' => 'Gói 12 Tháng',
            //     'slug_goi_vip' => 'goi-12-thang',
            //     'thoi_han' => 12, // Duration in months
            //     'tien_goc' => 1800000, // Original price
            //     'tien_sale' => 1500000, // Discounted price
            //     'tinh_trang' => 1, // Active status
            // ],
        ];

        DB::table('goi_vips')->insert($goiVips);
    }
}
