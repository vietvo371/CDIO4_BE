<?php

namespace App\Http\Controllers;

use App\Models\GoiVip;
use App\Models\PhanQuyen;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class GoiVipController extends Controller
{
    public function getData()
    {
        $id_chuc_nang = 13;
        $user   = Auth::guard('sanctum')->user(); // Chính là người đang login
        $user_chuc_vu   = $user->id_chuc_vu;    // Giả sử
        $check  = PhanQuyen::where('id_chuc_vu', $user_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $data   = GoiVip::select('goi_vips.*')
            ->get(); // get là ra 1 danh sách
        return response()->json([
            'goi_vips'  =>  $data,
        ]);
    }
    public function taoGoiVip(Request $request)
    {
        try {
            $id_chuc_nang = 13;
            $user   = Auth::guard('sanctum')->user(); // Chính là người đang login
            $user_chuc_vu   = $user->id_chuc_vu;    // Giả sử
            $check  = PhanQuyen::where('id_chuc_vu', $user_chuc_vu)
                                ->where('id_chuc_nang', $id_chuc_nang)
                                ->first();
            if(!$check) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            GoiVip::create([
                'ten_goi_vip'       =>$request->ten_goi_vip,
                'slug_goi_vip'      =>$request->slug_goi_vip,
                'gia_tien'          =>$request->gia_tien,
                'tinh_trang'          =>$request->tinh_trang,
                ]);
                return response()->json([
                    'status'   => true ,
                    'message'  => 'Bạn thêm gói vip thành công!',
                ]);
        } catch (ExceptionEvent $e) {
                return response()->json([
                    'status'     => false,
                    'message'    => 'thêm gói vip không thành công!!'
                ]);
        }

    }
    public function xoaGoiVip($id)
    {
        try {
            $id_chuc_nang = 13;
            $user   = Auth::guard('sanctum')->user(); // Chính là người đang login
            $user_chuc_vu   = $user->id_chuc_vu;    // Giả sử
            $check  = PhanQuyen::where('id_chuc_vu', $user_chuc_vu)
                                ->where('id_chuc_nang', $id_chuc_nang)
                                ->first();
            if(!$check) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            GoiVip::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá goi vip thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá goi vip không thành công!!'
            ]);

        }

    }

    public function capnhatGoiVip(Request $request)
    {
        try {
            $id_chuc_nang = 13;
            $user   = Auth::guard('sanctum')->user(); // Chính là người đang login
            $user_chuc_vu   = $user->id_chuc_vu;    // Giả sử
            $check  = PhanQuyen::where('id_chuc_vu', $user_chuc_vu)
                                ->where('id_chuc_nang', $id_chuc_nang)
                                ->first();
            if(!$check) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            GoiVip::where('id', $request->id)
                    ->update([
                        'ten_goi_vip'       =>$request->ten_goi_vip,
                        'slug_goi_vip'      =>$request->slug_goi_vip,
                        'gia_tien'          =>$request->gia_tien,
                        'tinh_trang'          =>$request->tinh_trang,
                    ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật goi vip thành công!' ,
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật goi vip không thành công!!'
            ]);
        }
    }

    public function thaydoiTrangThaiGoiVip(Request $request)
    {

        try {
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            GoiVip::where('id', $request->id)
                    ->update([
                        'tinh_trang'    =>$tinh_trang_moi
                    ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Cập Nhật Trạng Thái thành công!! '
            ]);
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật Trạng Thái không thành công!!'
            ]);
        }
    }
    public function kiemTraSlugGoiVip(Request $request)
    {
        $tac_gia = GoiVip::where('slug_goi_vip', $request->slug)->first();

        if(!$tac_gia) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Gói Vip phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Gói Vip Đã Tồn Tại!',
            ]);
        }
    }
    public function kiemTraSlugGoiVipUpdate(Request $request)
    {
        $mon_an = GoiVip::where('slug_goi_vip', $request->slug)
                                     ->where('id', '<>' , $request->id)
                                     ->first();

        if(!$mon_an) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Gói Vip phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Gói Vip Đã Tồn Tại!',
            ]);
        }
    }
    public function timGoiVip(Request $request)
    {
        $key    = '%'. $request->key . '%';
        $data   = GoiVip::select('goi_vips.*')
                    ->where('ten_goi_vip', 'like', $key)
                    ->get(); // get là ra 1 danh sách
        return response()->json([
        'goi_vips'  =>  $data,
        ]);
    }
}