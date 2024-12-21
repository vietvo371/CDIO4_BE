<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoiVip extends Model
{
    use HasFactory;
    protected $table = 'goi_vips';
    protected $fillable = [
            'ten_goi_vip',
            'slug_goi_vip',
            'gia_tien',
            'tinh_trang',
    ];
}
