<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LuotPhim extends Model
{
    use HasFactory;
    protected $table = 'luot_xems';
    protected $fillable = [
        'id_phim',
        'id_tap_phim',
        'ngay_xem',
        'so_luot_xem',
    ];
}
