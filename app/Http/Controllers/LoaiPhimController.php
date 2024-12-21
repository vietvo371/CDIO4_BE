<?php

namespace App\Http\Controllers;

use App\Models\LoaiPhim;
use App\Models\PhanQuyen;
use App\Models\Phim;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class LoaiPhimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getData()
    {
        $id_chuc_nang = 8;
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
        $dataAdmin   = LoaiPhim::select('loai_phims.*')
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
            'loai_phim_admin'  =>  $response,
        ]);
    }
    public function getDataHome()
    {
        $data   = LoaiPhim::where('loai_phims.tinh_trang', 1)
            ->select('loai_phims.*')
            ->get(); // get là ra 1 danh sách
        return response()->json([
            'loai_phim'  =>  $data,
        ]);
    }
    public function getDataHomeLPhim(Request $request)
    {
        $loai_phim               = LoaiPhim::where('loai_phims.tinh_trang', 1)
            ->where('loai_phims.slug_loai_phim', $request->slug_lp)
            ->select('loai_phims.*')
            ->first();

        $phim                   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.tinh_trang', 1)
            ->where('loai_phims.slug_loai_phim', $request->slug_lp)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
            ->get();

        $phim_9_obj              = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.tinh_trang', 1)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
            ->inRandomOrder() // Lấy ngẫu nhiên
            ->take(9)
            ->get(); // get là ra 1 danh sách
        return response()->json([
            'loai_phim'    =>  $loai_phim,
            'phim'        =>  $phim,
            'phim_9_obj'  =>  $phim_9_obj,
        ]);
    }
    public function sapxepHome(Request $request)
    {
        $catagory = $request->catagory;
        $id_lp    = $request->id_lp;
        if ($catagory === 'az') {
            $data = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
                ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
                ->orderBy('ten_phim', 'ASC')  // tăng dần
                ->get();
        } else if ($catagory === 'za') {
            $data = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
                ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
                ->orderBy('ten_phim', 'DESC')  // giảm dần
                ->get();
        } else if ($catagory === '1to10') {
            $data = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
                ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
                ->where('id_loai_phim', $id_lp)
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
                ->orderBy('id', 'DESC')  // giảm dần
                ->skip(0)
                ->take(9)
                ->get();
        }
        return response()->json([
            'phim'  =>  $data,
        ]);
    }

    public function taoLoaiPhim(Request $request)
    {
        try {
            $id_chuc_nang = 8;
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
            LoaiPhim::create([
                'ten_loai_phim'          => $request->ten_loai_phim,
                'slug_loai_phim'         => $request->slug_loai_phim,
                'tinh_trang'             => $request->tinh_trang,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Bạn thêm Loại Phim thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Loại Phim không thành công!!'
            ]);
        }
    }
    public function timLoaiPhim(Request $request)
    {
        $key    = '%' . $request->key . '%';
        $dataAdmin   = LoaiPhim::select('loai_phims.*')
            ->where('ten_loai_phim', 'like', $key)
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
            'loai_phim_admin'  =>  $response,
        ]);
    }
    public function capnhatLoaiPhim(Request $request)
    {
        try {
            $id_chuc_nang = 8;
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
            LoaiPhim::where('id', $request->id)
                ->update([
                    'ten_loai_phim'          => $request->ten_loai_phim,
                    'slug_loai_phim'         => $request->slug_loai_phim,
                    'tinh_trang'             => $request->tinh_trang,
                ]);
            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật thành ' . $request->ten_loai_phim,
            ]);
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật  Loại Phim không thành công!!'
            ]);
        }
    }
    public function xoaLoaiPhim($id)
    {
        try {
            $id_chuc_nang = 8;
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
            LoaiPhim::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá Loai Phim thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá  Loai Phim không thành công!!'
            ]);
        }
    }
    public function thaydoiTrangThaiLoaiPhim(Request $request)
    {

        try {
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            LoaiPhim::where('id', $request->id)
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
    public function kiemTraSlugLoaiPhim(Request $request)
    {
        $tac_gia = LoaiPhim::where('slug_loai_phim', $request->slug)->first();

        if (!$tac_gia) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Loại Phim phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Loại Phim Đã Tồn Tại!',
            ]);
        }
    }
    public function kiemTraSlugLoaiPhimUpdate(Request $request)
    {
        $mon_an = LoaiPhim::where('slug_loai_phim', $request->slug)
            ->where('id', '<>', $request->id)
            ->first();

        if (!$mon_an) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Loại Phim phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Loại Phim Đã Tồn Tại!',
            ]);
        }
    }
}
