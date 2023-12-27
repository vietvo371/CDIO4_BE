<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    use HasFactory;
    protected $table = 'khach_hangs';

    protected $fillable = [
        'email',
        'ho_va_ten',
        'password',
        'hinh_anh',
    ];
}
