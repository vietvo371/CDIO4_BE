<?php

namespace App\Http\Controllers;

use App\Models\DanhMucWeb;
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
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $dataDanhMuc       = DanhMucWeb::where('danh_muc_webs.tinh_trang', 1)
            ->select('danh_muc_webs.*')
            ->get(); // get là ra 1  sách
        $dataAdmin   = LoaiPhim::join('danh_muc_webs', 'loai_phims.id_danh_muc', 'danh_muc_webs.id')->select('loai_phims.*','danh_muc_webs.ten_danh_muc')
            ->paginate(9); // get là ra 1  sách
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
            'list_danh_muc'  =>  $dataDanhMuc,
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
    public function getDataHomeLPhim($slug)
    {
        $loai_phim               = LoaiPhim::where('loai_phims.tinh_trang', 1)
            ->where('loai_phims.slug_loai_phim', $slug)
            ->select('loai_phims.*')
            ->first();

        $phim                   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->where('phims.tinh_trang', 1)
            ->where('the_loais.tinh_trang', 1)
            ->where('loai_phims.tinh_trang', 1)
            ->where('loai_phims.slug_loai_phim', $slug)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim',)
            ->paginate(9); // get là ra 1  sách

        $response = [
            'pagination' => [
                'total' => $phim->total(),
                'per_page' => $phim->perPage(),
                'current_page' => $phim->currentPage(),
                'last_page' => $phim->lastPage(),
                'from' => $phim->firstItem(),
                'to' => $phim->lastItem()
            ],
            'dataPhim' => $phim
        ];
        $phim_9_obj              = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->where('phims.tinh_trang', 1)
            ->where('the_loais.tinh_trang', 1)
            ->where('loai_phims.tinh_trang', 1)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
            ->inRandomOrder() // Lấy ngẫu nhiên
            ->take(9)
            ->get(); // get là ra 1 danh sách
        return response()->json([
            'loai_phim'    =>  $loai_phim,
            'phim'        =>  $response,
            'phim_9_obj'  =>  $phim_9_obj,
        ]);
    }
    public function sapxepHome($id_lp, $catagory)
    {
        if ($catagory === 'az') {
            $data = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
                ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->where('id_loai_phim', $id_lp)
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
                ->orderBy('ten_phim', 'ASC')  // tăng dần
                ->paginate(9); // get là ra 1  sách

        } else if ($catagory === 'za') {
            $data = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
                ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->where('id_loai_phim', $id_lp)
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
                ->orderBy('ten_phim', 'DESC')  // giảm dần
                ->paginate(9); // get là ra 1  sách

        } else if ($catagory === '1to10') {
            $data = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
                ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->where('id_loai_phim', $id_lp)
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
                ->orderBy('id', 'DESC')  // giảm dần
                ->skip(0)
                ->take(9)
                ->paginate(9); // get là ra 1  sách
        }

        $response = [
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem()
            ],
            'dataPhim' => $data
        ];
        return response()->json([
            'phim'  =>  $response,
        ]);
    }

    public function taoLoaiPhim(Request $request)
    {
        try {
            $id_chuc_nang = 8;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            LoaiPhim::create([
                'ten_loai_phim'          => $request->ten_loai_phim,
                'slug_loai_phim'         => $request->slug_loai_phim,
                'tinh_trang'             => $request->tinh_trang,
                'id_danh_muc'            => $request->id_danh_muc,
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
        $id_chuc_nang = 8;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $key    = '%' . $request->key . '%';
        $dataAdmin   = LoaiPhim::select('loai_phims.*')
            ->where('ten_loai_phim', 'like', $key)
            ->paginate(9); // get là ra 1  sách
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
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
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
                    'id_danh_muc'            => $request->id_danh_muc,
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
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
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
            $id_chuc_nang = 8;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
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
        $loai_phim = LoaiPhim::where('slug_loai_phim', $request->slug)->first();

        if (!$loai_phim) {
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
