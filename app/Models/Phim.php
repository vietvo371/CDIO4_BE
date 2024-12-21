<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phim extends Model
{
    use HasFactory;
    protected $table = 'phims';
    protected $fillable = [
        'ten_phim',
        'slug_phim',
        'hinh_anh',
        'mo_ta',
        'thoi_gian_chieu',
        'nam_san_xuat',
        'quoc_gia',
        'id_loai_phim',
        'the_loai_thanh_toan',
        'id_the_loai',
        'dao_dien',
        'so_tap_phim',
        'tong_luong_xem',
        'tinh_trang',
        'cong_ty_san_xuat'
    ];

}
