<?php

namespace App\Http\Controllers;

use App\Models\Phim;
use App\Models\YeuThich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class YeuThichController extends Controller
{
    public function getData()
    {
        $user = Auth::guard('sanctum')->user();
        $data   = YeuThich::join('phims', 'id_phim', 'phims.id')
            ->where('id_khach_hang', $user->id)
            ->join('khach_hangs', 'id_khach_hang', 'khach_hangs.id')
            ->select('phims.*', 'khach_hangs.ho_va_ten', 'yeu_thichs.*')
            // ->take(3)
            ->get(); // get là ra 1 danh sách
        return response()->json([
            'yeu_thich'  =>  $data,
        ]);
    }
    public function taoYeuThich(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            YeuThich::create([
                'id_phim'               => $request->id_phim,
                'id_khach_hang'         => $user->id,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Bạn dã thêm vào yêu thích!!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Bạn dã bỏ yêu thích!!!'
            ]);
        }
    }
    public function xoaYeuThich(Request $request)
    {
        try {
            YeuThich::where('id_phim', $request->id_phim)
                ->where('id_khach_hang', $request->id_khach_hang)
                ->delete();
            return response()->json([
                'status'     => true,
                'message'    => 'Bạn bỏ yêu thích!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá không thành công!!'
            ]);
        }
    }
    public function checkYeuThich(Request $request)
    {
        try {
            $film = Phim::select('phims.id')
                ->where('slug_phim', $request->slug)
                ->where('tinh_trang', 1)
                ->first();
            $user = Auth::guard('sanctum')->user();
            $check = YeuThich::where('id_phim', $film->id)
                ->where('id_khach_hang', $user->id)
                ->first();

            if ($check) {
                return response()->json([
                    'status'     => true,
                    'message'    => 'đã yêu thích!!'
                ]);
            } else {
                return response()->json([
                    'status'     => false,
                    'message'    => 'chưa yêu thích!!'
                ]);
            }
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => ' không  yêu thích!!'
            ]);
        }
    }
    public function timYeuThich(Request $request)
    {
        $key    = '%' . $request->key . '%';
        $data   = YeuThich::select('yeu_thichs.*')
            ->where('ho_va_ten', 'like', $key)
            ->get(); // get là ra 1 danh sách
        return response()->json([
            'khach_hang'  =>  $data,
        ]);
    }
}
