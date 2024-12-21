<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    use HasFactory;
    protected $table = "hoa_dons";
    protected $fillable = [
        'ma_hoa_don',
        'id_goi',
        'id_khach_hang',
        'tong_tien',
        'tinh_trang',
        'ngay_bat_dau',
        'ngay_ket_thuc',
    ];
}
