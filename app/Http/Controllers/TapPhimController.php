<?php

namespace App\Http\Controllers;

use App\Models\PhanQuyen;
use App\Models\Phim;
use App\Models\TapPhim;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class TapPhimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getData()
    {
        $id_chuc_nang = 6;
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
        $dataAdmin       = TapPhim::join('phims', 'id_phim', 'phims.id')
            ->orderBy('slug_tap_phim', 'ASC')
            ->select('tap_phims.*', 'phims.ten_phim')
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
            'tap_phim_admin'  =>  $response,
        ]);
    }
    public function getDataHome()
    {
        $data       = TapPhim::join('phims', 'id_phim', 'phims.id')
            ->join('the_loais', 'phims.id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'phims.id_loai_phim', 'loai_phims.id')
            ->where('tap_phims.tinh_trang', 1)
            ->select('tap_phims.*', 'phims.ten_phim', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'phims.id_the_loai', 'phims.id_loai_phim')
            ->get(); // get là ra 1 danh sách
        return response()->json([
            'tap_phim'  =>  $data,
        ]);
    }

    public function taoTapPhim(Request $request)
    {
        try {
            $id_chuc_nang = 6;
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
            TapPhim::create([
                'ten_tap_phim'  => $request->ten_tap_phim,
                'slug_tap_phim' => $request->slug_tap_phim,
                'url'           => $request->url,
                'id_phim'       => $request->id_phim,
                'tinh_trang'    => $request->tinh_trang,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => ' thêm Tập Phim thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Tập Phim không thành công!!'
            ]);
        }
    }
    public function timTapPhim(Request $request)
    {
        $key    = '%' . $request->key . '%';
        $dataAdmin   = TapPhim::join('phims', 'id_phim', 'phims.id')
            ->orderBy('slug_tap_phim', 'ASC')
            ->select('tap_phims.*', 'phims.ten_phim')
            ->where('phims.ten_phim', 'like', $key)
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
            'tap_phim_admin'  =>  $response,
        ]);
    }
    public function capnhatTapPhim(Request $request)
    {
        try {
            $id_chuc_nang = 6;
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
            TapPhim::where('id', $request->id)
                ->update([
                    'ten_tap_phim'  => $request->ten_tap_phim,
                    'slug_tap_phim' => $request->slug_tap_phim,
                    'url'           => $request->url,
                    'id_phim'       => $request->id_phim,
                    'tinh_trang'    => $request->tinh_trang,
                ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật thành ' . $request->ten_the_loai,
            ]);
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật Thể Loại không thành công!!'
            ]);
        }
    }
    public function xoaTapPhim($id)
    {
        try {
            $id_chuc_nang = 6;
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
            TapPhim::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá Thể Loại thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Thể Loại không thành công!!'
            ]);
        }
    }
    public function thaydoiTrangThaiTapPhim(Request $request)
    {

        try {
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            TapPhim::where('id', $request->id)
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
    public function kiemTraSlugTapPhim(Request $request)
    {
        $tac_gia = TapPhim::where('slug_tap_phim', $request->slug)->first();

        if (!$tac_gia) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Tập Phim phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Tập Phim Đã Tồn Tại!',
            ]);
        }
    }
    public function kiemTraSlugTapPhimUpdate(Request $request)
    {
        $tap_phim = TapPhim::where('slug_tap_phim', $request->slug)
            ->where('id', '<>', $request->id)
            ->first();

        if (!$tap_phim) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tập Phim phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tập Phim Đã Tồn Tại!',
            ]);
        }
    }
    public function layTenPhim(Request $request)
    {
        $ten_phim =     Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.id', $request->id_phim)
            ->first();

        if ($ten_phim) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'lấy tên phim thành công!',
                'ten_phim'          => $ten_phim,
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'lấy tên phim không thành công!',
            ]);
        }
    }
    public function layTenPhimUpdate(Request $request)
    {
        $ten_phim =     Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.id', $request->id_phim)
            ->first();

        if ($ten_phim) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'lấy tên phim thành công!',
                'ten_phim'          => $ten_phim,
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'lấy tên phim không thành công!',
            ]);
        }
    }
}
