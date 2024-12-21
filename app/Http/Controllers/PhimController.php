<?php

namespace App\Http\Controllers;

use App\Models\LoaiPhim;
use App\Models\PhanQuyen;
use App\Models\Phim;
use App\Models\TacGia;
use App\Models\TapPhim;
use App\Models\TheLoai;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class PhimController extends Controller
{

    public function getData()
    {
        $id_chuc_nang = 5;
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
        $the_loai_admin   = TheLoai::select('the_loais.*')
            ->get();
        $tac_gia_admin   = TacGia::select('tac_gias.*')
            ->get();
        $loai_phim_admin   = LoaiPhim::select('loai_phims.*')
            ->get();
        $dataAdmin   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->orderBy('id', 'DESC')
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
            ->paginate(8); // get là ra 1  sách

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
            'phim_admin'  =>  $response,
            'the_loai_admin'  =>  $the_loai_admin,
            'tac_gia_admin'  =>  $tac_gia_admin,
            'loai_phim_admin'  =>  $loai_phim_admin,
        ]);
    }
    public function getDataXemPhim(Request $request)
    {
        $data   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.slug_phim', $request->slug)
            ->where('phims.tinh_trang', 1)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
            ->first(); // get là ra 1 danh sách

        return response()->json([
            'phim' => $data,
        ]);
    }
    public function dataTheoTL(Request $request)
    {
        $id_tl    = $request->id_tl;
        $data = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('id_the_loai', $id_tl)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
            ->get();
        return response()->json([
            'phim_theo_tl'  =>  $data,
        ]);
    }
    public function getDataHome()
    {
        $data   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.tinh_trang', 1)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
            ->get();
        // get là ra 1 danh sách
        // phaan trang php
        // ->paginate(9);
        // return response()->json([
        //     'data' => $data
        // ]);
        $data9   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.tinh_trang', 1)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
            ->inRandomOrder() // Lấy ngẫu nhiên
            ->take(9)
            ->get(); // get là ra 1 danh sách
        $data2   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.tinh_trang', 1)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
            ->take(2)
            ->get(); // get là ra 1 danh sách
        $data3   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.tinh_trang', 1)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
            ->orderBy('id', 'DESC') // sắp xếp giảm dần
            ->take(3)
            ->get(); // get là ra 1 danh sách

        return response()->json([
            'phim'             =>  $data,
            'phim_9_obj'       =>  $data9,
            'phim_2_obj'       =>  $data2,
            'phim_3_obj'       =>  $data3,
        ]);
    }
    public function getDataDelist(Request $request)
    {
        $phim                   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.tinh_trang', 1)
            ->where('phims.slug_phim', $request->slug)
            ->select('phims.*', 'the_loais.ten_the_loai', 'the_loais.id as id_tl', 'the_loais.slug_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
            ->first();
        $data5   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.tinh_trang', 1)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
            ->inRandomOrder() // Lấy ngẫu nhiên
            ->take(5)
            ->get(); // get là ra 1 danh sách
        return response()->json([
            'phim'        =>  $phim,
            'phim_5_obj'  =>  $data5,
        ]);
    }
    public function sapxepHome(Request $request)
    {
        $catagory = $request->catagory;
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
        }
        return response()->json([
            'phim'  =>  $data,
        ]);
    }

    public function taoPhim(Request $request)
    {
        try {
            $id_chuc_nang = 5;
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
            Phim::create([
                'ten_phim'                  => $request->ten_phim,
                'hinh_anh'                  => $request->hinh_anh,
                'mo_ta'                     => $request->mo_ta,
                'slug_phim'                 => $request->slug_phim,
                'id_loai_phim'              => $request->id_loai_phim,
                'id_the_loai'               => $request->id_the_loai,
                'id_tac_gia'                => $request->id_tac_gia,
                'so_tap_phim'               => $request->so_tap_phim,
                'tinh_trang'                => $request->tinh_trang,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Bạn thêm Phim thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Phim không thành công!!'
            ]);
        }
    }

    public function timPhim(Request $request)
    {
        $the_loai_admin   = TheLoai::select('the_loais.*')
            ->get();
        $tac_gia_admin   = TacGia::select('tac_gias.*')
            ->get();
        $loai_phim_admin   = LoaiPhim::select('loai_phims.*')
            ->get();
        $key    = '%' . $request->key . '%';
        $dataAdmin   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->orderBy('id', 'DESC')
            ->where('ten_phim', 'like', $key)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
            ->paginate(8); // get là ra 1  sách

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
            'phim_admin'  =>  $response,
            'the_loai_admin'  =>  $the_loai_admin,
            'tac_gia_admin'  =>  $tac_gia_admin,
            'loai_phim_admin'  =>  $loai_phim_admin,
        ]);
    }
    public function xoaPhim($id)
    {
        try {
            $id_chuc_nang = 5;
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
            Phim::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá Phim thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Phim không thành công!!'
            ]);
        }
    }

    public function capnhatPhim(Request $request)
    {
        try {
            $id_chuc_nang = 5;
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
            Phim::where('id', $request->id)
                ->update([
                    'ten_phim'                  => $request->ten_phim,
                    'hinh_anh'                  => $request->hinh_anh,
                    'mo_ta'                     => $request->mo_ta,
                    'slug_phim'                 => $request->slug_phim,
                    'id_loai_phim'              => $request->id_loai_phim,
                    'id_the_loai'               => $request->id_the_loai,
                    'id_tac_gia'                => $request->id_tac_gia,
                    'so_tap_phim'               => $request->so_tap_phim,
                    'tinh_trang'                => $request->tinh_trang,
                ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật thành ' . $request->ten_phim,
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật Phim không thành công!!'
            ]);
        }
    }

    public function thaydoiTrangThaiPhim(Request $request)
    {

        try {
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            Phim::where('id', $request->id)
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
    public function timPhimHome(Request $request)
    {
        $key    = '%' . $request->key . '%';
        if ($request->key == "") {
            $data = [];
        } else {
            $data   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
                ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
                ->where('ten_phim', 'like', $key)
                ->get(); // get là ra 1 danh sách
        }
        return response()->json([
            'phim'  =>  $data,
        ]);
    }
    public function kiemTraSlugPhim(Request $request)
    {
        $phim = Phim::where('slug_phim', $request->slug)->first();

        if (!$phim) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Phim phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Phim Đã Tồn Tại!',
            ]);
        }
    }
    public function kiemTraSlugPhimUpdate(Request $request)
    {
        $mon_an = Phim::where('slug_phim', $request->slug)
            ->where('id', '<>', $request->id)
            ->first();

        if (!$mon_an) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Phim phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Phim Đã Tồn Tại!',
            ]);
        }
    }
}
