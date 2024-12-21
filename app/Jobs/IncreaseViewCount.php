<?php

namespace App\Jobs;

use App\Models\LuotPhim;
use App\Models\Phim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IncreaseViewCount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phimId;
    protected $tapPhimId;
    protected $userId;
    public function __construct($phimId, $tapPhimId, $userId)
    {
        $this->phimId = $phimId;
        $this->tapPhimId = $tapPhimId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $luotXem = LuotPhim::firstOrCreate(
            [
                'id_phim' => $this->phimId,
                'id_tap_phim' => $this->tapPhimId,
                'ngay_xem' => date('Y-m-d'),
                'id_khach_hang' => $this->userId, // Liên kết khách hàng (nếu có).
            ],
            [
                'so_luot_xem' => 0,
            ]
        );

        $luotXem->increment('so_luot_xem');

        // Tăng tổng lượt xem cho phim
        Phim::where('id', $this->phimId)->increment('tong_luot_xem');
        // Tăng lượt xem cho tập phim

    }
}
