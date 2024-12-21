<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoaiPhimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('loai_phims')->truncate();

        $loai_phims = [
            'Phim Lẻ',
            'Phim Bộ',
            'Phim Hoạt Hình'
        ];
        $loaiPhimsData = [];

        foreach ($loai_phims as $loai_phim) {
            $loaiPhimsData[] = [
                'ten_loai_phim' => $loai_phim,
                'slug_loai_phim' => Str::slug($loai_phim, '-'),
                'tinh_trang' => 1,  // Active status
            ];
        }

        DB::table('loai_phims')->insert($loaiPhimsData);
    }
}
