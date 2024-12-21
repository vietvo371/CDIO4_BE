<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoaiPhim extends Model
{
    use HasFactory;
    protected $table = 'loai_phims';
    protected $fillable = [
            'ten_loai_phim',
            'slug_loai_phim',
            'tinh_trang',
    ];
}
