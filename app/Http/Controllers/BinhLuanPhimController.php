<?php

namespace App\Http\Controllers;

use App\Models\BinhLuanPhim;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class BinhLuanPhimController extends Controller
{
    public function getData()
    {
        $data   = BinhLuanPhim::join('phims','id_phim','phims.id')
                                ->join('khach_hangs','id_khach_hang','khach_hangs.id')
                                ->select('binh_luan_phims.*','khach_hangs.ho_va_ten','khach_hangs.hinh_anh')
                                // ->take(3)
                                ->get(); // get là ra 1 danh sách
                        return response()->json([
                        'binh_luan_phim'  =>  $data,
                        ]);
    }


    public function taoBinhLuanPhim(Request $request)
    {
        try {
            BinhLuanPhim::create([
                'noi_dung'              =>$request->noi_dung,
                'id_phim'               =>$request->id_phim,
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



    public function xoaBinhLuanPhim($id)
    {
        try {
            BinhLuanPhim::where('id', $id)->delete();

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
