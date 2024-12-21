<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapNhatBinhLuanPhimRequest;
use App\Http\Requests\TaoBinhLuanPhimRequest;
use App\Http\Requests\XoaBinhLuanPhimRequest;
use App\Models\BinhLuatTapPhim;
use App\Models\TapPhim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class BinhLuanTapPhimControllerr extends Controller
{
    public function getDataBinhLuanPhim()
    {
        // Truy vấn bình luận của tập phim
        $coment = BinhLuatTapPhim::join('tap_phims', 'binh_luat_tap_phims.id_tap_phim', '=', 'tap_phims.id')
            ->join('khach_hangs', 'binh_luat_tap_phims.id_khach_hang', '=', 'khach_hangs.id')
            ->select('binh_luat_tap_phims.*', 'khach_hangs.ho_va_ten', 'khach_hangs.avatar')
            ->orderBy('binh_luat_tap_phims.created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'binh_luan_tap_phim' => $coment,
        ]);
    }



    public function taoBinhLuanPhim(TaoBinhLuanPhimRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            BinhLuatTapPhim::create([
                'noi_dung'              => $request->noi_dung,
                'id_tap_phim'           => $request->id_tap_phim,
                'id_khach_hang'         => $user->id,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Đánh giá thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Đánh giá không thành công!!'
            ]);
        }
    }
    public function capNhatBinhLuanPhim(CapNhatBinhLuanPhimRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            BinhLuatTapPhim::updateOrCreate(
                [
                    'id' => $request->id,
                    'id_khach_hang' => $user->id,
                    'id_tap_phim' => $request->id_tap_phim
                ],
                [
                    'noi_dung' => $request->noi_dung,
                ]
            );
            return response()->json([
                'status'   => true,
                'message'  => 'Cập nhật đánh giá thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Cập nhật bình luận lỗi!!'
            ]);
        }
    }

    public function xoaBinhLuanPhim(XoaBinhLuanPhimRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            BinhLuatTapPhim::where('id', $request->id)->where('id_khach_hang',$user->id)->delete();
            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá bình luận thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá bình luận không thành công!!'
            ]);
        }
    }
}
