<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class KhachHang extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'khach_hangs';
    protected $fillable = [
        'ho_va_ten',
        'avatar',
        'email',
        'password',
        'so_dien_thoai',
        'is_block',
        'is_active',
        'google_id',
        'hash_reset',
        'hash_active',
        'id_goi_vip',
    ];

}
