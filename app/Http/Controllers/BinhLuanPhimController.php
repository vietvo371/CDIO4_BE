<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDanhGiaPhimRequest;
use App\Http\Requests\DeleteDanhGiaPhimRequest;
use App\Http\Requests\UpdateDanhGiaPhimRequest;
use App\Models\BinhLuanPhim;
use App\Models\Phim;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class BinhLuanPhimController extends Controller
{
    public function getData(Request $request)
    {
        $film = Phim::select('phims.id')
            ->where('slug_phim', $request->slug)
            ->where('tinh_trang', 1)
            ->first();
        $rate = BinhLuanPhim::where('id_phim', $film->id)
            ->select('id_phim', DB::raw('AVG(so_sao) AS so_sao_trung_binh'), DB::raw('COUNT(id) AS tong_so_luot_danh_gia'))
            ->groupBy('id_phim')
            ->get();
        $limit = $request->limit;
        $coment = BinhLuanPhim::join('phims', 'binh_luan_phims.id_phim', '=', 'phims.id')
            ->join('khach_hangs', 'binh_luan_phims.id_khach_hang', '=', 'khach_hangs.id')
            ->where('phims.id', $film->id)  // Sửa điều kiện ở đây
            ->select('binh_luan_phims.*', 'khach_hangs.ho_va_ten', 'khach_hangs.avatar')
            ->orderBy('binh_luan_phims.created_at', 'desc')
            ->take($limit)
            ->get();
        return response()->json([
            'binh_luan_phim'  =>  $coment,
            'rate'            =>  $rate,
        ]);
    }


    public function taoBinhLuanPhim(CreateDanhGiaPhimRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            BinhLuanPhim::create([
                'noi_dung'              => $request->noi_dung,
                'id_phim'               => $request->id_phim,
                'id_khach_hang'         => $user->id,
                'so_sao'                => $request->so_sao,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Đánh giá thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá binh luận không thành công!!'
            ]);
        }
    }
    public function capNhatBinhLuanPhim(UpdateDanhGiaPhimRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            BinhLuanPhim::updateOrCreate(
                [
                    'id' => $request->id,
                    'id_khach_hang' => $user->id,
                    'id_phim' => $request->id_phim
                ],
                [
                    'noi_dung' => $request->noi_dung,
                    'so_sao' => $request->so_sao
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



    public function xoaBinhLuanPhim(DeleteDanhGiaPhimRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            BinhLuanPhim::where('id', $request->id)->where('id_khach_hang', $user->id)->delete();

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
