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
        Schema::create('admin_animes', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('ho_va_ten');
            $table->string('password');
            $table->string('so_dien_thoai');
            $table->string('hinh_anh')->nullable();
            $table->integer('tinh_trang')->default(1);
            $table->integer('id_chuc_vu');
            $table->integer('is_master')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_animes');
    }
};
