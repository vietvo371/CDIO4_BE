<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapNhatBlogRequest;
use App\Http\Requests\TaoBinhLuanBlogRequest;
use App\Http\Requests\XoaBinhLuanBlogRequest;
use App\Models\BinhLuanBaiViet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class BinhLuanBaiVietController extends Controller
{
    public function getData()
    {
        $data   = BinhLuanBaiViet::join('bai_viets', 'id_bai_viet', 'bai_viets.id')
            ->join('khach_hangs', 'id_khach_hang', 'khach_hangs.id')
            ->select('binh_luan_bai_viets.*', 'khach_hangs.ho_va_ten', 'khach_hangs.avatar')
            // ->take(3)
            ->get(); // get là ra 1 danh sách
        return response()->json([
            'binh_luan_bai_viet'  =>  $data,
        ]);
    }

    public function taoBinhLuanBlog(TaoBinhLuanBlogRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            BinhLuanBaiViet::create([
                'noi_dung'              => $request->noi_dung,
                'id_bai_viet'           => $request->id_bai_viet,
                'id_khach_hang'         => $user->id,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Thêm binh luận thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => ' binh luận không thành công!!'
            ]);
        }
    }
    public function capNhatBlog(CapNhatBlogRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            BinhLuanBaiViet::updateOrCreate(
                [
                    'id' => $request->id,
                    'id_khach_hang' => $user->id,
                    'id_bai_viet' => $request->id_bai_viet
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
    public function xoaBinhLuanBlog(XoaBinhLuanBlogRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            BinhLuanBaiViet::where('id', $request->id)->where('id_khach_hang', $user->id)->delete();
            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá bình luận thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => ' bình luận không thành công!!'
            ]);
        }
    }
}
