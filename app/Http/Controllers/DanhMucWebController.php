<?php

namespace App\Http\Controllers;

use App\Models\DanhMucWeb;
use App\Models\PhanQuyen;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class DanhMucWebController extends Controller
{
    public function getData()
    {
        $id_chuc_nang = 14;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $dataAdmin   = DanhMucWeb::leftjoin('danh_muc_webs as cha','danh_muc_webs.id_danh_muc_cha','cha.id')->select('danh_muc_webs.*','cha.ten_danh_muc as ten_danh_muc_cha')
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
            'danh_muc_admin'  =>  $response,
        ]);
    }
    public function taoDanhMuc(Request $request)
    {
        try {
            $id_chuc_nang = 14;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            DanhMucWeb::create([
                'ten_danh_muc'          => $request->ten_danh_muc,
                'slug_danh_muc'         => $request->slug_danh_muc,
                'id_danh_muc_cha'       => $request->id_danh_muc_cha,
                'tinh_trang'            => $request->tinh_trang,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Bạn thêm Danh Mục thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Danh Mục không thành công!!'
            ]);
        }
    }
    public function timDanhMuc(Request $request)
    {
        $id_chuc_nang = 14;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $key    = '%' . $request->key . '%';
        $dataAdmin   = DanhMucWeb::select('danh_muc_webs.*')
            ->where('ten_danh_muc', 'like', $key)
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
            'danh_muc_admin'  =>  $response,
        ]);
    }
    public function capnhatDanhMuc(Request $request)
    {
        try {
            $id_chuc_nang = 14;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            DanhMucWeb::where('id', $request->id)
                ->update([
                    'ten_danh_muc'          => $request->ten_danh_muc,
                    'slug_danh_muc'         => $request->slug_danh_muc,
                    'id_danh_muc_cha'       => $request->id_danh_muc_cha,
                    'tinh_trang'            => $request->tinh_trang,

                ]);
            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật thành ' . $request->ten_danh_muc,
            ]);
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật  Danh Mục không thành công!!'
            ]);
        }
    }
    public function xoaDanhMuc($id)
    {
        try {
            $id_chuc_nang = 14;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            DanhMucWeb::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá Danh Mục thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá  Danh Mục không thành công!!'
            ]);
        }
    }
    public function thaydoiTrangThaiDanhMuc(Request $request)
    {

        try {
            $id_chuc_nang = 14;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            DanhMucWeb::where('id', $request->id)
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
    public function kiemTraSlugDanhMuc(Request $request)
    {
        $tac_gia = DanhMucWeb::where('slug_danh_muc', $request->slug)->first();

        if (!$tac_gia) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Danh Mục phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Danh Mục Đã Tồn Tại!',
            ]);
        }
    }
    public function kiemTraSlugDanhMucUpdate(Request $request)
    {
        $danh_muc = DanhMucWeb::where('slug_danh_muc', $request->slug)
            ->where('id', '<>', $request->id)
            ->first();

        if (!$danh_muc) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Danh Mục phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Danh Mục Đã Tồn Tại!',
            ]);
        }
    }
}
