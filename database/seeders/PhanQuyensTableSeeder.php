<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhanQuyensTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('phan_quyens')->truncate();

        $phanQuyens = [
            // Role: Quản Trị Website
            [
                'id_chuc_vu' => 1, // Assuming the role 'Quản Trị Website' has id 1
                'id_chuc_nang' => 1, // Assuming the permission 'Thêm, Sữa Xoá Tài Khoản Admin' has id 1
            ],
            [
                'id_chuc_vu' => 1, // Quản Trị Website
                'id_chuc_nang' => 2, // Thêm, Sữa Xoá Tài Khoản Khách Hàng
            ],
            [
                'id_chuc_vu' => 1, // Quản Trị Website
                'id_chuc_nang' => 3, // Thêm, Sữa Xoá Chức Vụ
            ],
            // Role: Quản trị Phim
            [
                'id_chuc_vu' => 2, // Quản trị Phim
                'id_chuc_nang' => 5, // Thêm, Sữa Xoá Phim
            ],
            [
                'id_chuc_vu' => 2, // Quản trị Phim
                'id_chuc_nang' => 6, // Thêm, Sữa Xoá Tập Phim
            ],
            [
                'id_chuc_vu' => 2, // Quản trị Phim
                'id_chuc_nang' => 8, // Thêm, Sữa Xoá Loại Phim
            ],
            // Role: Quản Trị Blog
            [
                'id_chuc_vu' => 3, // Quản Trị Blog
                'id_chuc_nang' => 10, // Thêm, Sữa Xoá BLOG
            ],
            [
                'id_chuc_vu' => 3, // Quản Trị Blog
                'id_chuc_nang' => 11, // Thêm, Sữa Xoá Chuyên Mục BLOG
            ],
            [
                'id_chuc_vu' => 3, // Quản Trị Blog
                'id_chuc_nang' => 14, // Quản lý Menu Client
            ],
        ];

        DB::table('phan_quyens')->insert($phanQuyens);
    }
}
