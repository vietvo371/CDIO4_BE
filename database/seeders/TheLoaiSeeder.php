<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class TheLoaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('the_loais')->truncate();
        $the_loais = [
            'Viễn Tưởng',
            'Khoa Học',
            'Chính Kịch',
            'Bí Ẩn',
            'Hành Động',
            'Hài Hước',
            'Phiêu Lưu',
            'Kinh Dị',
            'Tình Cảm',
            'Chiến Tranh',
            'Gia Đình',
            'Tâm Lý',
            'Thể Thao',
            'Võ Thuật',
            'Cổ Trang',
            'Hình Sự',
            'Tài Liệu',
            'Âm Nhạc',
            'Thần Thoại',
            'Học Đường',
            'Kinh Điển',
            'Phim 18+'
        ];

        $theLoaisData = [];

        foreach ($the_loais as $the_loai) {
            $theLoaisData[] = [
                'ten_the_loai' => $the_loai,
                'slug_the_loai' => Str::slug($the_loai, '-'),
                'tinh_trang' => 1,  // Active status
            ];
        }

        DB::table('the_loais')->insert($theLoaisData);
    }
}
