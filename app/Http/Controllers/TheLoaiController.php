<?php

namespace App\Http\Controllers;

use App\Models\DanhMucWeb;
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
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $dataAdmin       = TheLoai::join('danh_muc_webs', 'the_loais.id_danh_muc', 'danh_muc_webs.id')
            ->select('the_loais.*', 'danh_muc_webs.ten_danh_muc')
            ->paginate(6); // get là ra 1  sách

        $dataDanhMuc       = DanhMucWeb::where('danh_muc_webs.tinh_trang', 1)
            ->select('danh_muc_webs.*')
            ->get(); // get là ra 1  sách

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
            'list_danh_muc'  =>  $dataDanhMuc,
        ]);
    }
    public function getDataHomeTLPhim(Request $request)
    {
        $the_loai               = TheLoai::where('the_loais.tinh_trang', 1)
            ->where('the_loais.slug_the_loai', $request->slug)
            ->select('the_loais.*')
            ->first();

        $phim                   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->where('phims.tinh_trang', 1)
            ->where('the_loais.tinh_trang', 1)
            ->where('loai_phims.tinh_trang', 1)
            ->where('the_loais.slug_the_loai', $request->slug)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
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
            'the_loai'    =>  $the_loai,
            'phim'        =>  $response,
            'phim_9_obj'  =>  $phim_9_obj,
        ]);
    }
    public function sapxepHome($id_tl, $catagory)
    {
        if ($catagory === 'az') {
            $data = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
                ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->where('id_the_loai', $id_tl)
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
                ->orderBy('ten_phim', 'ASC')  // tăng dần
                ->paginate(9); // get là ra 1  sách

        } else if ($catagory === 'za') {
            $data = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
                ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->where('id_the_loai', $id_tl)
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
                ->orderBy('ten_phim', 'DESC')  // giảm dần
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
            'phim1'  =>  $data,
        ]);
    }
    public function getDataHome()
    {
        $data   = TheLoai::where('the_loais.tinh_trang', 1)
            ->select('the_loais.*')
            ->get();
        $phims   = [];  // mảng chứa phim theo thể loại
        foreach ($data as $key  => $value) {
            $phim_theo_the_loai = Phim::join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->where('phims.tinh_trang', 1)
                ->where('loai_phims.tinh_trang', 1)
                ->where('phims.id_the_loai', $value->id)
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
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
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            TheLoai::create([
                'ten_the_loai'      => $request->ten_the_loai,
                'slug_the_loai'     => $request->slug_the_loai,
                'id_danh_muc'        => $request->id_danh_muc,
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
        $id_chuc_nang = 7;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $key    = '%' . $request->key . '%';
        $dataAdmin       = TheLoai::join('danh_muc_webs', 'the_loais.id_danh_muc', 'danh_muc_webs.id')
            ->select('the_loais.*', 'danh_muc_webs.ten_danh_muc')
            ->where('ten_the_loai', 'like', $key)
            ->paginate(6); // get là ra 1  sách
        $dataDanhMuc       = DanhMucWeb::where('danh_muc_webs.tinh_trang', 1)
            ->select('danh_muc_webs.*')
            ->get(); // get là ra 1  sách
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
            'list_danh_muc'  =>  $dataDanhMuc,
        ]);
    }
    public function capnhatTheLoai(Request $request)
    {
        try {
            $id_chuc_nang = 7;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            TheLoai::where('id', $request->id)
                ->update([
                    'ten_the_loai'      => $request->ten_the_loai,
                    'slug_the_loai'     => $request->slug_the_loai,
                    'id_danh_muc'        => $request->id_danh_muc,
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
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
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
            $id_chuc_nang = 7;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
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
