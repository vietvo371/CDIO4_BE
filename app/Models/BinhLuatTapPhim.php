<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BinhLuatTapPhim extends Model
{
    use HasFactory;
    protected $table = 'binh_luat_tap_phims';
    protected $fillable = [
        'noi_dung',
        'id_tap_phim',
        'id_khach_hang',
    ];
}
