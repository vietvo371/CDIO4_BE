<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigSlide extends Model
{
    use HasFactory;
    protected $table = 'config_slides';
    protected $fillable = [
        'slide',
        'id_phim',
        'tinh_trang',
    ];
}
