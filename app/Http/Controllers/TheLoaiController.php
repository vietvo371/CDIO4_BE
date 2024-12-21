<?php

namespace App\Http\Controllers;

use App\Models\PhanQuyen;
use App\Models\Phim;
use App\Models\TheLoai;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class TheLoaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getData()
    {
        $id_chuc_nang = 7;
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
        $dataAdmin       = TheLoai::select('the_loais.*')
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
            'the_loai'  =>  $response,
        ]);
    }
    public function getDataHomeTLPhim(Request $request)
    {
        $the_loai               = TheLoai::where('the_loais.tinh_trang', 1)
            ->where('the_loais.slug_the_loai', $request->slug_tl)
            ->select('the_loais.*')
            ->first();

        $phim                   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.tinh_trang', 1)
            ->where('the_loais.slug_the_loai', $request->slug_tl)
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
            'the_loai'    =>  $the_loai,
            'phim'        =>  $phim,
            'phim_9_obj'  =>  $phim_9_obj,
        ]);
    }
    public function sapxepHome(Request $request)
    {
        $catagory = $request->catagory;
        $id_tl    = $request->id_tl;
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
                ->where('id_the_loai', $id_tl)
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
    public function getDataHome()
    {
        $data   = TheLoai::where('the_loais.tinh_trang', 1)
            ->select('the_loais.*')
            ->get();
        $phims   = [];  // mảng chứa phim theo thể loại
        foreach ($data as $key  => $value) {
            $phim_theo_the_loai = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
                ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
                ->where('phims.tinh_trang', 1)
                ->where('phims.id_the_loai', $value->id)
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'tac_gias.ten_tac_gia')
                //    ->orderBy('id', 'DESC') sắp xép giảm dần
                ->inRandomOrder()
                ->take(3)
                ->get();
            $phims = array_merge($phims, $phim_theo_the_loai->toArray());
        }
        return response()->json([
            'the_loai'                  =>  $data,
            'phim_theo_the_loai'        =>  $phims,
        ]);
    }



    public function taoTheLoai(Request $request)
    {
        try {
            $id_chuc_nang = 7;
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
            TheLoai::create([
                'ten_the_loai'      => $request->ten_the_loai,
                'slug_the_loai'     => $request->slug_the_loai,
                'tinh_trang'        => $request->tinh_trang,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Bạn thêm Thể Thể Loại thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Thể Thể Loại không thành công!!'
            ]);
        }
    }
    public function timTheLoai(Request $request)
    {
        $key    = '%' . $request->key . '%';
        $dataAdmin   = TheLoai::select('the_loais.*')
            ->where('ten_the_loai', 'like', $key)
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
            'the_loai'  =>  $response,
        ]);
    }
    public function capnhatTheLoai(Request $request)
    {
        try {
            $id_chuc_nang = 7;
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
            TheLoai::where('id', $request->id)
                ->update([
                    'ten_the_loai'      => $request->ten_the_loai,
                    'slug_the_loai'     => $request->slug_the_loai,
                    'tinh_trang'        => $request->tinh_trang,
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
    public function xoaTheLoai($id)
    {
        try {
            $id_chuc_nang = 7;
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
            TheLoai::where('id', $id)->delete();

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
    public function thaydoiTrangThaiTheLoai(Request $request)
    {

        try {
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            TheLoai::where('id', $request->id)
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
    public function kiemTraSlugTheLoai(Request $request)
    {
        $tac_gia = TheLoai::where('slug_the_loai', $request->slug)->first();

        if (!$tac_gia) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Thể Loại phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Thể Loại Đã Tồn Tại!',
            ]);
        }
    }
    public function kiemTraSlugTheLoaiUpdate(Request $request)
    {
        $mon_an = TheLoai::where('slug_the_loai', $request->slug)
            ->where('id', '<>', $request->id)
            ->first();

        if (!$mon_an) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Thể Loại phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Thể Loại Đã Tồn Tại!',
            ]);
        }
    }
}
