<?php

namespace App\Http\Controllers;

use App\Models\BinhLuanBaiViet;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class BinhLuanBaiVietController extends Controller
{
    public function getData()
    {
        $data   = BinhLuanBaiViet::join('bai_viets','id_bai_viet','bai_viets.id')
                                ->join('khach_hangs','id_khach_hang','khach_hangs.id')
                                ->select('binh_luan_bai_viets.*','khach_hangs.ho_va_ten','khach_hangs.hinh_anh')
                                // ->take(3)
                                ->get(); // get là ra 1 danh sách
                        return response()->json([
                        'binh_luan_bai_viet'  =>  $data,
                        ]);
    }

    public function taoBinhLuanBlog(Request $request)
    {
        try {
            BinhLuanBaiViet::create([
                'noi_dung'              =>$request->noi_dung,
                'id_bai_viet'           =>$request->id_bai_viet,
                'id_khach_hang'         =>$request->id_khach_hang,
                ]);
                return response()->json([
                    'status'   => true ,
                    'message'  => 'Bạn thêm binh luận thành công!',
                ]);
        } catch (ExceptionEvent $e) {
                return response()->json([
                    'status'     => false,
                    'message'    => 'Xoá binh luận không thành công!!'
                ]);
        }

    }
    public function xoaBinhLuanBlog($id)
    {
        try {
            BinhLuanBaiViet::where('id', $id)->delete();

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
