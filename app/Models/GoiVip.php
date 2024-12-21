<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoiVip extends Model
{
    use HasFactory;
    protected $table = 'goi_vips';
    protected $fillable = [
        'ten_goi',
        'slug_goi_vip',
        'thoi_han',
        'tien_goc',
        'tien_sale',
        'tinh_trang',
    ];
}
