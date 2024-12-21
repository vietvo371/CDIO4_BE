<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietDienVien extends Model
{
    use HasFactory;
    protected   $table = 'chi_tiet_dien_viens';
    protected   $fillable = [
        'id_phim',
        'id_dien_vien',
    ];
}
