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
        Schema::create('khach_hangs', function (Blueprint $table) {
            $table->id();
            $table->string('ho_va_ten');
            $table->string('avatar')->nullable();
            $table->string('email');
            $table->string('password');
            $table->string('so_dien_thoai')->nullable();
            $table->integer('is_block')->default(1);
            $table->integer('is_active')->default(0);
            $table->string('google_id')->nullable();
            $table->string ('hash_reset')->nullable();
            $table->string ('hash_active')->nullable();
            $table->integer('id_goi_vip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khach_hangs');
    }
};
