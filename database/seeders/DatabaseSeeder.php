<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Http\Controllers\PhimController;
use App\Models\Action;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        $this->call(ActionsTableSeeder::class);
        $this->call(ChucVusTableSeeder::class);
        $this->call(PhanQuyensTableSeeder::class);
        $this->call(LoaiPhimSeeder::class);
        $this->call(TheLoaiSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(GoiVipsTableSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(ChuyenMucSeeder::class);
        $this->call(BaiVietSeeder::class);


        // $this->call(PhimSeeder::class);
        // $this->call(TacGiaSeeder::class);

        // $this->call(BlogSeeder::class);
        // $this->call(ChuyenMucSeeder::class);
        // $this->call(AdminSeeder::class);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
