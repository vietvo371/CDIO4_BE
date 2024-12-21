<?php

namespace Database\Seeders;

use App\Models\LoaiPhim;
use App\Models\TheLoai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Fetch 'the_loais' and 'loai_phims' that are active
        $the_loais  = TheLoai::select('ten_the_loai')->where('tinh_trang', 1)->get()->pluck('ten_the_loai')->toArray();
        $loai_phims = LoaiPhim::select('ten_loai_phim')->where('tinh_trang', 1)->get()->pluck('ten_loai_phim')->toArray();

        // Clear existing data in danh_muc_webs table
        DB::table('danh_muc_webs')->truncate();

        // Define top-level categories
        $topCategories = [
            [
                'id' => 1,
                'ten_danh_muc' => 'Trang Chủ',
                'slug_danh_muc' => 'trang-chu',
                'link' => '/',
                'tinh_trang' => 1,
                'id_danh_muc_cha' => null,
            ],
            [
                'id' => 2,
                'ten_danh_muc' => 'Thể Loại',
                'slug_danh_muc' => 'the-loai',
                'link' => '/the-loai',
                'tinh_trang' => 1,
                'id_danh_muc_cha' => null,
            ],
            [
                'id' => 3,
                'ten_danh_muc' => 'Loại Phim',
                'slug_danh_muc' => 'loai-phim',
                'link' => '/loai-phim',
                'tinh_trang' => 1,
                'id_danh_muc_cha' => null,
            ],
            [
                'id' => 4,
                'ten_danh_muc' => 'Blog',
                'slug_danh_muc' => 'blog',
                'link' => '/blog',
                'tinh_trang' => 1,
                'id_danh_muc_cha' => null,
            ],
        ];

        // Insert top-level categories
        DB::table('danh_muc_webs')->insert($topCategories);

        // Define categories under "Thể Loại" and "Loại Phim"
        $subCategories = [];

        foreach ($the_loais as $the_loai) {
            $subCategories[] = [
                'ten_danh_muc' => $the_loai,
                'slug_danh_muc' => Str::slug($the_loai, '-'),
                'link' => 'the-loai/' . Str::slug($the_loai, '-'),
                'tinh_trang' => 1,  // Active status
                'id_danh_muc_cha' => 2,  // Parent ID for "Thể Loại"
            ];
        }

        foreach ($loai_phims as $loai_phim) {
            $subCategories[] = [
                'ten_danh_muc' => $loai_phim,
                'slug_danh_muc' => Str::slug($loai_phim, '-'),
                'link' => 'loai-phim/' . Str::slug($loai_phim, '-'),
                'tinh_trang' => 1,  // Active status
                'id_danh_muc_cha' => 3,  // Parent ID for "Loại Phim"
            ];
        }

        // Insert all sub-categories at once
        DB::table('danh_muc_webs')->insert($subCategories);
    }
}
