<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\ChuyenMuc;
use App\Models\PhanQuyen;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class BaiVietController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getData()
    {
        $id_chuc_nang = 10;
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
        $chuyen_muc_admin   = ChuyenMuc::select('chuyen_mucs.*')
            ->get();
        $dataAdmin   = BaiViet::join('chuyen_mucs', 'id_chuyen_muc', 'chuyen_mucs.id')
            ->select('bai_viets.*', 'chuyen_mucs.ten_chuyen_muc')
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
            'bai_viet_admin'  =>  $response,
            'chuyen_muc_admin'  =>  $chuyen_muc_admin,
        ]);
    }
    public function getDataHome()
    {
        $data   = BaiViet::where('bai_viets.tinh_trang', 1)
            ->join('chuyen_mucs', 'id_chuyen_muc', 'chuyen_mucs.id')
            ->select('bai_viets.*', 'chuyen_mucs.ten_chuyen_muc')
            ->orderBy('id', 'DESC') // sắp xếp giảm dần
            ->get(); // get là ra 1 danh sách

        return response()->json([
            'bai_viet'        =>  $data,
        ]);
    }
    public function getDelistBlog(Request $request)
    {
        $data   = BaiViet::where('bai_viets.tinh_trang', 1)
            ->join('chuyen_mucs', 'id_chuyen_muc', 'chuyen_mucs.id')
            ->where('bai_viets.slug_tieu_de', $request->slug)
            ->select('bai_viets.*', 'chuyen_mucs.ten_chuyen_muc')
            ->first(); // get là ra 1 danh sách

        return response()->json([
            'bai_viet'        =>  $data,
        ]);
    }


    public function taoBaiViet(Request $request)
    {
        try {
            $id_chuc_nang = 10;
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
            BaiViet::create([
                'tieu_de'               => $request->tieu_de,
                'slug_tieu_de'           => $request->slug_tieu_de,
                'hinh_anh'              => $request->hinh_anh,
                'mo_ta'                 => $request->mo_ta,
                'mo_ta_chi_tiet'        => $request->mo_ta_chi_tiet,
                'id_chuyen_muc'         => $request->id_chuyen_muc,
                'tinh_trang'            => $request->tinh_trang,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Bạn thêm bài viết thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá bài viết không thành công!!'
            ]);
        }
    }

    public function timBaiViet(Request $request)
    {
        $key    = '%' . $request->key . '%';
        $dataAdmin   = BaiViet::select('bai_viets.*')
            ->where('tieu_de', 'like', $key)
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
            'bai_viet_admin'  =>  $response,
        ]);
    }
    public function xoaBaiViet($id)
    {
        try {
            $id_chuc_nang = 10;
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
            BaiViet::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá bài viết thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá bài viết không thành công!!'
            ]);
        }
    }

    public function capnhatBaiViet(Request $request)
    {
        try {
            $id_chuc_nang = 10;
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
            BaiViet::where('id', $request->id)
                ->update([
                    'tieu_de'               => $request->tieu_de,
                    'hinh_anh'              => $request->hinh_anh,
                    'mo_ta'                 => $request->mo_ta,
                    'mo_ta_chi_tiet'        => $request->mo_ta_chi_tiet,
                    'id_chuyen_muc'         => $request->id_chuyen_muc,
                    'tinh_trang'            => $request->tinh_trang,
                ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật bài viết thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật bài viết không thành công!!'
            ]);
        }
    }

    public function thaydoiTrangThaiBaiViet(Request $request)
    {

        try {
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            BaiViet::where('id', $request->id)
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
    public function kiemTraSlugBaiViet(Request $request)
    {
        $tac_gia = BaiViet::where('slug_tieu_de', $request->slug)->first();

        if (!$tac_gia) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Bài Viết phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Bài Viết Đã Tồn Tại!',
            ]);
        }
    }
    public function kiemTraSlugBaiVietUpdate(Request $request)
    {
        $mon_an = BaiViet::where('slug_tieu_de', $request->slug)
            ->where('id', '<>', $request->id)
            ->first();

        if (!$mon_an) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Bài Viết phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Bài Viết Đã Tồn Tại!',
            ]);
        }
    }
}
