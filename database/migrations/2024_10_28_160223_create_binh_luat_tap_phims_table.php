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
        Schema::create('binh_luat_tap_phims', function (Blueprint $table) {
            $table->id();
            $table->longText('noi_dung');
            $table->integer('id_tap_phim');
            $table->integer('id_khach_hang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('binh_luat_tap_phims');
    }
};
