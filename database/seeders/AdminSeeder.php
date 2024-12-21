<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admin_animes')->delete();
        DB::table('admin_animes')->truncate();
        DB::table('admin_animes')->insert([
            [
                'email'         =>"vietvo371@gmail.com",
                'ho_va_ten'     =>"Văn Việt",
                'password'      =>bcrypt(123456),
                'hinh_anh'      =>"https://res.cloudinary.com/dltbjoii4/image/upload/v1705578787/image_anime/yqhdc90cpluahrye6sfr.jpg",
                'id_chuc_vu'      =>"1",

            ],
            [
                'email'         =>"dinhquy223@gmail.com",
                'ho_va_ten'     =>"Đình Quý",
                'password'      =>bcrypt(123456),
                'hinh_anh'      =>"https://res.cloudinary.com/dltbjoii4/image/upload/v1705578814/image_anime/hl93ebbase1xuwldoeoj.jpg",
                'id_chuc_vu'      =>"1",
            ],



        ]);

    }
}
