<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChuyenMucBlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bai_viets')->truncate();
        DB::table('bai_viets')->insert([
            [
                'ten_chuyen_muc' => 'Tin Tức',
                'slug_chuyen_muc' => 'tin-tuc',
                'tinh_trang' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chuyen_muc' => 'Phim Sắp Chiếu',
                'slug_chuyen_muc' => 'phim-sap-chieu',
                'tinh_trang' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
