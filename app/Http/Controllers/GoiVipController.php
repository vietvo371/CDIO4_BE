<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapNhatGoiVipRequest;
use App\Http\Requests\TaoGoiVipRequest;
use App\Http\Requests\thaydoiTrangThaiGoiVip;
use App\Models\GoiVip;
use App\Models\PhanQuyen;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class GoiVipController extends Controller
{
    public function goiVipOpen()
    {
        $dataOpen   = GoiVip::select('goi_vips.*')
            ->where('tinh_trang', 1)
            ->get();
        return response()->json([
            'data'  =>  $dataOpen,
        ]);
    }
    
    public function getData()
    {
        $id_chuc_nang = 13;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $dataAdmin   = GoiVip::select('goi_vips.*')
            ->paginate(6); // get là ra 1  sách
        $response = [
            'pagination' => [
                'total' => $dataAdmin->total(),
                'per_page' => $dataAdmin->perPage(),
                'current_page' => $dataAdmin->currentPage(),
                'last_page' => $dataAdmin->lastPage(),
                'from' => $dataAdmin->firstItem(),
                'to' => $dataAdmin->lastItem()
            ],
            'dataAdmin' => $dataAdmin
        ];
        return response()->json([
            'goi_vips'  =>  $response,
        ]);
    }
    public function taoGoiVip(TaoGoiVipRequest $request)
    {
        try {
            $id_chuc_nang = 13;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            GoiVip::create([
                'ten_goi'       => $request->ten_goi,
                'slug_goi_vip'  => $request->slug_goi_vip,
                'thoi_han'      => $request->thoi_han,
                'tien_goc'      => $request->tien_goc,
                'tien_sale'     => $request->tien_sale,
                'tinh_trang'    => $request->tinh_trang,
            ]);
            return response()->json([
                'status'   => true,
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
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
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

    public function capnhatGoiVip(CapNhatGoiVipRequest $request)
    {
        try {
            $id_chuc_nang = 13;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            GoiVip::where('id', $request->id)
                ->update([
                    'ten_goi'       => $request->ten_goi,
                    'slug_goi_vip'  => $request->slug_goi_vip,
                    'thoi_han'      => $request->thoi_han,
                    'tien_goc'      => $request->tien_goc,
                    'tien_sale'     => $request->tien_sale,
                    'tinh_trang'    => $request->tinh_trang,
                ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật goi vip thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật goi vip không thành công!!'
            ]);
        }
    }

    public function thaydoiTrangThaiGoiVip(thaydoiTrangThaiGoiVip $request)
    {

        try {
            $id_chuc_nang = 13;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            GoiVip::where('id', $request->id)
                ->update([
                    'tinh_trang'    => $tinh_trang_moi
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
        $goi_vip = GoiVip::where('slug_goi_vip', $request->slug)->first();

        if (!$goi_vip) {
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
            ->where('id', '<>', $request->id)
            ->first();

        if (!$mon_an) {
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
        $id_chuc_nang = 13;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $key    = '%' . $request->key . '%';
        $dataAdmin   = GoiVip::select('goi_vips.*')
            ->where('ten_goi', 'like', $key)
            ->paginate(6); // get là ra 1  sách
        $response = [
            'pagination' => [
                'total' => $dataAdmin->total(),
                'per_page' => $dataAdmin->perPage(),
                'current_page' => $dataAdmin->currentPage(),
                'last_page' => $dataAdmin->lastPage(),
                'from' => $dataAdmin->firstItem(),
                'to' => $dataAdmin->lastItem()
            ],
            'dataAdmin' => $dataAdmin
        ];
        return response()->json([
            'goi_vips'  =>  $response,
        ]);
    }
}
