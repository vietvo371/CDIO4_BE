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
        $adminAnimes = [
            [
                'email'         => "vietvo371@gmail.com",
                'ho_va_ten'     => "Văn Việt",
                'password'      => bcrypt(123456),
                'so_dien_thoai' => '0708585120',
                'hinh_anh'      => "http://127.0.0.1:8000/uploads/avatars/admins/default_avatar.png",
                'tinh_trang'    => 1,
                'id_chuc_vu'    => 1,
                'is_master'     => 1,
            ],
            [
                'email'         => "dinhquy223@gmail.com",
                'ho_va_ten'     => "Đình Quý",
                'password'      => bcrypt(123456),
                'so_dien_thoai' => '0987654321',
                'hinh_anh'      => 'http://127.0.0.1:8000/uploads/avatars/admins/default_avatar.png',
                'tinh_trang'    => 1,
                'id_chuc_vu'    => 2,
                'is_master'     => 0,
            ],
        ];
        DB::table('admin_animes')->delete();
        DB::table('admin_animes')->truncate();
        DB::table('admin_animes')->insert($adminAnimes);
    }
}
