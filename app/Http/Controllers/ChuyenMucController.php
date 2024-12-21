<?php

namespace App\Http\Controllers;

use App\Models\ChuyenMuc;
use App\Models\PhanQuyen;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ChuyenMucController extends Controller
{

    public function getData()
    {
        $id_chuc_nang = 11;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $dataAdmin   = ChuyenMuc::select('chuyen_mucs.*')
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
            'chuyen_muc_admin'  =>  $response,
        ]);
    }
    public function getDataHome()
    {
        $data   = ChuyenMuc::where('chuyen_mucs.tinh_trang', 1)
            ->select('chuyen_mucs.*')
            ->get(); // get là ra 1 danh sách
        return response()->json([
            'chuyen_muc'        =>  $data,
        ]);
    }


    public function taoChuyenMuc(Request $request)
    {
        try {
            $id_chuc_nang = 11;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            ChuyenMuc::create([
                'ten_chuyen_muc'    => $request->ten_chuyen_muc,
                'slug_chuyen_muc'   => $request->slug_chuyen_muc,
                'tinh_trang'        => $request->tinh_trang,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Bạn thêm chuyên mục thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá chuyên mục không thành công!!'
            ]);
        }
    }

    public function timChuyenMuc(Request $request)
    {
        $id_chuc_nang = 11;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $key    = '%' . $request->key . '%';
        $dataAdmin   = ChuyenMuc::select('chuyen_mucs.*')
            ->where('ten_chuyen_muc', 'like', $key)
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
            'chuyen_muc_admin'  =>  $response,
        ]);
    }
    public function xoaChuyenMuc($id)
    {
        try {
            $id_chuc_nang = 11;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            ChuyenMuc::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá chuyên mục thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá chuyên mục không thành công!!'
            ]);
        }
    }

    public function capnhatChuyenMuc(Request $request)
    {
        try {
            $id_chuc_nang = 11;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            ChuyenMuc::where('id', $request->id)
                ->update([
                    'ten_chuyen_muc'    => $request->ten_chuyen_muc,
                    'slug_chuyen_muc'   => $request->slug_chuyen_muc,
                    'tinh_trang'        => $request->tinh_trang,
                ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật chuyên mục thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật chuyên mục không thành công!!'
            ]);
        }
    }

    public function thaydoiTrangThaiChuyenMuc(Request $request)
    {

        try {
            $id_chuc_nang = 11;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            ChuyenMuc::where('id', $request->id)
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
    public function kiemTraSlugChuyenMuc(Request $request)
    {
        $tac_gia = ChuyenMuc::where('slug_chuyen_muc', $request->slug)->first();

        if (!$tac_gia) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Chuyên Mục phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Chuyên Mục Đã Tồn Tại!',
            ]);
        }
    }
    public function kiemTraSlugChuyenMucUpdate(Request $request)
    {
        $mon_an = ChuyenMuc::where('slug_chuyen_muc', $request->slug)
            ->where('id', '<>', $request->id)
            ->first();

        if (!$mon_an) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Chuyên Mục phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Chuyên Mục Đã Tồn Tại!',
            ]);
        }
    }
}
