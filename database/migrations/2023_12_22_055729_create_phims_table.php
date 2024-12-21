<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phims', function (Blueprint $table) {
            $table->id();
            $table->string('ten_phim');
            $table->string('slug_phim');
            $table->longText('hinh_anh');
            $table->longText('mo_ta');
            $table->integer('thoi_gian_chieu');
            $table->integer('nam_san_xuat');
            $table->string('quoc_gia');
            $table->string('cong_ty_san_xuat');
            $table->integer('id_loai_phim');
            $table->string('the_loai_thanh_toan')->nullable();
            $table->integer('id_the_loai')->default(0);
            $table->string('dao_dien');
            $table->integer('so_tap_phim');
            $table->integer('tong_luong_xem')->default(0);
            $table->integer('tinh_trang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phims');
    }
};
