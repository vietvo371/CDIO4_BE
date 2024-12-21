<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChucVusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chuc_vus')->truncate();

        $chucVus = [
            [
                'ten_chuc_vu' => 'Quản Trị Website',
                'slug_chuc_vu' => 'quan-tri-website',
                'tinh_trang' => 1,
            ],
            [
                'ten_chuc_vu' => 'Quản trị Phim',
                'slug_chuc_vu' => 'quan-tri-phim',
                'tinh_trang' => 1,
            ],
            [
                'ten_chuc_vu' => 'Quản Trị Blog',
                'slug_chuc_vu' => 'quan-tri-blog',
                'tinh_trang' => 1,
            ],
        ];
        DB::table('chuc_vus')->insert($chucVus);
    }
}
