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
        Schema::create('luot_xems', function (Blueprint $table) {
            $table->id();
            $table->integer('id_phim');
            $table->integer('id_tap_phim');
            $table->date('ngay_xem');
            $table->integer('so_luot_xem');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('luot_xems');
    }
};
