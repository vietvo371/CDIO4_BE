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
        Schema::create('dien_viens', function (Blueprint $table) {
            $table->id();
            $table->string("ten_dv");
            $table->string("mo_ta");
            $table->integer("nam_sinh");
            $table->integer("tinh_trang");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dien_viens');
    }
};
