<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMucWeb extends Model
{
    use HasFactory;

    protected $table = 'danh_muc_webs';
    protected $fillable = [
        'ten_danh_muc',
        'slug_danh_muc',
        'tinh_trang',
        'id_danh_muc_cha'
    ];
}
