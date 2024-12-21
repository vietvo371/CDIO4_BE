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
            'email',
           'ho_va_ten',
           'password',
           'hinh_anh',
           'hash_quen_mat_khau',
           'hash_kich_hoat',
           'is_done',
           'ngay_sinh',
    ];

}
