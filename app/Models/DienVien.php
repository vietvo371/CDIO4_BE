<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DienVien extends Model
{
    use HasFactory;
    protected $table = 'dien_viens';
    protected $fillable = [
        'ten_dv',
        'mo_ta',
        'nam_sinh',
        'tinh_trang',
    ];
}
