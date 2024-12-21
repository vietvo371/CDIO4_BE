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
        Schema::create('goi_vips', function (Blueprint $table) {
            $table->id();
            $table->string('ten_goi');
            $table->string('slug_goi_vip');
            $table->integer('thoi_han')->comment('tinh_theo_thang');
            $table->double('tien_goc');
            $table->double('tien_sale');
            $table->integer('tinh_trang')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goi_vips');
    }
};
