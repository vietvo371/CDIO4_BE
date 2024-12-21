<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('actions')->truncate();

        $actions = [
            ['ten_chuc_nang' => 'Thêm, Sữa Xoá Tài Khoản Admin'],
            ['ten_chuc_nang' => 'Thêm, Sữa Xoá Tài Khoản Khách Hàng'],
            ['ten_chuc_nang' => 'Thêm, Sữa Xoá Chức Vụ'],
            ['ten_chuc_nang' => 'Phân Quyền'],
            ['ten_chuc_nang' => 'Thêm, Sữa Xoá Phim'],
            ['ten_chuc_nang' => 'Thêm, Sữa Xoá Tập Phim'],
            ['ten_chuc_nang' => 'Thêm, Sữa Xoá Thể Loại'],
            ['ten_chuc_nang' => 'Thêm, Sữa Xoá Loại Phim'],
            ['ten_chuc_nang' => 'Thêm, Sữa Xoá Tác Giả'],
            ['ten_chuc_nang' => 'Thêm, Sữa Xoá BLOG'],
            ['ten_chuc_nang' => 'Thêm, Sữa Xoá Chuyên Mục BLOG'],
            ['ten_chuc_nang' => 'Thống Kê'],
            ['ten_chuc_nang' => 'Thêm, Sữa Xoá Gói Vip'],
            ['ten_chuc_nang' => 'Quản lý Menu Client'],
            ['ten_chuc_nang' => 'Quản lý Leech Phim'],
            ['ten_chuc_nang' => 'Quản lý SLide'],
            ['ten_chuc_nang' => 'Quản lý Thanh Toán'],
        ];
        DB::table('actions')->insert($actions);
    }
}
